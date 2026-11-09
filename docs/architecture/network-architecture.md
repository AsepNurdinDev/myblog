# MyBlog network architecture

All Compose services use the `myblog_internal` Docker bridge (`172.28.0.0/24`)
with `internal: true`. MySQL and PHP-FPM have no published host ports. Nginx is
the sole HTTP entry and maps only `${NGINX_BIND_IP:-127.0.0.1}:8080`.

Cloudflare Tunnel remains outside this repository and must point at
`http://127.0.0.1:8080` on NEXUS. Its ingress rules should preserve the HTTP
Host header:

```yaml
ingress:
  - hostname: asepblog.online
    service: http://127.0.0.1:8080
  - hostname: api.asepblog.online
    service: http://127.0.0.1:8080
  - hostname: admin.asepblog.online
    service: http://127.0.0.1:8080
  - service: http_status:404
```

This is an operator configuration example only; it does not apply DNS or alter
the existing tunnel. Do not add MySQL, PHP-FPM, cAdvisor, Prometheus, or
Grafana ingress rules.

TLS terminates at Cloudflare. Nginx receives plaintext only from the local
tunnel and tells Laravel the external scheme is HTTPS. It accepts
`CF-Connecting-IP` only from the fixed Docker bridge gateway created for the
loopback-published service, then overwrites forwarded headers before PHP-FPM.
This prevents remote clients from directly choosing Laravel's trusted proxy
headers. The host remains a trust boundary: any privileged local NEXUS process
that can connect to the loopback port can impersonate the tunnel, so restrict
host access and do not publish the port publicly.

Laravel currently uses `$middleware->trustProxies(at: '*')` in
`backend/bootstrap/app.php`. No application source was changed because the
Nginx configuration sanitizes forwarded headers at the only deployment edge.
If another proxy route is added, replace that wildcard with the exact Nginx
bridge address/range supported by the deployed Laravel version and test URL,
secure-cookie, and client-IP behavior before rollout.
