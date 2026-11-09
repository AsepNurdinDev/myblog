# MyBlog production Compose stack

Run every command from the repository root. The stack is deliberately
loopback-only: Nginx publishes `127.0.0.1:8080` by default, so the existing
NEXUS Cloudflare Tunnel is the only intended external ingress.

```bash
cp infra/compose/.env.example infra/compose/.env
chmod 600 infra/compose/.env
```

Fill every `CHANGE_ME` value. Generate an application key on a trusted host
without writing an application `.env` into the repository:

```bash
docker run --rm php:8.5-cli php -r 'echo "base64:".base64_encode(random_bytes(32)).PHP_EOL;'
```

Set `APP_URL=https://api.asepblog.online`; Laravel's `public` filesystem disk
will then generate media URLs at `https://api.asepblog.online/storage/...`.

## Database safety gate

`deploy.sh` refuses to create `myblog_mysql_data` unless
`ALLOW_EMPTY_DATABASE=true`. Use one of these explicit paths:

1. New installation: set `ALLOW_EMPTY_DATABASE=true`, run `deploy.sh`, then
   run reviewed migrations manually.
2. Existing installation: keep it `false`, start only the database to create
   an empty target, restore a verified backup, then deploy the app:

```bash
docker compose --env-file infra/compose/.env -f infra/compose/docker-compose.yml up -d mysql
./infra/scripts/restore.sh /secure/backups/myblog-YYYYMMDDTHHMMSSZ
./infra/scripts/deploy.sh
```

The MySQL initialization variables are used only for a brand-new volume. They
never overwrite an existing database volume.

## Frontend profile

The checked-in Next.js project is still a generated starter, so `frontend` is
not part of the default stack. Nginx returns HTTP 503 for the public hostname
until the real frontend is ready. After implementation and a production build
review, set `FRONTEND_ENABLED=true` and run `./infra/scripts/deploy.sh`.

Useful inspection commands:

```bash
docker compose --env-file infra/compose/.env -f infra/compose/docker-compose.yml ps
docker compose --env-file infra/compose/.env -f infra/compose/docker-compose.yml logs -f nginx backend mysql
./infra/scripts/health-check.sh
```
