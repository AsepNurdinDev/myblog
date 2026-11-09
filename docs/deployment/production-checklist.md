# Production checklist

## Before first cutover

- [ ] Confirm the checkout is the reviewed deployment branch and is clean.
- [ ] Create `infra/compose/.env` with production values, a stable `APP_KEY`,
  unique database passwords, `APP_DEBUG=false`, and mode `0600`.
- [ ] Create and checksum-verify an SQL plus media backup of the old system.
- [ ] Decide explicitly: new empty MySQL volume, or restore the existing data.
- [ ] Confirm the NEXUS tunnel has the three hostname ingress mappings to
  `http://127.0.0.1:8080`; do not expose internal services.
- [ ] Confirm NEXUS Docker storage has capacity for MySQL, media, and backups.
- [ ] Review planned migrations with `php artisan migrate --force --pretend`.

## Deploy

- [ ] For existing data, restore the verified backup before normal deployment.
- [ ] Run `./infra/scripts/deploy.sh`.
- [ ] Run `./infra/scripts/health-check.sh`.
- [ ] Verify `https://api.asepblog.online/up` and a real API endpoint.
- [ ] Verify a known image at `https://api.asepblog.online/storage/...`.
- [ ] Verify `https://admin.asepblog.online/` redirects to `/admin/` and login
  works.
- [ ] Confirm `https://asepblog.online/` returns the real Next.js app only
  after `FRONTEND_ENABLED=true`; before then its 503 is expected.
- [ ] If a reviewed migration is needed, run it manually in the approved
  maintenance window and retest.

## Ongoing operations

- [ ] Run and retain verified backups; perform restore drills.
- [ ] Monitor cAdvisor container restarts/resources and Node Exporter host disk
  space using the existing Grafana/Prometheus stack.
- [ ] Review Docker and Laravel logs after deployments.
- [ ] Keep Cloudflare, Docker images, Composer dependencies, and NEXUS patched.
- [ ] Reassess CORS, rate limits, and trusted proxies when the API/frontend
  authentication model changes.
