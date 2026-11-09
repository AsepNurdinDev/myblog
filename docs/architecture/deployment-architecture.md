# MyBlog deployment architecture

The stack has three default services:

| Service | Responsibility | Persistence / exposure |
| --- | --- | --- |
| `myblog-mysql` | MySQL 8.4 data service | `myblog_mysql_data`; internal only |
| `myblog-backend` | Laravel PHP-FPM, REST API, Filament | `myblog_laravel_storage`; internal only |
| `myblog-nginx` | Host routing, static public files, FastCGI proxy | loopback port `8080` only |

`myblog-frontend` is an optional Next.js 16 service enabled with the
`frontend` profile. Nginx uses Docker DNS at request time so its absence cannot
prevent Nginx from starting.

`myblog_laravel_storage` mounts at Laravel's whole `storage/` directory. This
preserves `storage/app/public`, private Livewire temporary files, logs, and
framework cache across container replacement. Nginx mounts it read-only and
maps `/storage/` directly to `storage/app/public`; no writable storage path is
served by Nginx.

Database schema changes are deliberately outside deployment automation. An
operator must first back up, review `php artisan migrate --force --pretend`,
then run `docker compose ... exec backend php artisan migrate --force` during a
chosen maintenance window. Never use `migrate:fresh`, seeders, or volume
deletion against production data.
