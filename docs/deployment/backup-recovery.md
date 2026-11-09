# Backup and recovery

## Backup

Create backups from NEXUS to a protected local filesystem, then copy them to
an approved remote destination outside this repository using the operator's
existing secure process. No automatic upload destination is configured.

```bash
cd ~/projects/personals/myblog
./infra/scripts/backup.sh /secure/backups
```

Each backup gets a UTC timestamped, non-overwriting directory containing:

- `database.sql.gz`: a transaction-consistent MySQL logical dump;
- `media.tar.gz`: Laravel `storage/app/public` uploads;
- `SHA256SUMS`: integrity checks for both archives;
- `manifest.txt`: timestamp, database name, and media source.

The script sets restrictive file creation permissions, validates both archives,
and never prints database passwords. Test the backup workflow and perform a
periodic restore drill on a non-production host.

Suggested daily cron entry after confirming paths and Docker permissions:

```cron
15 2 * * * cd /home/asepnrdn/projects/personals/myblog && ./infra/scripts/backup.sh /secure/backups >> /var/log/myblog-backup.log 2>&1
```

Keep the backup root readable only by administrators and use an encrypted,
off-host retention copy. Monitor its freshness and available capacity.

## Restore

Restore is intentionally destructive: it stops Nginx/backend, drops and
recreates the configured application database, replaces public uploads, then
starts the normal services. It refuses missing or checksum-invalid archives.

```bash
cd ~/projects/personals/myblog
./infra/scripts/restore.sh /secure/backups/myblog-YYYYMMDDTHHMMSSZ
```

The script requires the exact typed confirmation `RESTORE <backup-directory>`.
For an already-reviewed, noninteractive disaster-recovery run, append `--yes`;
that flag is an explicit acknowledgement that the command replaces data.

Recovery sequence:

1. Stop publishing or put the public frontend into maintenance mode externally.
2. Select the intended backup based on timestamp and checksum verification.
3. Run the restore command.
4. Run `./infra/scripts/health-check.sh` and test API, media, and
   `https://admin.asepblog.online/admin/` through Cloudflare.
5. Review logs and only then resume normal traffic.

Do not run restore as part of normal deployment. A schema migration rollback is
not a substitute for restoring the matching SQL and media backup.
