# Docker deployment on NEXUS

## Preconditions

- Docker Engine and Docker Compose v2 are installed on NEXUS.
- The existing Cloudflare Tunnel remains managed separately and can reach
  `127.0.0.1:8080`.
- A private checkout of this repository exists on the intended deployment
  branch.
- A safe database and media backup has been made before moving production data.

## First deployment

```bash
cd ~/projects/personals/myblog
git status --short --branch
cp infra/compose/.env.example infra/compose/.env
chmod 600 infra/compose/.env
editor infra/compose/.env
```

Set unique passwords and an `APP_KEY`; use `APP_ENV=production`,
`APP_DEBUG=false`, and `APP_URL=https://api.asepblog.online`. Keep
`ALLOW_EMPTY_DATABASE=false` when data already exists.

For a new empty installation only:

```bash
# after explicitly setting ALLOW_EMPTY_DATABASE=true
./infra/scripts/deploy.sh
# Review first; migration is deliberately manual.
docker compose --env-file infra/compose/.env -f infra/compose/docker-compose.yml exec backend php artisan migrate --force
```

For an existing production dataset:

1. Make and verify a backup from the old environment as described in
   [backup-recovery.md](backup-recovery.md).
2. Start only MySQL to establish the target volume and database users:

   ```bash
   docker compose --env-file infra/compose/.env -f infra/compose/docker-compose.yml up -d mysql
   ```

3. Run the explicit restore, which asks for a destructive confirmation:

   ```bash
   ./infra/scripts/restore.sh /secure/backups/myblog-YYYYMMDDTHHMMSSZ
   ```

4. Run the normal deployment and then verify the health endpoint:

   ```bash
   ./infra/scripts/deploy.sh
   ./infra/scripts/health-check.sh
   ```

`deploy.sh` will not create an empty database accidentally, run migrations,
seed data, delete volumes, change Cloudflare, or reset Git state.

## Subsequent application updates

```bash
cd ~/projects/personals/myblog
./infra/scripts/backup.sh /secure/backups
git pull --ff-only
./infra/scripts/deploy.sh
```

Review and execute migrations separately, after a backup. The deployment script
requires a clean Git working tree and the `DEPLOY_BRANCH` selected in the
private Compose environment file.

## Media migration

Laravel currently stores public media in `backend/storage/app/public` (posts,
profiles, and projects). Copy it into the persistent volume before cutover;
the safest portable method is to include it in the backup archive and use
`restore.sh`, which restores both the matching SQL dump and media archive.

If a controlled one-time media-only import is necessary, first archive the
current source and target, stop Nginx/backend, and extract only into
`/var/www/html/storage/app/public` through a temporary `backend` Compose run.
Do not copy files into an image layer or remove the Docker volume.

## Cloudflare Tunnel handoff

Apply the three hostname-to-`http://127.0.0.1:8080` mappings from
[network architecture](../architecture/network-architecture.md) in the
separately managed tunnel configuration. Confirm DNS and tunnel status with
the existing NEXUS operational procedure; this repository makes no tunnel or
DNS changes.

## Troubleshooting

```bash
docker compose --env-file infra/compose/.env -f infra/compose/docker-compose.yml ps
docker compose --env-file infra/compose/.env -f infra/compose/docker-compose.yml logs --tail=200 nginx backend mysql
docker inspect --format '{{json .State.Health}}' myblog-nginx
curl --resolve api.asepblog.online:8080:127.0.0.1 http://api.asepblog.online:8080/up
```

If Laravel returns unexpected generated URLs, verify `APP_URL` and the tunnel
boundary. If Filament assets fail, confirm the repository checkout's
`backend/public` directory remains readable to Docker and that the Nginx
container is running the version from this Compose file.
