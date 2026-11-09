#!/usr/bin/env bash
set -Eeuo pipefail

source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/lib.sh"

require_command docker
require_command git
require_env_file

if ! docker info >/dev/null 2>&1; then
    die 'Docker daemon is unavailable to this user.'
fi

compose config --quiet

for key in APP_KEY MYSQL_DATABASE MYSQL_USER MYSQL_PASSWORD MYSQL_ROOT_PASSWORD; do
    value="$(env_value "$key")"
    [[ -n "$value" && "$value" != *CHANGE_ME* ]] || die "$key is missing or still uses the example placeholder."
done

[[ "$(env_value APP_ENV production)" == "production" ]] || die 'APP_ENV must be production for this deployment script.'
[[ "$(env_value APP_DEBUG false)" == "false" ]] || die 'APP_DEBUG must be false for this deployment script.'
require_safe_database_name

expected_branch="$(env_value DEPLOY_BRANCH)"
current_branch="$(git -C "$repo_root" branch --show-current)"
[[ -n "$current_branch" ]] || die 'Deployment requires a checked-out branch, not detached HEAD.'
[[ -z "$expected_branch" || "$current_branch" == "$expected_branch" ]] || die "Checked-out branch is '$current_branch'; expected '$expected_branch'."

if [[ -n "$(git -C "$repo_root" status --porcelain)" ]]; then
    die 'Git working tree is not clean. Commit, stash, or explicitly resolve changes before deployment.'
fi

# Compose names this volume explicitly, making the guard stable and easy to
# inspect. A missing volume means MySQL would initialise an empty database.
if ! docker volume inspect myblog_mysql_data >/dev/null 2>&1; then
    [[ "$(env_value ALLOW_EMPTY_DATABASE false)" == "true" ]] || die \
        'myblog_mysql_data does not exist. Refusing to initialise an empty database. Start only mysql, restore a verified backup, then rerun; or explicitly set ALLOW_EMPTY_DATABASE=true for a new installation.'
fi

profile_args=()
if [[ "$(env_value FRONTEND_ENABLED false)" == "true" ]]; then
    profile_args+=(--profile frontend)
fi

info 'Building application images from the current locked dependencies.'
compose "${profile_args[@]}" build backend
if [[ ${#profile_args[@]} -gt 0 ]]; then
    compose "${profile_args[@]}" build frontend
fi

info 'Starting services without migrations, seeders, volume deletion, or database reset.'
compose "${profile_args[@]}" up -d

info 'Waiting for service health checks (up to 120 seconds).'
deadline=$((SECONDS + 120))
while (( SECONDS < deadline )); do
    mysql_health="$(docker inspect --format '{{if .State.Health}}{{.State.Health.Status}}{{else}}none{{end}}' myblog-mysql 2>/dev/null || true)"
    backend_health="$(docker inspect --format '{{if .State.Health}}{{.State.Health.Status}}{{else}}none{{end}}' myblog-backend 2>/dev/null || true)"
    nginx_health="$(docker inspect --format '{{if .State.Health}}{{.State.Health.Status}}{{else}}none{{end}}' myblog-nginx 2>/dev/null || true)"
    if [[ "$mysql_health" == healthy && "$backend_health" == healthy && "$nginx_health" == healthy ]]; then
        "$script_dir/health-check.sh"
        info 'Deployment health checks completed.'
        exit 0
    fi
    sleep 5
done

compose ps
die 'Timed out waiting for healthy services. Review docker compose logs before retrying.'
