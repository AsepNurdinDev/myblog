# MyBlog system architecture

MyBlog is a decoupled application with Laravel as the authoritative data and
administration system. The public Next.js application consumes Laravel only by
HTTP APIs; it does not connect to MySQL or share Laravel's filesystem.

```text
Browser → Cloudflare → existing cloudflared on NEXUS → 127.0.0.1:8080 Nginx
                                                        ├─ asepblog.online → Next.js (optional)
                                                        ├─ api.asepblog.online → Laravel PHP-FPM
                                                        └─ admin.asepblog.online → Laravel /admin
                                                                                  │
                                               Laravel storage volume ←───────────┤
                                               MySQL named volume   ←── MySQL ────┘
```

The default stack omits the frontend because the existing `frontend/` app is a
Next.js starter rather than the finished public site. Its service is isolated
behind the Compose `frontend` profile. This avoids turning an unfinished UI
into a deployment dependency while preserving the intended domain routing.

Laravel serves API requests and the existing Filament/Blade functionality.
`admin.asepblog.online/` redirects to the confirmed Filament path `/admin/`.
The API hostname intentionally exposes only `/api/*`, `/storage/*`, and `/up`;
old Blade public routes remain available internally through Laravel but are not
used as the new public edge.

At the time this infrastructure was added, `backend/bootstrap/app.php` does
not yet register `routes/api.php` in Laravel's routing bootstrap. Consequently
the proxy is ready for the API but `/api/*` will return Laravel 404 responses
until the parallel backend work enables that route file. This deployment work
does not alter that application behavior.
