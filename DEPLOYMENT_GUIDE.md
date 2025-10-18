# HEAC CMS Deployment Guide

## Overview

This guide covers the deployment process for the HEAC CMS application, including zero-downtime deployment strategies, rollback procedures, and best practices.

## Prerequisites

### Server Requirements
- PHP 8.2 or higher
- Composer 2.x
- Node.js 18.x or higher
- MySQL 8.0+ or PostgreSQL 14+
- Redis (for caching and queues)
- Git
- Web server (Nginx or Apache)

### Required PHP Extensions
- BCMath
- Ctype
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO
- Tokenizer
- XML
- GD or Imagick

## Deployment Scripts

### Linux/Unix Deployment

Use the `deploy.sh` script for Linux/Unix systems:

```bash
# Make script executable
chmod +x deploy.sh

# Run deployment
./deploy.sh
```

### Windows Deployment

Use the `deploy.ps1` script for Windows systems:

```powershell
# Run deployment
.\deploy.ps1

# Or with custom paths
.\deploy.ps1 -AppDir "C:\inetpub\wwwroot\heac-cms" -BackupDir "C:\Backups\heac-cms"
```

## Manual Deployment Steps

If you prefer to deploy manually or need to customize the process:

### 1. Enable Maintenance Mode

```bash
php artisan down --retry=60 --secret="your-secret-key"
```

Access the site during maintenance with: `https://yoursite.com?secret=your-secret-key`

### 2. Pull Latest Code

```bash
git fetch origin
git pull origin main
```

### 3. Install Dependencies

```bash
# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install Node dependencies
npm ci --production
```

### 4. Build Assets

```bash
npm run build
```

### 5. Run Migrations

```bash
php artisan migrate --force
```

### 6. Clear and Optimize Caches

```bash
# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### 7. Warm Cache (Optional)

```bash
php artisan cache:warm
```

### 8. Disable Maintenance Mode

```bash
php artisan up
```

### 9. Verify Deployment

```bash
# Check application health
curl -I https://yoursite.com

# Check logs for errors
tail -f storage/logs/laravel.log
```

## Zero-Downtime Deployment

For production environments, use a zero-downtime deployment strategy:

### Using Symlinks (Recommended)

```bash
# Directory structure
/var/www/
├── heac-cms/              # Symlink to current release
├── releases/
│   ├── 20250110_120000/
│   ├── 20250110_130000/
│   └── 20250110_140000/   # Latest release
└── shared/
    ├── storage/
    └── .env
```

### Deployment Process

1. Deploy to new release directory
2. Run migrations and optimizations
3. Update symlink to new release
4. Reload PHP-FPM (no downtime)
5. Keep last 5 releases for quick rollback

## Rollback Procedure

### Automatic Rollback

The deployment scripts include automatic rollback on failure.

### Manual Rollback

```bash
# Restore from backup
cd /var/backups/heac-cms
ls -lt  # List backups by date

# Copy backup to application directory
cp -r backup_YYYYMMDD_HHMMSS/* /var/www/heac-cms/

# Restore database
php artisan backup:restore --latest

# Clear caches
php artisan cache:clear
php artisan config:clear
```

## Environment-Specific Configurations

### Development

```bash
APP_ENV=local
APP_DEBUG=true
```

### Staging

```bash
APP_ENV=staging
APP_DEBUG=false
```

### Production

```bash
APP_ENV=production
APP_DEBUG=false
```

## Post-Deployment Checklist

- [ ] Application loads without errors
- [ ] Admin panel is accessible
- [ ] Database migrations completed successfully
- [ ] Assets are loading correctly
- [ ] Cache is working properly
- [ ] Queue workers are running
- [ ] Scheduled tasks are configured
- [ ] Backups are running
- [ ] SSL certificate is valid
- [ ] Security headers are set
- [ ] Error logging is working
- [ ] Email notifications are working

## Monitoring

### Application Health

```bash
# Check application status
php artisan health:check

# Monitor queue workers
php artisan queue:monitor

# Check scheduled tasks
php artisan schedule:list
```

### Log Monitoring

```bash
# Real-time log monitoring
tail -f storage/logs/laravel.log

# Check for errors
grep ERROR storage/logs/laravel.log
```

## Troubleshooting

### Common Issues

#### Permission Errors

```bash
# Fix storage permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### Cache Issues

```bash
# Clear all caches
php artisan optimize:clear

# Rebuild caches
php artisan optimize
```

#### Migration Errors

```bash
# Check migration status
php artisan migrate:status

# Rollback last migration
php artisan migrate:rollback --step=1
```

#### Asset Loading Issues

```bash
# Rebuild assets
npm run build

# Clear browser cache
# Check asset paths in .env
```

## CI/CD Integration

### GitHub Actions Example

See `.github/workflows/deploy.yml` for automated deployment configuration.

### GitLab CI Example

See `.gitlab-ci.yml` for automated deployment configuration.

## Security Considerations

1. **Never commit `.env` file** - Use environment-specific configurations
2. **Use secure secrets** - Rotate deployment keys regularly
3. **Enable HTTPS** - Force SSL in production
4. **Restrict access** - Use firewall rules and IP whitelisting
5. **Monitor logs** - Set up alerts for suspicious activity
6. **Keep updated** - Regularly update dependencies

## Backup Strategy

- **Database**: Daily automated backups, retained for 30 days
- **Files**: Weekly full backup, daily incremental
- **Backup Storage**: Off-site S3-compatible storage
- **Restore Testing**: Monthly restore drills

## Support

For deployment issues or questions:
- Check logs: `storage/logs/laravel.log`
- Review documentation: `README.md`
- Contact system administrator

## Version History

- **v1.0.0** - Initial deployment guide
- Last updated: 2025-10-10
