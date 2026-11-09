# Monitoring integration on NEXUS

MyBlog adds no Prometheus, Grafana, exporter, or public monitoring port. The
existing NEXUS cAdvisor should discover the `myblog-*` containers; Node Exporter
continues to report host CPU, memory, disk, filesystem, and network pressure.

Recommended Grafana views and alerts:

- Container availability/restarts for `myblog-nginx`, `myblog-backend`, and
  `myblog-mysql` from cAdvisor.
- Container CPU, memory working set, filesystem usage, and network receive/
  transmit rates.
- NEXUS disk free space for Docker's volume filesystem; MySQL and upload
  persistence are Docker named volumes.
- HTTP health via an existing blackbox probe to
  `http://127.0.0.1:8080/up` with `Host: api.asepblog.online`, if the existing
  monitoring stack already has a suitable probe mechanism.
- Backup freshness: alert if no new `myblog-*` backup directory appears within
  the agreed recovery-point objective.

MySQL exporter is optional. Add it only after an operator decides that
database-level metrics justify another credentialed service; it must be scoped
to a least-privilege monitoring user and added to the existing Prometheus
configuration, never exposed publicly.

Operational logs remain available through Docker:

```bash
docker compose --env-file infra/compose/.env -f infra/compose/docker-compose.yml logs --since=1h nginx backend mysql
docker inspect --format '{{json .State.Health}}' myblog-backend
```
