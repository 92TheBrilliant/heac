# Environment Variables Documentation

## Overview

This document provides detailed information about all environment variables used in the HEAC CMS application.

## Quick Start

1. Copy the appropriate example file:
   - Development: `cp .env.example .env`
   - Staging: `cp .env.staging.example .env`
   - Production: `cp .env.production.example .env`

2. Generate application key:
   ```bash
   php artisan key:generate
   ```

3. Update the values according to your environment

## Application Configuration

### APP_NAME
- **Type:** String
- **Default:** `HEAC CMS`
- **Description:** Application name displayed in admin panel, emails, and page titles
- **Example:** `APP_NAME="HEAC CMS"`

### APP_ENV
- **Type:** String (local, staging, production)
- **Required:** Yes
- **Description:** Current application environment
- **Example:** `APP_ENV=production`

### APP_KEY
- **Type:** String (base64 encoded)
- **Required:** Yes
- **Description:** Encryption key for secure data. Generate with `php artisan key:generate`
- **Example:** `APP_KEY=base64:xxxxxxxxxxxxxxxxxxxxx`

### APP_DEBUG
- **Type:** Boolean
- **Default:** `false`
- **Description:** Enable debug mode. **MUST be false in production**
- **Example:** `APP_DEBUG=false`

### APP_URL
- **Type:** URL
- **Required:** Yes
- **Description:** Full application URL including protocol, no trailing slash
- **Example:** `APP_URL=https://heac.example.com`

### FORCE_HTTPS
- **Type:** Boolean
- **Default:** `false`
- **Description:** Force HTTPS redirects. Set to `true` in production
- **Example:** `FORCE_HTTPS=true`

## Database Configuration

### DB_CONNECTION
- **Type:** String (mysql, pgsql, sqlite)
- **Required:** Yes
- **Description:** Database driver to use
- **Example:** `DB_CONNECTION=mysql`

### DB_HOST
- **Type:** String
- **Required:** Yes (except for SQLite)
- **Description:** Database server hostname or IP
- **Example:** `DB_HOST=127.0.0.1`

### DB_PORT
- **Type:** Integer
- **Default:** `3306` (MySQL), `5432` (PostgreSQL)
- **Description:** Database server port
- **Example:** `DB_PORT=3306`

### DB_DATABASE
- **Type:** String
- **Required:** Yes
- **Description:** Database name
- **Example:** `DB_DATABASE=heac_cms`

### DB_USERNAME
- **Type:** String
- **Required:** Yes
- **Description:** Database username
- **Example:** `DB_USERNAME=heac_user`

### DB_PASSWORD
- **Type:** String
- **Required:** Yes (in production)
- **Description:** Database password
- **Example:** `DB_PASSWORD=secure_password_here`

## Cache Configuration

### CACHE_STORE
- **Type:** String (redis, memcached, file, database)
- **Default:** `redis`
- **Description:** Cache driver to use
- **Recommendation:** Use `redis` for production
- **Example:** `CACHE_STORE=redis`

### CACHE_PREFIX
- **Type:** String
- **Default:** `heac_cms_cache`
- **Description:** Prefix for cache keys to avoid conflicts
- **Example:** `CACHE_PREFIX=heac_prod_cache`

### CACHE_TTL
- **Type:** Integer (seconds)
- **Default:** `3600`
- **Description:** Default cache time-to-live
- **Example:** `CACHE_TTL=3600`

## Redis Configuration

### REDIS_HOST
- **Type:** String
- **Default:** `127.0.0.1`
- **Description:** Redis server hostname
- **Example:** `REDIS_HOST=redis.example.com`

### REDIS_PASSWORD
- **Type:** String
- **Default:** `null`
- **Description:** Redis authentication password
- **Example:** `REDIS_PASSWORD=redis_password_here`

### REDIS_PORT
- **Type:** Integer
- **Default:** `6379`
- **Description:** Redis server port
- **Example:** `REDIS_PORT=6379`

