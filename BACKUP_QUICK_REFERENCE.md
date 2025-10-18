# Backup System Quick Reference

## Quick Commands

```bash
# List all backups and their status
php artisan backup:list

# Create a full backup now
php artisan backup:run

# Create database-only backup
php artisan backup:run --only-db

# Clean up old backups
php artisan backup:clean

# Check backup health
php artisan backup:health-check
```

## Environment Variables

```env
BACKUP_DISK=local                           # or backup-s3 for S3 storage
BACKUP_NOTIFICATION_EMAIL=admin@heac.com    # Email for notifications
BACKUP_ARCHIVE_PASSWORD=                    # Optional encryption password
AWS_BACKUP_BUCKET=                          # S3 bucket name (if using S3)
```

## Backup Schedule

| Time  | Task                | Description                    |
|-------|---------------------|--------------------------------|
| 1 AM  | Create Backup       | Full backup of DB and files    |
| 3 AM  | Cleanup             | Remove old backups             |
| 4 AM  | Monitor             | Check backup health            |
| 5 AM  | Health Check        | Detailed health report         |

## Storage Locations

- **Local:** `storage/app/backups/`
- **S3:** Configured bucket in AWS

## Retention Policy

- **7 days:** All backups kept
- **30 days:** Daily backups kept
- **12 weeks:** Weekly backups kept
- **6 months:** Monthly backups kept
- **3 years:** Yearly backups kept
- **Max:** 10 GB total storage

## Admin Dashboard

Access the Backup Status Widget on the admin dashboard to:
- View backup health status
- See latest backups
- Run manual backups
- Check storage usage

## Troubleshooting

**No backups created?**
- Check if mysqldump is installed
- Verify storage permissions
- Check Laravel logs

**Email notifications not working?**
- Verify MAIL_* settings in .env
- Check BACKUP_NOTIFICATION_EMAIL is set
- Test mail configuration

**Backup failed?**
- Check disk space
- Verify database connection
- Review error logs in storage/logs

## Restore Process

1. Locate backup file in `storage/app/backups/`
2. Extract the ZIP archive
3. Restore database from `db-dumps/` folder
4. Restore files from backup as needed
5. Run `php artisan migrate` if needed
6. Clear caches: `php artisan optimize:clear`

## Support

For detailed documentation, see `BACKUP_SYSTEM_GUIDE.md`
