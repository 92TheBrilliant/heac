# Performance Optimization Guide

## Optimizations Applied

### 1. Environment Configuration
- ✅ Changed `APP_DEBUG` to `false` (reduces overhead)
- ✅ Changed `LOG_LEVEL` to `error` (less logging)
- ✅ Changed `CACHE_STORE` to `file` (faster than database)

### 2. Caching Strategy
- ✅ Homepage data cached for 1 hour (was 15 minutes)
- ✅ Organization data cached for 24 hours
- ✅ Reduced featured research from 6 to 3 items
- ✅ Reduced latest pages from 3 to 2 items
- ✅ Configuration cached
- ✅ Routes cached
- ✅ Views cached
- ✅ Events cached

### 3. Database Optimization
- ✅ Existing indexes on research and pages tables
- ✅ Composite indexes for common queries
- ✅ Analytics table properly indexed

## Performance Improvements

### Before Optimization:
- Multiple database queries on each page load
- No caching of homepage data
- Debug mode enabled (slower)
- Verbose logging

### After Optimization:
- Homepage data cached for 1 hour
- Configuration, routes, and views cached
- Debug mode disabled
- Minimal logging
- Fewer items loaded per page

## Expected Results
- **Page Load Time**: 50-70% faster
- **Database Queries**: Reduced by 80%+
- **Server Response**: Under 200ms (from cache)
- **Time to First Byte (TTFB)**: Under 100ms

## Maintenance Commands

### Clear All Caches (when updating content)
```bash
php artisan optimize:clear
```

### Re-optimize After Changes
```bash
.\optimize-performance.ps1
```

### Individual Cache Commands
```bash
# Clear specific caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

## Additional Optimization Tips

### 1. Enable OPcache (PHP)
Add to your `php.ini`:
```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=10000
opcache.revalidate_freq=2
```

### 2. Use Redis (Optional)
For even better performance, install Redis and update `.env`:
```env
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### 3. Enable Gzip Compression
In your web server configuration (Apache/Nginx), enable gzip compression for text files.

### 4. Use CDN for Assets
Consider using a CDN for static assets (CSS, JS, images) to reduce server load.

### 5. Image Optimization
- Use WebP format for images
- Compress images before uploading
- Use lazy loading for images below the fold

### 6. Database Optimization
```bash
# Optimize database tables
php artisan db:optimize
```

### 7. Queue Long-Running Tasks
Move email sending and other slow operations to queues:
```bash
php artisan queue:work
```

## Monitoring Performance

### Check Page Load Time
Use browser DevTools Network tab to monitor:
- Total page load time
- Number of requests
- Total page size
- Time to First Byte (TTFB)

### Laravel Telescope (Development)
Install Laravel Telescope for detailed performance monitoring:
```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

### Production Monitoring
Consider using:
- New Relic
- Blackfire.io
- Laravel Debugbar (development only)

## Troubleshooting

### If Site is Still Slow:

1. **Check Database**
   ```bash
   php artisan db:show
   ```

2. **Check Cache**
   ```bash
   php artisan cache:table
   ```

3. **Check Logs**
   ```bash
   tail -f storage/logs/laravel.log
   ```

4. **Profile Queries**
   Enable query logging temporarily in `AppServiceProvider`:
   ```php
   DB::listen(function($query) {
       Log::info($query->sql, $query->bindings);
   });
   ```

### Clear Everything and Start Fresh
```bash
php artisan optimize:clear
php artisan cache:clear
composer dump-autoload
.\optimize-performance.ps1
```

## Performance Checklist

- [x] Debug mode disabled
- [x] Caching enabled
- [x] Configuration cached
- [x] Routes cached
- [x] Views cached
- [x] Reduced homepage queries
- [x] Database indexes in place
- [ ] OPcache enabled (server configuration)
- [ ] Gzip compression enabled (server configuration)
- [ ] CDN configured (optional)
- [ ] Redis installed (optional)

## Notes

- Always run `optimize-performance.ps1` after deploying code changes
- Clear cache when updating content through admin panel
- Monitor performance regularly using browser DevTools
- Consider upgrading to Redis for production environments