### REDIS_DB
- **Type:** Integer
- **Default:** `0`
- **Description:** Redis database number for general use
- **Example:** `REDIS_DB=0`

### REDIS_CACHE_DB
- **Type:** Integer
- **Default:** `1`
- **Description:** Redis database number for cache
- **Example:** `REDIS_CACHE_DB=1`

### REDIS_QUEUE_DB
- **Type:** Integer
- **Default:** `2`
- **Description:** Redis database number for queues
- **Example:** `REDIS_QUEUE_DB=2`

## Queue Configuration

### QUEUE_CONNECTION
- **Type:** String (redis, database, sync)
- **Default:** `redis`
- **Description:** Queue driver to use
- **Recommendation:** Use `redis` for production, `sync` for local development
- **Example:** `QUEUE_CONNECTION=redis`

### QUEUE_NAME
- **Type:** String
- **Default:** `default`
- **Description:** Default queue name
- **Example:** `QUEUE_NAME=production`

## Mail Configuration

### MAIL_MAILER
- **Type:** String (smtp, sendmail, mailgun, ses, postmark, log)
- **Default:** `smtp`
- **Description:** Mail driver to use
- **Example:** `MAIL_MAILER=smtp`

### MAIL_HOST
- **Type:** String
- **Required:** Yes (for SMTP)
- **Description:** SMTP server hostname
- **Example:** `MAIL_HOST=smtp.example.com`

### MAIL_PORT
- **Type:** Integer
- **Default:** `587`
- **Description:** SMTP server port
- **Example:** `MAIL_PORT=587`

### MAIL_USERNAME
- **Type:** String
- **Required:** Yes (for SMTP with auth)
- **Description:** SMTP authentication username
- **Example:** `MAIL_USERNAME=noreply@heac.example.com`

### MAIL_PASSWORD
- **Type:** String
- **Required:** Yes (for SMTP with auth)
- **Description:** SMTP authentication password
- **Example:** `MAIL_PASSWORD=mail_password_here`

### MAIL_ENCRYPTION
- **Type:** String (tls, ssl, null)
- **Default:** `tls`
- **Description:** Mail encryption method
- **Example:** `MAIL_ENCRYPTION=tls`

### MAIL_FROM_ADDRESS
- **Type:** Email
- **Required:** Yes
- **Description:** Default sender email address
- **Example:** `MAIL_FROM_ADDRESS="noreply@heac.example.com"`

### MAIL_FROM_NAME
- **Type:** String
- **Default:** `${APP_NAME}`
- **Description:** Default sender name
- **Example:** `MAIL_FROM_NAME="HEAC CMS"`

### CONTACT_NOTIFICATION_EMAIL
- **Type:** Email
- **Required:** Yes
- **Description:** Email address to receive contact form notifications
- **Example:** `CONTACT_NOTIFICATION_EMAIL=admin@heac.example.com`

## File Storage Configuration

### FILESYSTEM_DISK
- **Type:** String (local, public, s3)
- **Default:** `public`
- **Description:** Default filesystem disk
- **Recommendation:** Use `s3` for production
- **Example:** `FILESYSTEM_DISK=s3`

### AWS_ACCESS_KEY_ID
- **Type:** String
- **Required:** Yes (if using S3)
- **Description:** AWS access key ID
- **Example:** `AWS_ACCESS_KEY_ID=AKIAIOSFODNN7EXAMPLE`

### AWS_SECRET_ACCESS_KEY
- **Type:** String
- **Required:** Yes (if using S3)
- **Description:** AWS secret access key
- **Example:** `AWS_SECRET_ACCESS_KEY=wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY`

### AWS_DEFAULT_REGION
- **Type:** String
- **Default:** `us-east-1`
- **Description:** AWS region
- **Example:** `AWS_DEFAULT_REGION=us-east-1`

### AWS_BUCKET
- **Type:** String
- **Required:** Yes (if using S3)
- **Description:** S3 bucket name
- **Example:** `AWS_BUCKET=heac-cms-production`

