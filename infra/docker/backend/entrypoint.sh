#!/bin/sh
set -eu

app_root=/var/www/html
storage_dir="$app_root/storage"

# A new named volume is empty. Recreate Laravel's writable structure without
# running migrations, seeders, cache clears, or any database-changing command.
mkdir -p \
    "$storage_dir/app/public" \
    "$storage_dir/app/private" \
    "$storage_dir/framework/cache/data" \
    "$storage_dir/framework/sessions" \
    "$storage_dir/framework/views" \
    "$storage_dir/logs" \
    "$app_root/bootstrap/cache"

if [ ! -L "$app_root/public/storage" ]; then
    rm -rf "$app_root/public/storage"
    ln -s "$storage_dir/app/public" "$app_root/public/storage"
fi

chown -R www-data:www-data "$storage_dir" "$app_root/bootstrap/cache"

# Package discovery only creates framework cache metadata. It never migrates,
# seeds, resets, or otherwise modifies the database.
su-exec www-data php "$app_root/artisan" package:discover --ansi --no-interaction

exec su-exec www-data "$@"
