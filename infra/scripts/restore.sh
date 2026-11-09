#!/usr/bin/env bash
set -Eeuo pipefail

source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/lib.sh"

usage() {
    printf 'Usage: %s <backup-directory> [--yes]\n' "$0" >&2
    exit 2
}

[[ $# -ge 1 && $# -le 2 ]] || usage
backup_dir="$1"
assume_yes="${2:-}"
[[ -d "$backup_dir" ]] || die "Backup directory does not exist: $backup_dir"
[[ -f "$backup_dir/database.sql.gz" ]] || die 'database.sql.gz is missing from backup directory.'
[[ -f "$backup_dir/media.tar.gz" ]] || die 'media.tar.gz is missing from backup directory.'

require_command docker
require_command gzip
require_command tar
require_command sha256sum
require_env_file
require_safe_database_name

if [[ -f "$backup_dir/SHA256SUMS" ]]; then
    (cd "$backup_dir" && sha256sum -c SHA256SUMS)
else
    die 'SHA256SUMS is missing; refusing to restore an unverifiable backup.'
fi
gzip -t "$backup_dir/database.sql.gz"
tar -tzf "$backup_dir/media.tar.gz" >/dev/null

database="$(env_value MYSQL_DATABASE)"
backup_label="$(basename "$backup_dir")"
if [[ "$assume_yes" != --yes ]]; then
    printf 'This permanently replaces database %q and storage/app/public with backup %q.\n' "$database" "$backup_label"
    read -r -p "Type RESTORE $backup_label to continue: " confirmation
    [[ "$confirmation" == "RESTORE $backup_label" ]] || die 'Restore cancelled.'
fi

info 'Stopping HTTP services; MySQL remains running for import.'
compose stop nginx backend

info 'Replacing the application database from the verified dump.'
compose exec -T mysql sh -ec 'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" exec mysql -uroot -e "$1"' sh \
    "DROP DATABASE IF EXISTS \`$database\`; CREATE DATABASE \`$database\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; GRANT ALL PRIVILEGES ON \`$database\`.* TO \`$MYSQL_USER\`@'%'; FLUSH PRIVILEGES;"
gzip -dc "$backup_dir/database.sql.gz" \
    | compose exec -T mysql sh -ec 'MYSQL_PWD="$MYSQL_PASSWORD" exec mysql -u"$MYSQL_USER" "$MYSQL_DATABASE"'

info 'Replacing public uploads from the verified media archive.'
compose run --rm --no-deps --entrypoint sh backend -ec \
    'mkdir -p /var/www/html/storage/app/public && find /var/www/html/storage/app/public -mindepth 1 -maxdepth 1 -exec rm -rf -- {} + && tar -C /var/www/html/storage/app/public -xzf -' \
    < "$backup_dir/media.tar.gz"

info 'Starting normal services after explicit restore.'
compose up -d
"$script_dir/health-check.sh"
info 'Restore completed. Run migrations manually only after reviewing the migration plan.'
