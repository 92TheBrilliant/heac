#!/bin/bash

###############################################################################
# HEAC CMS Deployment Script
# Zero-downtime deployment with health checks and rollback capability
###############################################################################

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
APP_DIR="/var/www/heac-cms"
BACKUP_DIR="/var/backups/heac-cms"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_PATH="${BACKUP_DIR}/backup_${TIMESTAMP}"

# Functions
log_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if running as correct user
check_user() {
    if [ "$EUID" -eq 0 ]; then 
        log_error "Please do not run as root. Run as the web server user."
        exit 1
    fi
}

# Create backup before deployment
create_backup() {
    log_info "Creating backup..."
    mkdir -p "${BACKUP_DIR}"
    
    # Backup database
    php artisan backup:run --only-db
    
    # Backup current release
    if [ -d "${APP_DIR}" ]; then
        cp -r "${APP_DIR}" "${BACKUP_PATH}"
        log_info "Backup created at ${BACKUP_PATH}"
    fi
}

# Enable maintenance mode
enable_maintenance() {
    log_info "Enabling maintenance mode..."
    php artisan down --retry=60 --secret="deployment-secret-key"
    log_info "Maintenance mode enabled. Access with: ?secret=deployment-secret-key"
}

# Disable maintenance mode
disable_maintenance() {
    log_info "Disabling maintenance mode..."
    php artisan up
}

# Pull latest code
pull_code() {
    log_info "Pulling latest code from repository..."
    git fetch origin
    git reset --hard origin/main
    log_info "Code updated successfully"
}

# Install dependencies
install_dependencies() {
    log_info "Installing Composer dependencies..."
    composer install --no-dev --optimize-autoloader --no-interaction
    
    log_info "Installing NPM dependencies..."
    npm ci --production
}

# Build assets
build_assets() {
    log_info "Building frontend assets..."
    npm run build
}

# Run database migrations
run_migrations() {
    log_info "Running database migrations..."
    php artisan migrate --force
}

# Clear and optimize caches
optimize_application() {
    log_info "Clearing caches..."
    
    # Clear application cache
    php artisan cache:clear
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    
    log_info "Optimizing application..."
    
    # Optimize for production
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache
    
    # Optimize Composer autoloader
    composer dump-autoload --optimize
    
    # Clear OPcache if available
    if command -v php-fpm &> /dev/null; then
        log_info "Reloading PHP-FPM..."
        sudo systemctl reload php-fpm || true
    fi
}

# Warm cache
warm_cache() {
    log_info "Warming application cache..."
    php artisan cache:warm || log_warning "Cache warming command not available"
}

# Run health check
health_check() {
    log_info "Running health check..."
    
    # Check if application is responding
    HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost)
    
    if [ "$HTTP_CODE" -eq 200 ] || [ "$HTTP_CODE" -eq 302 ]; then
        log_info "Health check passed (HTTP $HTTP_CODE)"
        return 0
    else
        log_error "Health check failed (HTTP $HTTP_CODE)"
        return 1
    fi
}

# Rollback deployment
rollback() {
    log_error "Deployment failed. Rolling back..."
    
    if [ -d "${BACKUP_PATH}" ]; then
        log_info "Restoring from backup..."
        rsync -a --delete "${BACKUP_PATH}/" "${APP_DIR}/"
        
        # Restore database
        log_info "Restoring database..."
        php artisan backup:restore --latest
        
        # Clear caches
        php artisan cache:clear
        php artisan config:clear
        
        log_info "Rollback completed"
    else
        log_error "No backup found at ${BACKUP_PATH}"
    fi
    
    disable_maintenance
    exit 1
}

# Cleanup old backups
cleanup_backups() {
    log_info "Cleaning up old backups (keeping last 5)..."
    cd "${BACKUP_DIR}"
    ls -t | tail -n +6 | xargs -r rm -rf
}

# Main deployment process
main() {
    log_info "Starting deployment at $(date)"
    log_info "=========================================="
    
    # Change to application directory
    cd "${APP_DIR}"
    
    # Pre-deployment checks
    check_user
    
    # Create backup
    create_backup
    
    # Enable maintenance mode
    enable_maintenance
    
    # Deployment steps with error handling
    {
        pull_code &&
        install_dependencies &&
        build_assets &&
        run_migrations &&
        optimize_application &&
        warm_cache
    } || {
        rollback
    }
    
    # Disable maintenance mode
    disable_maintenance
    
    # Post-deployment health check
    sleep 2
    if ! health_check; then
        log_error "Post-deployment health check failed"
        rollback
    fi
    
    # Cleanup
    cleanup_backups
    
    log_info "=========================================="
    log_info "Deployment completed successfully at $(date)"
    log_info "Application is now live!"
}

# Run main deployment
main