## Analytics Configuration

### GOOGLE_ANALYTICS_ID
- **Type:** String
- **Required:** No
- **Description:** Google Analytics 4 Measurement ID
- **Example:** `GOOGLE_ANALYTICS_ID=G-XXXXXXXXXX`

### GOOGLE_TAG_MANAGER_ID
- **Type:** String
- **Required:** No
- **Description:** Google Tag Manager container ID
- **Example:** `GOOGLE_TAG_MANAGER_ID=GTM-XXXXXXX`

## Backup Configuration

### BACKUP_DISK
- **Type:** String (local, s3)
- **Default:** `local`
- **Description:** Disk to store backups
- **Recommendation:** Use `s3` for production
- **Example:** `BACKUP_DISK=s3`

### BACKUP_NOTIFICATION_EMAIL
- **Type:** Email
- **Required:** Yes
- **Description:** Email to receive backup notifications
- **Example:** `BACKUP_NOTIFICATION_EMAIL=admin@heac.example.com`

### BACKUP_ARCHIVE_PASSWORD
- **Type:** String
- **Required:** No
- **Description:** Password to encrypt backup archives
- **Example:** `BACKUP_ARCHIVE_PASSWORD=strong_password_here`

### AWS_BACKUP_BUCKET
- **Type:** String
- **Required:** Yes (if using S3 for backups)
- **Description:** S3 bucket for backups
- **Example:** `AWS_BACKUP_BUCKET=heac-cms-backups`

### BACKUP_RETENTION_DAYS
- **Type:** Integer
- **Default:** `30`
- **Description:** Number of days to retain backups
- **Example:** `BACKUP_RETENTION_DAYS=30`

## Security Configuration

### SESSION_ENCRYPT
- **Type:** Boolean
- **Default:** `false`
- **Description:** Encrypt session data
- **Recommendation:** Set to `true` in production
- **Example:** `SESSION_ENCRYPT=true`

### CSP_ENABLED
- **Type:** Boolean
- **Default:** `true`
- **Description:** Enable Content Security Policy headers
- **Example:** `CSP_ENABLED=true`

### HSTS_MAX_AGE
- **Type:** Integer (seconds)
- **Default:** `31536000`
- **Description:** HTTP Strict Transport Security max age
- **Example:** `HSTS_MAX_AGE=31536000`

### TWO_FACTOR_ENABLED
- **Type:** Boolean
- **Default:** `false`
- **Description:** Enable two-factor authentication
- **Recommendation:** Set to `true` in production
- **Example:** `TWO_FACTOR_ENABLED=true`

## Rate Limiting Configuration

### CONTACT_FORM_RATE_LIMIT
- **Type:** Integer (requests per minute)
- **Default:** `3`
- **Description:** Maximum contact form submissions per minute per IP
- **Example:** `CONTACT_FORM_RATE_LIMIT=3`

### API_RATE_LIMIT
- **Type:** Integer (requests per minute)
- **Default:** `60`
- **Description:** Maximum API requests per minute
- **Example:** `API_RATE_LIMIT=60`

### LOGIN_RATE_LIMIT
- **Type:** Integer (attempts per minute)
- **Default:** `5`
- **Description:** Maximum login attempts per minute
- **Example:** `LOGIN_RATE_LIMIT=5`

## File Upload Configuration

### MAX_UPLOAD_SIZE
- **Type:** Integer (KB)
- **Default:** `10240`
- **Description:** Maximum file upload size in kilobytes
- **Example:** `MAX_UPLOAD_SIZE=10240`

### ALLOWED_FILE_TYPES
- **Type:** String (comma-separated)
- **Default:** `pdf,doc,docx,xls,xlsx,ppt,pptx`
- **Description:** Allowed file extensions for uploads
- **Example:** `ALLOWED_FILE_TYPES=pdf,doc,docx`

