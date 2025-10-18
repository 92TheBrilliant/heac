# Maintenance Commands Reference

This document provides a quick reference for all maintenance artisan commands available in the HEAC CMS.

## Available Commands

### 1. Sitemap Generation

**Command:** `php artisan sitemap:generate`

**Description:** Generates an XML sitemap for the website including all published pages and research.

**Schedule:** Daily at 2:00 AM

**Usage:**
```bash
# Generate sitemap manually
php artisan sitemap:generate
```

**Output:** Creates `public/sitemap.xml`

---

### 2. Cache Warming

**Command:** `php artisan cache:warm`

**Description:** Pre-caches frequently accessed data including homepage data, popular pages, and popular research.

**Schedule:** Daily at 6:00 AM (also run manually after deployments)

**Usage:**
```bash
# Warm caches manually (recommended after deployments)
php artisan cache:warm
```

**What it caches:**
- Homepage data (featured research, latest pages, statistics)
- Top 10 most viewed pages
- Top 10 most viewed research

---

### 3. Cleanup Old Files

**Command:** `php artisan cleanup:old-files`

**Description:** Cleans up unused media files, old log files, and archives old contact inquiries.

**Schedule:** Weekly on Sunday at 3:00 AM

**Options:**
- `--days=90` - Number of days to keep files (default: 90)
- `--dry-run` - Run without actually deleting files (preview mode)

**Usage:**
```bash
# Run cleanup with default settings (90 days)
php artisan cleanup:old-files

# Run cleanup keeping only 30 days of data
php artisan cleanup:old-files --days=30

# Preview what would be deleted without actually deleting
php artisan cleanup:old-files --dry-run

# Preview with custom days
php artisan cleanup:old-files --days=60 --dry-run
```

**What it cleans:**
- **Unused Media Files:** Media files older than 6 months that are not referenced in any pages or research
- **Old Log Files:** Log files older than the specified number of days
- **Old Inquiries:** Contact inquiries with status "resolved" or "closed" older than the specified number of days (moved to archive table)

---

## Scheduled Tasks Overview

All scheduled tasks are defined in `routes/console.php`:

| Command | Schedule | Time |
|---------|----------|------|
| `backup:run` | Daily | 1:00 AM |
| `sitemap:generate` | Daily | 2:00 AM |
| `backup:clean` | Daily | 3:00 AM |
| `backup:monitor` | Daily | 4:00 AM |
| `backup:health-check` | Daily | 5:00 AM |
| `cache:warm` | Daily | 6:00 AM |
| `cleanup:old-files` | Weekly (Sunday) | 3:00 AM |

## Running the Scheduler

To enable scheduled tasks, add this cron entry to your server:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

Or for development, run:

```bash
php artisan schedule:work
```

## Best Practices

### After Deployment
Always run these commands after deploying:
```bash
php artisan cache:warm
php artisan sitemap:generate
```

### Before Major Updates
Run cleanup in dry-run mode to see what will be affected:
```bash
php artisan cleanup:old-files --dry-run
```

### Monitoring
Check the output of scheduled commands in `storage/logs/laravel.log`

### Manual Cleanup
If you need to aggressively clean up space:
```bash
# Clean up files older than 30 days
php artisan cleanup:old-files --days=30

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Warm caches again
php artisan cache:warm
```

## Troubleshooting

### Sitemap Not Updating
```bash
# Manually regenerate
php artisan sitemap:generate

# Check if file is writable
ls -la public/sitemap.xml
```

### Cache Not Working
```bash
# Clear all caches
php artisan cache:clear

# Warm caches again
php artisan cache:warm

# Check Redis connection (if using Redis)
php artisan tinker
>>> Cache::get('test')
```

### Cleanup Not Running
```bash
# Test in dry-run mode
php artisan cleanup:old-files --dry-run

# Check permissions on storage directories
ls -la storage/logs
ls -la storage/app/public
```

## Archive Table

The cleanup command creates an archive table for old inquiries:

**Table:** `contact_inquiries_archive`

**Structure:** Same as `contact_inquiries` table

**Purpose:** Keeps historical data while reducing the size of the active inquiries table

**Accessing Archive:**
```sql
-- View archived inquiries
SELECT * FROM contact_inquiries_archive;

-- Count archived inquiries
SELECT COUNT(*) FROM contact_inquiries_archive;

-- Restore an inquiry from archive
INSERT INTO contact_inquiries SELECT * FROM contact_inquiries_archive WHERE id = ?;
DELETE FROM contact_inquiries_archive WHERE id = ?;
```

## Performance Tips

1. **Cache Warming:** Run after content updates to ensure fresh data is cached
2. **Sitemap:** Run after publishing new pages or research
3. **Cleanup:** Run more frequently if storage is limited (e.g., `--days=30`)
4. **Monitoring:** Check logs regularly to ensure commands are running successfully

## Security Notes

- The cleanup command requires proper file permissions on `storage/` directories
- Archive table contains sensitive contact information - ensure proper database security
- Log files may contain sensitive information - review before manual deletion
- Always test with `--dry-run` before running cleanup in production
