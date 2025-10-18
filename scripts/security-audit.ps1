# Security Audit Script for HEAC CMS (PowerShell)
# This script runs various security checks and generates a report

Write-Host "======================================" -ForegroundColor Cyan
Write-Host "HEAC CMS Security Audit" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""

# Configuration
$OutputDir = "storage/security-reports"
$Timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
$ReportFile = "$OutputDir/security_audit_$Timestamp.txt"

# Create output directory
if (-not (Test-Path $OutputDir)) {
    New-Item -ItemType Directory -Path $OutputDir | Out-Null
}

Write-Host "Report will be saved to: $ReportFile" -ForegroundColor Yellow
Write-Host ""

# Start report
@"
======================================
HEAC CMS Security Audit Report
======================================
Date: $(Get-Date)

"@ | Out-File -FilePath $ReportFile -Encoding UTF8

# Counters
$script:PassCount = 0
$script:WarnCount = 0
$script:FailCount = 0

# Function to check and report
function Check-Item {
    param(
        [string]$Name,
        [ValidateSet("pass", "warn", "fail")]
        [string]$Status,
        [string]$Message = ""
    )
    
    $symbol = switch ($Status) {
        "pass" { "✓"; $script:PassCount++; $color = "Green" }
        "warn" { "⚠"; $script:WarnCount++; $color = "Yellow" }
        "fail" { "✗"; $script:FailCount++; $color = "Red" }
    }
    
    Write-Host "$symbol $Name" -ForegroundColor $color
    if ($Message) {
        Write-Host "  $Message" -ForegroundColor Gray
    }
    
    @"
[$Status] $Name
$(if ($Message) { "  $Message" })

"@ | Out-File -FilePath $ReportFile -Append -Encoding UTF8
}

Write-Host "======================================" -ForegroundColor Cyan
Write-Host "1. Checking Dependencies" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""

# Check for known vulnerabilities in dependencies
if (Get-Command composer -ErrorAction SilentlyContinue) {
    Write-Host "Running composer audit..." -ForegroundColor Gray
    $auditOutput = composer audit --no-interaction 2>&1
    $auditOutput | Out-File -FilePath $ReportFile -Append -Encoding UTF8
    
    if ($LASTEXITCODE -eq 0) {
        Check-Item "Composer dependencies" "pass" "No known vulnerabilities found"
    } else {
        Check-Item "Composer dependencies" "fail" "Vulnerabilities found - check report"
    }
} else {
    Check-Item "Composer" "fail" "Composer not found"
}

Write-Host ""
Write-Host "======================================" -ForegroundColor Cyan
Write-Host "2. Checking Configuration" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""

# Check .env file
if (Test-Path ".env") {
    Check-Item ".env file exists" "pass"
    
    $envContent = Get-Content ".env" -Raw
    
    # Check if APP_DEBUG is false
    if ($envContent -match "APP_DEBUG=false") {
        Check-Item "APP_DEBUG disabled" "pass"
    } else {
        Check-Item "APP_DEBUG disabled" "warn" "Debug mode should be disabled in production"
    }
    
    # Check if APP_KEY is set
    if ($envContent -match "APP_KEY=base64:") {
        Check-Item "APP_KEY is set" "pass"
    } else {
        Check-Item "APP_KEY is set" "fail" "APP_KEY must be generated"
    }
    
    # Check if default passwords are changed
    if ($envContent -match "DB_PASSWORD=(password|secret)") {
        Check-Item "Database password" "fail" "Default password detected"
    } else {
        Check-Item "Database password" "pass"
    }
} else {
    Check-Item ".env file" "fail" ".env file not found"
}

Write-Host ""
Write-Host "Checking file permissions..." -ForegroundColor Gray

# Check storage directory
if (Test-Path "storage") {
    Check-Item "Storage directory exists" "pass"
} else {
    Check-Item "Storage directory" "fail" "Storage directory not found"
}

# Check .env permissions (Windows doesn't have Unix-style permissions)
if (Test-Path ".env") {
    $envAcl = Get-Acl ".env"
    Check-Item ".env file permissions" "pass" "File exists and is accessible"
}

Write-Host ""
Write-Host "======================================" -ForegroundColor Cyan
Write-Host "3. Running Security Tests" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""

# Run PHPUnit security tests
if (Test-Path "vendor/bin/phpunit") {
    Write-Host "Running security test suite..." -ForegroundColor Gray
    $testOutput = php artisan test --testsuite=Security 2>&1
    $testOutput | Out-File -FilePath $ReportFile -Append -Encoding UTF8
    
    if ($LASTEXITCODE -eq 0) {
        Check-Item "Security test suite" "pass" "All tests passed"
    } else {
        Check-Item "Security test suite" "fail" "Some tests failed - check report"
    }
} else {
    Check-Item "PHPUnit" "warn" "PHPUnit not found"
}

Write-Host ""
Write-Host "======================================" -ForegroundColor Cyan
Write-Host "4. Checking Security Headers" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""