### ALLOWED_IMAGE_TYPES
- **Type:** String (comma-separated)
- **Default:** `jpg,jpeg,png,gif,webp,svg`
- **Description:** Allowed image extensions
- **Example:** `ALLOWED_IMAGE_TYPES=jpg,jpeg,png,webp`

### IMAGE_QUALITY
- **Type:** Integer (1-100)
- **Default:** `85`
- **Description:** JPEG/WebP compression quality
- **Example:** `IMAGE_QUALITY=85`

### GENERATE_WEBP
- **Type:** Boolean
- **Default:** `true`
- **Description:** Automatically generate WebP versions of images
- **Example:** `GENERATE_WEBP=true`

## Performance Configuration

### QUERY_CACHE_ENABLED
- **Type:** Boolean
- **Default:** `true`
- **Description:** Enable database query caching
- **Example:** `QUERY_CACHE_ENABLED=true`

### PAGE_CACHE_ENABLED
- **Type:** Boolean
- **Default:** `true`
- **Description:** Enable full page caching
- **Example:** `PAGE_CACHE_ENABLED=true`

### PAGE_CACHE_TTL
- **Type:** Integer (minutes)
- **Default:** `15`
- **Description:** Page cache time-to-live in minutes
- **Example:** `PAGE_CACHE_TTL=15`

### OPCACHE_ENABLED
- **Type:** Boolean
- **Default:** `true`
- **Description:** Enable PHP OPcache
- **Example:** `OPCACHE_ENABLED=true`

## Development Tools

### TELESCOPE_ENABLED
- **Type:** Boolean
- **Default:** `false`
- **Description:** Enable Laravel Telescope debugging tool
- **Warning:** Set to `false` in production
- **Example:** `TELESCOPE_ENABLED=false`

### DEBUGBAR_ENABLED
- **Type:** Boolean
- **Default:** `false`
- **Description:** Enable Laravel Debugbar
- **Warning:** Set to `false` in production
- **Example:** `DEBUGBAR_ENABLED=false`

## Environment-Specific Recommendations

### Development
- `APP_DEBUG=true`
- `QUEUE_CONNECTION=sync`
- `MAIL_MAILER=log`
- `CACHE_STORE=file`
- `TELESCOPE_ENABLED=true`

### Staging
- `APP_DEBUG=false`
- `QUEUE_CONNECTION=redis`
- `MAIL_MAILER=smtp` (use Mailtrap or similar)
- `CACHE_STORE=redis`
- `TELESCOPE_ENABLED=true`

### Production
- `APP_DEBUG=false`
- `FORCE_HTTPS=true`
- `SESSION_ENCRYPT=true`
- `QUEUE_CONNECTION=redis`
- `CACHE_STORE=redis`
- `FILESYSTEM_DISK=s3`
- `BACKUP_DISK=s3`
- `TWO_FACTOR_ENABLED=true`
- `TELESCOPE_ENABLED=false`
- `DEBUGBAR_ENABLED=false`

## Security Best Practices

1. **Never commit `.env` file** to version control
2. **Use strong passwords** for all credentials
3. **Rotate keys regularly** especially after team member changes
4. **Use different credentials** for each environment
5. **Enable 2FA** in production
6. **Use encrypted connections** (HTTPS, TLS for mail)
7. **Restrict database access** to application servers only
8. **Use IAM roles** instead of AWS keys when possible
9. **Monitor logs** for suspicious activity
10. **Keep backups encrypted** and off-site

## Troubleshooting

### Application Key Issues
```bash
# Generate new key
php artisan key:generate

# Clear config cache
php artisan config:clear
```

### Cache Issues
```bash
# Clear all caches
php artisan optimize:clear

# Rebuild caches
php artisan optimize
```

### Permission Issues
```bash
# Fix storage permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## Additional Resources

- [Laravel Configuration Documentation](https://laravel.com/docs/configuration)
- [Laravel Environment Configuration](https://laravel.com/docs/configuration#environment-configuration)
- [Deployment Guide](DEPLOYMENT_GUIDE.md)
- [README](README.md)
