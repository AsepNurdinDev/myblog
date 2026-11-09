#!/usr/bin/env bash
set -Eeuo pipefail

script_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
repo_root="$(cd "$script_dir/../.." && pwd)"
compose_file="$repo_root/infra/compose/docker-compose.yml"
env_file="${MYBLOG_ENV_FILE:-$repo_root/infra/compose/.env}"

die() {
    printf 'ERROR: %s\n' "$*" >&2
    exit 1
}

info() {
    printf 'INFO: %s\n' "$*"
}

require_command() {
    command -v "$1" >/dev/null 2>&1 || die "Required command not found: $1"
}

compose() {
    docker compose --env-file "$env_file" -f "$compose_file" "$@"
}

require_env_file() {
    [[ -f "$env_file" ]] || die "Environment file not found: $env_file (copy infra/compose/.env.example)"
}

env_value() {
    local key="$1"
    local default_value="${2:-}"
    local value
    value="$(sed -n -E "s/^${key}=(.*)$/\\1/p" "$env_file" | tail -n 1)"
    printf '%s' "${value:-$default_value}"
}

require_safe_database_name() {
    local database
    database="$(env_value MYSQL_DATABASE)"
    [[ "$database" =~ ^[A-Za-z0-9_]+$ ]] || die 'MYSQL_DATABASE must contain only letters, numbers, and underscores.'
}
