# HEAC Performance Optimization Script
Write-Host "Starting HEAC Performance Optimization..." -ForegroundColor Green

# Clear all caches
Write-Host "`nClearing caches..." -ForegroundColor Yellow
php artisan optimize:clear

# Cache configuration
Write-Host "`nCaching configuration..." -ForegroundColor Yellow
php artisan config:cache

# Cache routes
Write-Host "`nCaching routes..." -ForegroundColor Yellow
php artisan route:cache

# Cache views
Write-Host "`nCaching views..." -ForegroundColor Yellow
php artisan view:cache

# Cache events
Write-Host "`nCaching events..." -ForegroundColor Yellow
php artisan event:cache

# Optimize autoloader
Write-Host "`nOptimizing Composer autoloader..." -ForegroundColor Yellow
composer dump-autoload --optimize --no-dev --quiet

Write-Host "`n✓ Performance optimization complete!" -ForegroundColor Green
Write-Host "Your website should now load significantly faster." -ForegroundColor Cyan
