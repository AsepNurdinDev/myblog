# Security hardening

## Implemented controls

- MySQL and PHP-FPM are isolated on an internal Docker network with no host
  ports. Nginx alone has a loopback-bound HTTP listener.
- Production secrets stay in `infra/compose/.env`, which is ignored by Git and
  Docker builds. Restrict it with `chmod 600` and use a unique `APP_KEY`.
- The backend production image uses locked Composer dependencies and excludes
  development packages and local environment files. Its FPM process runs as
  `www-data` after the entrypoint prepares only writable Laravel paths.
- Nginx mounts Laravel storage read-only, blocks dotfiles and source metadata,
  limits upload bodies to 25 MiB, emits baseline browser security headers, and
  does not cache dynamic FastCGI responses.
- Nginx runs read-only with a temporary writable runtime filesystem, dropped
  capabilities except the few it needs at startup, and no-new-privileges.
- Health checks cover MySQL, PHP-FPM, and Laravel's `/up` endpoint through
  Nginx. Backups use restrictive file permissions and integrity hashes.

## Required operator controls

- Keep `APP_DEBUG=false`, `APP_ENV=production`, and `APP_URL` set to the API
  hostname. Do not rotate `APP_KEY` casually: encrypted Laravel data and
  sessions depend on it.
- Restrict SSH and Docker-group membership on NEXUS. Docker daemon access is
  effectively root-equivalent and can read container environment variables.
- Keep the Nginx port loopback-only. Publishing it externally weakens the
  Cloudflare Tunnel trust boundary and enables header spoofing attempts.
- Use Cloudflare access controls, WAF/rate limiting, and DDoS protections for
  public endpoints. Apply application-level Laravel rate limits to comment,
  authentication, and future write API routes; the present proxy intentionally
  does not guess route-specific limits while the API is evolving.
- Review CORS in `backend/config/cors.php` before adding frontend origins.
  Current configuration allows `https://asepblog.online` and local development.
- Patch Docker base images and dependencies on an operating schedule. Rebuild
  and test on a non-production host before updating NEXUS.

## Trusted proxy boundary

Cloudflare terminates public TLS; cloudflared is expected to send traffic only
to Nginx's loopback-published port. Nginx accepts `CF-Connecting-IP` only from
the fixed Compose bridge gateway and overwrites all forwarded headers sent to
Laravel. If the tunnel topology or `myblog_internal` subnet changes, review
this boundary before deployment. See
[network architecture](../architecture/network-architecture.md).

Do not expose Grafana, Prometheus, cAdvisor, or MySQL through Cloudflare as a
side effect of this application deployment.