# Check if app is running
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8000" -UseBasicParsing -TimeoutSec 5
    $headers = $response.Headers
    
    if ($headers["X-Content-Type-Options"]) {
        Check-Item "X-Content-Type-Options header" "pass"
    } else {
        Check-Item "X-Content-Type-Options header" "fail" "Header not found"
    }
    
    if ($headers["X-Frame-Options"]) {
        Check-Item "X-Frame-Options header" "pass"
    } else {
        Check-Item "X-Frame-Options header" "fail" "Header not found"
    }
    
    if ($headers["X-XSS-Protection"]) {
        Check-Item "X-XSS-Protection header" "pass"
    } else {
        Check-Item "X-XSS-Protection header" "fail" "Header not found"
    }
    
    if ($headers["Strict-Transport-Security"]) {
        Check-Item "HSTS header" "pass"
    } else {
        Check-Item "HSTS header" "warn" "HSTS not enabled (required for production)"
    }
    
    if ($headers["Content-Security-Policy"]) {
        Check-Item "CSP header" "pass"
    } else {
        Check-Item "CSP header" "warn" "CSP not configured"
    }
} catch {
    Check-Item "Application" "warn" "Application not running on localhost:8000"
}

Write-Host ""
Write-Host "======================================" -ForegroundColor Cyan
Write-Host "5. Checking File Security" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""

# Check for sensitive files in public directory
$sensitiveFiles = @(".env", ".git", "composer.json", "composer.lock")
foreach ($file in $sensitiveFiles) {
    if (Test-Path "public/$file") {
        Check-Item "Sensitive file in public: $file" "fail" "Remove from public directory"
    }
}

# Check for PHP files in storage
$phpFiles = Get-ChildItem -Path "storage" -Filter "*.php" -Recurse -ErrorAction SilentlyContinue
if ($phpFiles) {
    Check-Item "PHP files in storage" "warn" "PHP files found in storage directory"
} else {
    Check-Item "PHP files in storage" "pass" "No PHP files in storage"
}

Write-Host ""
Write-Host "======================================" -ForegroundColor Cyan
Write-Host "6. Checking Database Security" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""

# Check if using default database credentials
if (Test-Path ".env") {
    $envContent = Get-Content ".env" -Raw
    
    if ($envContent -match "DB_USERNAME=root" -and $envContent -match "DB_PASSWORD=\s*$") {
        Check-Item "Database credentials" "fail" "Using default root credentials"
    } elseif ($envContent -match "DB_USERNAME=root") {
        Check-Item "Database credentials" "warn" "Using root user"
    } else {
        Check-Item "Database credentials" "pass"
    }
}

Write-Host ""
Write-Host "======================================" -ForegroundColor Cyan
Write-Host "7. Checking Laravel Security" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""

# Check Laravel version
$laravelVersion = php artisan --version
"Laravel version: $laravelVersion" | Out-File -FilePath $ReportFile -Append -Encoding UTF8

# Check if maintenance mode is disabled
if (Test-Path "storage/framework/down") {
    Check-Item "Maintenance mode" "warn" "Application is in maintenance mode"
} else {
    Check-Item "Maintenance mode" "pass" "Application is not in maintenance mode"
}

# Check CSRF protection
if (Select-String -Path "app/Http/Kernel.php" -Pattern "VerifyCsrfToken" -Quiet) {
    Check-Item "CSRF protection" "pass" "VerifyCsrfToken middleware enabled"
} else {
    Check-Item "CSRF protection" "fail" "CSRF middleware not found"
}

Write-Host ""
Write-Host "======================================" -ForegroundColor Cyan
Write-Host "8. Checking Backup Configuration" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""

if (Test-Path "config/backup.php") {
    Check-Item "Backup configuration" "pass" "Backup package configured"
    
    # Check if backups are encrypted
    $backupConfig = Get-Content "config/backup.php" -Raw
    if ($backupConfig -match "BACKUP_ARCHIVE_PASSWORD") {
        $envContent = Get-Content ".env" -Raw
        if ($envContent -match "BACKUP_ARCHIVE_PASSWORD=") {
            Check-Item "Backup encryption" "pass" "Backups are encrypted"
        } else {
            Check-Item "Backup encryption" "warn" "Backup password not set"
        }
    }
} else {
    Check-Item "Backup configuration" "warn" "Backup not configured"
}

Write-Host ""
Write-Host "======================================" -ForegroundColor Cyan
Write-Host "Security Audit Complete" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Report saved to: $ReportFile" -ForegroundColor Green
Write-Host ""

# Summary
Write-Host "Summary:" -ForegroundColor Cyan
Write-Host "  Passed: $script:PassCount" -ForegroundColor Green
Write-Host "  Warnings: $script:WarnCount" -ForegroundColor Yellow
Write-Host "  Failed: $script:FailCount" -ForegroundColor Red
Write-Host ""

if ($script:FailCount -gt 0) {
    Write-Host "⚠ Critical security issues found! Review the report immediately." -ForegroundColor Red
    exit 1
} elseif ($script:WarnCount -gt 0) {
    Write-Host "⚠ Some security warnings found. Review recommended." -ForegroundColor Yellow
    exit 0
} else {
    Write-Host "✓ No critical security issues found." -ForegroundColor Green
    exit 0
}
