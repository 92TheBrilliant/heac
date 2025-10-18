# SEO and Performance Optimization Guide

This document outlines the SEO and performance optimizations implemented in the HEAC CMS.

## Table of Contents

1. [Sitemap Generation](#sitemap-generation)
2. [Structured Data](#structured-data)
3. [Caching Strategies](#caching-strategies)
4. [Image Optimization](#image-optimization)
5. [Asset Optimization](#asset-optimization)

## Sitemap Generation

### Automatic Sitemap Generation

The CMS automatically generates an XML sitemap that includes all published pages and research.

**Command:**
```bash
php artisan sitemap:generate
```

**Scheduled Execution:**
The sitemap is automatically regenerated daily at 2:00 AM via Laravel's task scheduler.

**Location:**
The sitemap is saved to `public/sitemap.xml` and is accessible at `https://yourdomain.com/sitemap.xml`

**What's Included:**
- Homepage
- All published pages
- Research listing page
- All published research articles
- Contact page

### Manual Regeneration

To manually regenerate the sitemap:
```bash
php artisan sitemap:generate
```

## Structured Data

### Schema.org Markup

The CMS implements structured data (JSON-LD) for better search engine understanding:

#### Organization Schema
- Implemented on the homepage
- Includes organization name, URL, logo, and contact information

#### WebPage Schema
- Applied to all content pages
- Includes page title, description, publication date, and publisher information

#### ScholarlyArticle Schema
- Applied to research publications
- Includes headline, abstract, authors, publication date, and keywords

### Implementation

Structured data is automatically added to pages via the `<x-structured-data>` Blade component:

```blade
@push('structured-data')
    <x-structured-data :data="$structuredData" />
@endpush
```

### Open Graph Tags

All pages include Open Graph tags for social media sharing:
- `og:title`
- `og:description`
- `og:image`
- `og:url`
- `og:type`

Research articles also include:
- `article:published_time`
- `article:author`
- `article:tag`

## Caching Strategies

### Redis Configuration

The CMS uses Redis for caching and session management.

**Environment Variables:**
```env
CACHE_STORE=redis
SESSION_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_DB=0
REDIS_CACHE_DB=1
```

### Cache Layers

1. **Route Caching**
   ```bash
   php artisan route:cache
   ```

2. **Config Caching**
   ```bash
   php artisan config:cache
   ```

3. **View Caching**
   ```bash
   php artisan view:cache
   ```

4. **Query Result Caching**
   - Homepage data: 15 minutes
   - Page content: 15 minutes
   - Research listings: 15 minutes

5. **Page Response Caching**
   - Public pages cached for 15 minutes
   - Automatic cache invalidation on content updates

### Cache Management

**Clear All Caches:**
```bash
php artisan cache:clear
```

**Warm Cache (Pre-populate):**
```bash
php artisan cache:warm
```

**Clear Specific Caches:**
```php
use App\Services\CacheService;

$cacheService = app(CacheService::class);

// Clear page caches
$cacheService->clearPageCache();

// Clear research caches
$cacheService->clearResearchCache();

// Clear homepage cache
$cacheService->clearHomepageCache();
```

### Automatic Cache Invalidation

The CMS automatically clears relevant caches when content is updated:
- Page created/updated/deleted → Page and homepage caches cleared
- Research created/updated/deleted → Research and homepage caches cleared

This is handled by model observers:
- `PageObserver`
- `ResearchObserver`

### Cache Tags

When using Redis or Memcached, the CMS supports cache tagging for selective cache clearing:

```php
Cache::tags(['pages'])->flush();
Cache::tags(['research'])->flush();
```

## Image Optimization

### Automatic Optimization

Images are automatically optimized when uploaded:

1. **WebP Conversion**
   - All uploaded images (except SVG) are converted to WebP format
   - Original files are preserved
   - Quality set to 85%

2. **Thumbnail Generation**
   - Small: 150x150px
   - Medium: 300x300px
   - Large: 600x600px

3. **Lazy Loading**
   - All images use native lazy loading
   - Intersection Observer for progressive loading
   - Smooth fade-in animation on load

### Using Lazy Loading Component

```blade
<x-lazy-image 
    src="{{ $imageUrl }}" 
    alt="Image description"
    class="w-full h-auto"
/>
```

### Manual Image Optimization

To optimize existing images:

```php
use App\Services\MediaService;

$mediaService = app(MediaService::class);
$media = Media::find($id);

// Generate thumbnails
$mediaService->generateThumbnails($media);

// Convert to WebP
$mediaService->optimizeImage($media);
```

## Asset Optimization

### Vite Build Configuration

The CMS uses Vite for modern asset bundling with the following optimizations:

1. **Code Splitting**
   - Vendor code separated into its own chunk
   - Automatic chunk splitting for large files

2. **Minification**
   - JavaScript minified with Terser
   - CSS minified automatically
   - Console statements removed in production
   - Comments stripped

3. **Cache Busting**
   - All assets include content hash in filename
   - Example: `app-a1b2c3d4.js`
   - Automatic browser cache invalidation on updates

4. **Asset Organization**
   - Images: `build/images/[name]-[hash][ext]`
   - Fonts: `build/fonts/[name]-[hash][ext]`
   - JS: `build/js/[name]-[hash].js`
   - CSS: `build/css/[name]-[hash].css`

### Building for Production

```bash
npm run build
```

This will:
- Minify all JavaScript and CSS
- Generate hashed filenames
- Split code into optimized chunks
- Remove development code (console.log, etc.)
- Optimize images and assets

### Development Mode

```bash
npm run dev
```

Features:
- Hot module replacement (HMR)
- Source maps for debugging
- Fast rebuild times

## Performance Best Practices

### 1. Enable OPcache (PHP)

Add to `php.ini`:
```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
```

### 2. Use CDN for Static Assets

Configure in `.env`:
```env
ASSET_URL=https://cdn.yourdomain.com
```

### 3. Enable Gzip/Brotli Compression

Configure in your web server (Nginx example):
```nginx
gzip on;
gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript;
```

### 4. Set Cache Headers

Add to `.htaccess` or Nginx config:
```
# Cache static assets for 1 year
location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff2)$ {
    expires 1y;
    add_header Cache-Control "public, immutable";
}
```

### 5. Database Optimization

- Ensure proper indexes on frequently queried columns
- Use eager loading to prevent N+1 queries
- Monitor slow queries with Laravel Telescope

### 6. Queue Long-Running Tasks

Use queues for:
- Email sending
- Image processing
- Report generation
- Sitemap generation (if large)

```bash
php artisan queue:work
```

## Monitoring Performance

### Laravel Telescope

Install for development monitoring:
```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

Access at: `https://yourdomain.com/telescope`

### Performance Metrics

Monitor these key metrics:
- Page load time: < 2 seconds
- Time to First Byte (TTFB): < 600ms
- First Contentful Paint (FCP): < 1.8 seconds
- Largest Contentful Paint (LCP): < 2.5 seconds
- Cumulative Layout Shift (CLS): < 0.1
- First Input Delay (FID): < 100ms

### Tools

- Google PageSpeed Insights
- GTmetrix
- WebPageTest
- Chrome DevTools Lighthouse

## Troubleshooting

### Cache Not Working

1. Check Redis connection:
   ```bash
   php artisan tinker
   >>> Cache::put('test', 'value', 60);
   >>> Cache::get('test');
   ```

2. Verify Redis is running:
   ```bash
   redis-cli ping
   ```

3. Check cache driver in `.env`:
   ```env
   CACHE_STORE=redis
   ```

### Images Not Optimizing

1. Ensure Intervention Image is installed:
   ```bash
   composer require intervention/image
   ```

2. Check GD or Imagick extension is enabled:
   ```bash
   php -m | grep -i gd
   ```

3. Verify storage permissions:
   ```bash
   chmod -R 775 storage/app/public
   ```

### Sitemap Not Generating

1. Check scheduled tasks are running:
   ```bash
   php artisan schedule:list
   ```

2. Manually run the command:
   ```bash
   php artisan sitemap:generate
   ```

3. Verify public directory is writable:
   ```bash
   chmod 775 public
   ```

## Deployment Checklist

Before deploying to production:

- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`
- [ ] Run `npm run build`
- [ ] Run `php artisan sitemap:generate`
- [ ] Run `php artisan cache:warm`
- [ ] Verify Redis is configured and running
- [ ] Enable OPcache
- [ ] Configure web server caching headers
- [ ] Set up CDN (optional)
- [ ] Enable Gzip/Brotli compression
- [ ] Test with PageSpeed Insights

## Additional Resources

- [Laravel Performance](https://laravel.com/docs/11.x/deployment#optimization)
- [Vite Documentation](https://vitejs.dev/guide/)
- [Redis Documentation](https://redis.io/documentation)
- [Web.dev Performance](https://web.dev/performance/)
- [Schema.org Documentation](https://schema.org/)
