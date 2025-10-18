# HEAC CMS Deployment Script for Windows
# Zero-downtime deployment with health checks and rollback capability

param(
    [string]$AppDir = "C:\inetpub\wwwroot\heac-cms",
    [string]$BackupDir = "C:\Backups\heac-cms"
)

$ErrorActionPreference = "Stop"

# Configuration
$Timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
$BackupPath = Join-Path $BackupDir "backup_$Timestamp"

# Functions
function Write-Info {
    param([string]$Message)
    Write-Host "[INFO] $Message" -ForegroundColor Green
}

function Write-Warning {
    param([string]$Message)
    Write-Host "[WARNING] $Message" -ForegroundColor Yellow
}

function Write-Error {
    param([string]$Message)
    Write-Host "[ERROR] $Message" -ForegroundColor Red
}

# Create backup before deployment
function Create-Backup {
    Write-Info "Creating backup..."
    
    if (-not (Test-Path $BackupDir)) {
        New-Item -ItemType Directory -Path $BackupDir | Out-Null
    }
    
    # Backup database
    php artisan backup:run --only-db
    
    # Backup current release
    if (Test-Path $AppDir) {
        Copy-Item -Path $AppDir -Destination $BackupPath -Recurse
        Write-Info "Backup created at $BackupPath"
    }
}

# Enable maintenance mode
function Enable-Maintenance {
    Write-Info "Enabling maintenance mode..."
    php artisan down --retry=60 --secret="deployment-secret-key"
    Write-Info "Maintenance mode enabled"
}

# Disable maintenance mode
function Disable-Maintenance {
    Write-Info "Disabling maintenance mode..."
    php artisan up
}

# Pull latest code
function Pull-Code {
    Write-Info "Pulling latest code from repository..."
    git fetch origin
    git reset --hard origin/main
    Write-Info "Code updated successfully"
}

# Install dependencies
function Install-Dependencies {
    Write-Info "Installing Composer dependencies..."
    composer install --no-dev --optimize-autoloader --no-interaction
    
    Write-Info "Installing NPM dependencies..."
    npm ci --production
}

# Build assets
function Build-Assets {
    Write-Info "Building frontend assets..."
    npm run build
}

# Run database migrations
function Run-Migrations {
    Write-Info "Running database migrations..."
    php artisan migrate --force
}

# Clear and optimize caches
function Optimize-Application {
    Write-Info "Clearing caches..."
    
    php artisan cache:clear
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    
    Write-Info "Optimizing application..."
    
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache
    
    composer dump-autoload --optimize
}

# Warm cache
function Warm-Cache {
    Write-Info "Warming application cache..."
    try {
        php artisan cache:warm
    } catch {
        Write-Warning "Cache warming command not available"
    }
}

# Run health check
function Test-Health {
    Write-Info "Running health check..."
    
    try {
        $response = Invoke-WebRequest -Uri "http://localhost" -UseBasicParsing -TimeoutSec 10
        $statusCode = $response.StatusCode
        
        if ($statusCode -eq 200 -or $statusCode -eq 302) {
            Write-Info "Health check passed (HTTP $statusCode)"
            return $true
        } else {
            Write-Error "Health check failed (HTTP $statusCode)"
            return $false
        }
    } catch {
        Write-Error "Health check failed: $_"
        return $false
    }
}

# Rollback deployment
function Invoke-Rollback {
    Write-Error "Deployment failed. Rolling back..."
    
    if (Test-Path $BackupPath) {
        Write-Info "Restoring from backup..."
        Remove-Item -Path $AppDir -Recurse -Force
        Copy-Item -Path $BackupPath -Destination $AppDir -Recurse
        
        Set-Location $AppDir
        
        Write-Info "Restoring database..."
        php artisan backup:restore --latest
        
        php artisan cache:clear
        php artisan config:clear
        
        Write-Info "Rollback completed"
    } else {
        Write-Error "No backup found at $BackupPath"
    }
    
    Disable-Maintenance
    exit 1
}

# Cleanup old backups
function Remove-OldBackups {
    Write-Info "Cleaning up old backups (keeping last 5)..."
    
    Get-ChildItem -Path $BackupDir -Directory | 
        Sort-Object CreationTime -Descending | 
        Select-Object -Skip 5 | 
        Remove-Item -Recurse -Force
}

# Main deployment process
function Start-Deployment {
    Write-Info "Starting deployment at $(Get-Date)"
    Write-Info "=========================================="
    
    # Change to application directory
    Set-Location $AppDir
    
    try {
        # Create backup
        Create-Backup
        
        # Enable maintenance mode
        Enable-Maintenance
        
        # Deployment steps
        Pull-Code
        Install-Dependencies
        Build-Assets
        Run-Migrations
        Optimize-Application
        Warm-Cache
        
        # Disable maintenance mode
        Disable-Maintenance
        
        # Post-deployment health check
        Start-Sleep -Seconds 2
        if (-not (Test-Health)) {
            throw "Post-deployment health check failed"
        }
        
        # Cleanup
        Remove-OldBackups
        
        Write-Info "=========================================="
        Write-Info "Deployment completed successfully at $(Get-Date)"
        Write-Info "Application is now live!"
        
    } catch {
        Write-Error "Deployment error: $_"
        Invoke-Rollback
    }
}

# Run main deployment
Start-Deployment
