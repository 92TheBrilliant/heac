# Backup System Guide

## Overview

The HEAC CMS includes a comprehensive backup system powered by Spatie Laravel Backup. This system automatically backs up your database and files on a daily schedule and provides monitoring and health checks.

## Features

- **Automated Daily Backups**: Database and files are backed up automatically at 1 AM daily
- **Backup Monitoring**: Health checks run daily to ensure backups are current and valid
- **Email Notifications**: Receive notifications for backup success, failures, and health issues
- **Admin Dashboard**: View backup status and manage backups from the Filament admin panel
- **Flexible Storage**: Store backups locally or on S3-compatible storage
- **Retention Policy**: Automatic cleanup of old backups based on configurable retention rules

## Configuration

### Environment Variables

Add these variables to your `.env` file:

```env
# Backup Configuration
BACKUP_DISK=local                           # Storage disk for backups (local, backup-s3)
BACKUP_NOTIFICATION_EMAIL=admin@heac.com    # Email for backup notifications
BACKUP_ARCHIVE_PASSWORD=                    # Optional password for backup encryption
AWS_BACKUP_BUCKET=                          # S3 bucket for backups (if using S3)
```

### Storage Disks

The system supports multiple storage options:

1. **Local Storage** (default): Backups stored in `storage/app/backups`
2. **S3 Storage**: Configure AWS credentials and set `BACKUP_DISK=backup-s3`

### Retention Policy

Default retention settings (configurable in `config/backup.php`):

- **7 days**: Keep all backups
- **30 days**: Keep daily backups
- **12 weeks**: Keep weekly backups
- **6 months**: Keep monthly backups
- **3 years**: Keep yearly backups
- **Max storage**: 10 GB (oldest backups deleted when exceeded)

## Scheduled Tasks

The following backup tasks run automatically:

| Time  | Command                  | Description                          |
|-------|--------------------------|--------------------------------------|
| 1 AM  | `backup:run`             | Create full backup                   |
| 3 AM  | `backup:clean`           | Clean up old backups                 |
| 4 AM  | `backup:monitor`         | Monitor backup health                |
| 5 AM  | `backup:health-check`    | Custom health check with reporting   |

Ensure your cron is configured to run Laravel's scheduler:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## Manual Backup Commands

### Create a Full Backup

```bash
php artisan backup:run
```

### Backup Database Only

```bash
php artisan backup:run --only-db
```

### Backup Files Only

```bash
php artisan backup:run --only-files
```

### Clean Old Backups

```bash
php artisan backup:clean
```

### List All Backups

```bash
php artisan backup:list
```

### Monitor Backup Health

```bash
php artisan backup:monitor
```

### Custom Health Check

```bash
php artisan backup:health-check
```

## Admin Panel Features

### Backup Status Widget

The dashboard includes a Backup Status Widget that displays:

- Backup destination health status
- Number of backups
- Latest backup date
- Storage usage
- List of recent backups

### Manual Backup Management

Use the artisan commands to manage backups manually. All commands can be run from the terminal.

## Email Notifications

The system sends email notifications for:

- ✅ Successful backups
- ❌ Failed backups
- ⚠️ Unhealthy backup destinations
- 🧹 Successful cleanup operations
- ❌ Failed cleanup operations

Configure the notification email in your `.env` file:

```env
BACKUP_NOTIFICATION_EMAIL=admin@heac.com
```

## Backup Encryption

For added security, you can encrypt backup archives with a password:

```env
BACKUP_ARCHIVE_PASSWORD=your-secure-password
```

**Important:** Store this password securely! You'll need it to restore encrypted backups.

## Using S3 for Backups

To store backups on Amazon S3 or S3-compatible storage:

1. Configure AWS credentials in `.env`:

```env
AWS_ACCESS_KEY_ID=your-access-key
AWS_SECRET_ACCESS_KEY=your-secret-key
AWS_DEFAULT_REGION=us-east-1
AWS_BACKUP_BUCKET=your-backup-bucket
```

2. Set the backup disk:

```env
BACKUP_DISK=backup-s3
```

3. Update `config/backup.php` if needed to customize S3 settings

## Restoring from Backup

### Database Restore

1. Locate your backup file in `storage/app/backups` (or download from S3)
2. Extract the backup archive
3. Find the database dump file (e.g., `db-dumps/mysql-database.sql`)
4. Restore the database:

```bash
mysql -u username -p database_name < mysql-database.sql
```

### Files Restore

1. Extract the backup archive
2. Copy files from the backup to your application directory
3. Ensure proper permissions are set

### Full System Restore

For a complete system restore:

1. Set up a fresh Laravel installation
2. Restore the database as described above
3. Restore the `storage/app/public` directory
4. Restore any custom files from the backup
5. Run migrations if needed: `php artisan migrate`
6. Clear caches: `php artisan optimize:clear`

## Monitoring and Troubleshooting

### Check Backup Health

Run the health check command to verify all backups are current:

```bash
php artisan backup:health-check
```

### Common Issues

**Issue: Backups not running automatically**
- Verify cron is configured correctly
- Check Laravel scheduler is running: `php artisan schedule:list`
- Review logs in `storage/logs/laravel.log`

**Issue: Backup destination unreachable**
- Verify disk configuration in `config/filesystems.php`
- Check storage directory permissions
- For S3: Verify AWS credentials and bucket access

**Issue: Backups too large**
- Exclude unnecessary directories in `config/backup.php`
- Consider database-only backups for frequent backups
- Adjust retention policy to keep fewer backups

**Issue: Email notifications not sending**
- Verify mail configuration in `.env`
- Check `BACKUP_NOTIFICATION_EMAIL` is set correctly
- Review mail logs

### Logs

Backup operations are logged to:
- Laravel log: `storage/logs/laravel.log`
- Backup-specific events are logged with context

## Best Practices

1. **Test Restores Regularly**: Verify backups can be restored successfully
2. **Off-site Storage**: Use S3 or similar for disaster recovery
3. **Monitor Notifications**: Set up email alerts and review them regularly
4. **Secure Backups**: Use encryption for sensitive data
5. **Document Procedures**: Keep restore procedures documented and accessible
6. **Verify Disk Space**: Ensure adequate storage for retention policy
7. **Review Retention**: Adjust retention policy based on your needs

## Security Considerations

- Backup files contain sensitive data - secure them appropriately
- Use encryption for backups containing personal information
- Restrict access to backup storage (S3 bucket policies, file permissions)
- Regularly rotate backup encryption passwords
- Limit admin panel access to trusted administrators only

## Support

For issues or questions about the backup system:

1. Check the logs in `storage/logs/laravel.log`
2. Review the Spatie Laravel Backup documentation: https://spatie.be/docs/laravel-backup
3. Run health checks to diagnose issues
4. Contact your system administrator

## Additional Resources

- [Spatie Laravel Backup Documentation](https://spatie.be/docs/laravel-backup)
- [Laravel Task Scheduling](https://laravel.com/docs/scheduling)
- [Laravel Filesystem](https://laravel.com/docs/filesystem)
