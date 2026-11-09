#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/lib.sh"

require_command docker
require_command gzip
require_command tar
require_command sha256sum
require_env_file
require_safe_database_name

backup_root="${1:-$repo_root/backups}"
mkdir -p "$backup_root"
[[ -d "$backup_root" ]] || die "Backup directory is not usable: $backup_root"

timestamp="$(date -u +%Y%m%dT%H%M%SZ)"
backup_name="myblog-$timestamp"
destination="$backup_root/$backup_name"
[[ ! -e "$destination" ]] || die "Backup destination already exists: $destination"

temporary="$(mktemp -d "$backup_root/.myblog-backup.XXXXXX")"
cleanup() { rm -rf "$temporary"; }
trap cleanup EXIT

info "Creating database dump in $backup_name (secrets are not logged)."
compose exec -T mysql sh -ec 'MYSQL_PWD="$MYSQL_PASSWORD" exec mysqldump --single-transaction --routines --events --triggers --no-tablespaces -u"$MYSQL_USER" "$MYSQL_DATABASE"' \
    | gzip -9 > "$temporary/database.sql.gz"

info 'Creating archive of Laravel public uploads.'
compose exec -T backend tar -C /var/www/html/storage/app/public -czf - . > "$temporary/media.tar.gz"

gzip -t "$temporary/database.sql.gz"
tar -tzf "$temporary/media.tar.gz" >/dev/null
(
    cd "$temporary"
    sha256sum database.sql.gz media.tar.gz > SHA256SUMS
    printf 'created_at_utc=%s\n' "$timestamp" > manifest.txt
    printf 'database=%s\n' "$(env_value MYSQL_DATABASE)" >> manifest.txt
    printf 'media_source=storage/app/public\n' >> manifest.txt
)

mv "$temporary" "$destination"
trap - EXIT
info "Backup created: $destination"
