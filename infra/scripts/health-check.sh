#!/usr/bin/env bash
set -Eeuo pipefail

source "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/lib.sh"

require_command curl
require_command docker
require_env_file

bind_ip="$(env_value NGINX_BIND_IP 127.0.0.1)"
port="$(env_value NGINX_HTTP_PORT 8080)"
[[ "$bind_ip" == "127.0.0.1" || "$bind_ip" == "::1" || "$bind_ip" == "localhost" ]] || die 'health-check.sh only supports a loopback Nginx listener.'

for container in myblog-mysql myblog-backend myblog-nginx; do
    health="$(docker inspect --format '{{if .State.Health}}{{.State.Health.Status}}{{else}}none{{end}}' "$container" 2>/dev/null || true)"
    [[ "$health" == healthy ]] || die "$container is not healthy (status: ${health:-missing})."
done

curl --fail --silent --show-error \
    --resolve "api.asepblog.online:${port}:${bind_ip}" \
    "http://api.asepblog.online:${port}/up" >/dev/null

printf 'INFO: MyBlog API health endpoint responded successfully through Nginx.\n'
