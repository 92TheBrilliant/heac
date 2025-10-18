#!/bin/bash

# Security Audit Script for HEAC CMS
# This script runs various security checks and generates a report

echo "======================================"
echo "HEAC CMS Security Audit"
echo "======================================"
echo ""

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Configuration
OUTPUT_DIR="storage/security-reports"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
REPORT_FILE="${OUTPUT_DIR}/security_audit_${TIMESTAMP}.txt"

# Create output directory
mkdir -p "$OUTPUT_DIR"

echo "Report will be saved to: $REPORT_FILE"
echo ""

# Start report
{
    echo "======================================"
    echo "HEAC CMS Security Audit Report"
    echo "======================================"
    echo "Date: $(date)"
    echo ""
} > "$REPORT_FILE"

# Function to check and report
check_item() {
    local name=$1
    local status=$2
    local message=$3
    
    if [ "$status" = "pass" ]; then
        echo -e "${GREEN}✓${NC} $name"
    elif [ "$status" = "warn" ]; then
        echo -e "${YELLOW}⚠${NC} $name"
    else
        echo -e "${RED}✗${NC} $name"
    fi
    
    if [ -n "$message" ]; then
        echo "  $message"
    fi
    
    {
        echo "[$status] $name"
        if [ -n "$message" ]; then
            echo "  $message"
        fi
        echo ""
    } >> "$REPORT_FILE"
}

echo "======================================"
echo "1. Checking Dependencies"
echo "======================================"
echo ""

# Check for known vulnerabilities in dependencies
if command -v composer &> /dev/null; then
    echo "Running composer audit..."
    if composer audit --no-interaction >> "$REPORT_FILE" 2>&1; then
        check_item "Composer dependencies" "pass" "No known vulnerabilities found"
    else
        check_item "Composer dependencies" "fail" "Vulnerabilities found - check report"
    fi
else
    check_item "Composer" "fail" "Composer not found"
fi

echo ""
echo "======================================"
echo "2. Checking Configuration"
echo "======================================"
echo ""

# Check .env file
if [ -f ".env" ]; then
    check_item ".env file exists" "pass"
    
    # Check if APP_DEBUG is false in production
    if grep -q "APP_DEBUG=false" .env; then
        check_item "APP_DEBUG disabled" "pass"
    else
        check_item "APP_DEBUG disabled" "warn" "Debug mode should be disabled in production"
    fi
    
    # Check if APP_KEY is set
    if grep -q "APP_KEY=base64:" .env; then
        check_item "APP_KEY is set" "pass"
    else
        check_item "APP_KEY is set" "fail" "APP_KEY must be generated"
    fi
    
    # Check if default passwords are changed
    if grep -q "DB_PASSWORD=password" .env || grep -q "DB_PASSWORD=secret" .env; then
        check_item "Database password" "fail" "Default password detected"
    else
        check_item "Database password" "pass"
    fi
else
    check_item ".env file" "fail" ".env file not found"
fi

# Check file permissions
echo ""
echo "Checking file permissions..."

if [ -d "storage" ]; then
    STORAGE_PERMS=$(stat -c "%a" storage 2>/dev/null || stat -f "%A" storage 2>/dev/null)
    if [ "$STORAGE_PERMS" = "755" ] || [ "$STORAGE_PERMS" = "775" ]; then
        check_item "Storage directory permissions" "pass" "Permissions: $STORAGE_PERMS"
    else
        check_item "Storage directory permissions" "warn" "Permissions: $STORAGE_PERMS (should be 755 or 775)"
    fi
fi

if [ -f ".env" ]; then
    ENV_PERMS=$(stat -c "%a" .env 2>/dev/null || stat -f "%A" .env 2>/dev/null)
    if [ "$ENV_PERMS" = "600" ] || [ "$ENV_PERMS" = "644" ]; then
        check_item ".env file permissions" "pass" "Permissions: $ENV_PERMS"
    else
        check_item ".env file permissions" "warn" "Permissions: $ENV_PERMS (should be 600 or 644)"
    fi
fi

echo ""
echo "======================================"
echo "3. Running Security Tests"
echo "======================================"
echo ""

# Run PHPUnit security tests
if [ -f "vendor/bin/phpunit" ]; then
    echo "Running security test suite..."
    php artisan test --testsuite=Security >> "$REPORT_FILE" 2>&1
    
    if [ $? -eq 0 ]; then
        check_item "Security test suite" "pass" "All tests passed"
    else
        check_item "Security test suite" "fail" "Some tests failed - check report"
    fi
else
    check_item "PHPUnit" "warn" "PHPUnit not found"
fi

echo ""
echo "======================================"
echo "4. Checking Security Headers"
echo "======================================"
echo ""

# Check if app is running
if curl -s -I http://localhost:8000 > /dev/null 2>&1; then
    HEADERS=$(curl -s -I http://localhost:8000)
    
    if echo "$HEADERS" | grep -q "X-Content-Type-Options"; then
        check_item "X-Content-Type-Options header" "pass"
    else
        check_item "X-Content-Type-Options header" "fail" "Header not found"
    fi
    
    if echo "$HEADERS" | grep -q "X-Frame-Options"; then
        check_item "X-Frame-Options header" "pass"
    else
        check_item "X-Frame-Options header" "fail" "Header not found"
    fi
    
    if echo "$HEADERS" | grep -q "X-XSS-Protection"; then
        check_item "X-XSS-Protection header" "pass"
    else
        check_item "X-XSS-Protection header" "fail" "Header not found"
    fi
    
    if echo "$HEADERS" | grep -q "Strict-Transport-Security"; then
        check_item "HSTS header" "pass"
    else
        check_item "HSTS header" "warn" "HSTS not enabled (required for production)"
    fi
    
    if echo "$HEADERS" | grep -q "Content-Security-Policy"; then
        check_item "CSP header" "pass"
    else
        check_item "CSP header" "warn" "CSP not configured"
    fi
else
    check_item "Application" "warn" "Application not running on localhost:8000"
fi

echo ""
echo "======================================"
echo "5. Checking File Security"
echo "======================================"
echo ""

# Check for sensitive files in public directory
SENSITIVE_FILES=(".env" ".git" "composer.json" "composer.lock")
for file in "${SENSITIVE_FILES[@]}"; do
    if [ -f "public/$file" ]; then
        check_item "Sensitive file in public: $file" "fail" "Remove from public directory"
    fi
done

# Check for PHP files in storage
if find storage -name "*.php" -type f | grep -q .; then
    check_item "PHP files in storage" "warn" "PHP files found in storage directory"
else
    check_item "PHP files in storage" "pass" "No PHP files in storage"
fi

# Check for executable files in uploads
if [ -d "storage/app/public" ]; then
    if find storage/app/public -type f -executable | grep -q .; then
        check_item "Executable files in uploads" "fail" "Executable files found"
    else
        check_item "Executable files in uploads" "pass"
    fi
fi

echo ""
echo "======================================"
echo "6. Checking Database Security"
echo "======================================"
echo ""

# Check if using default database credentials
if [ -f ".env" ]; then
    if grep -q "DB_USERNAME=root" .env && grep -q "DB_PASSWORD=$" .env; then
        check_item "Database credentials" "fail" "Using default root credentials"
    elif grep -q "DB_USERNAME=root" .env; then
        check_item "Database credentials" "warn" "Using root user"
    else
        check_item "Database credentials" "pass"
    fi
fi

echo ""
echo "======================================"
echo "7. Checking Laravel Security"
echo "======================================"
echo ""

# Check Laravel version
LARAVEL_VERSION=$(php artisan --version | grep -oP '\d+\.\d+\.\d+')
echo "Laravel version: $LARAVEL_VERSION" >> "$REPORT_FILE"

# Check if maintenance mode is disabled
if php artisan down --help > /dev/null 2>&1; then
    if [ -f "storage/framework/down" ]; then
        check_item "Maintenance mode" "warn" "Application is in maintenance mode"
    else
        check_item "Maintenance mode" "pass" "Application is not in maintenance mode"
    fi
fi

# Check CSRF protection
if grep -q "VerifyCsrfToken" app/Http/Kernel.php; then
    check_item "CSRF protection" "pass" "VerifyCsrfToken middleware enabled"
else
    check_item "CSRF protection" "fail" "CSRF middleware not found"
fi

echo ""
echo "======================================"
echo "8. Checking Backup Configuration"
echo "======================================"
echo ""

if [ -f "config/backup.php" ]; then
    check_item "Backup configuration" "pass" "Backup package configured"
    
    # Check if backups are encrypted
    if grep -q "'password' => env('BACKUP_ARCHIVE_PASSWORD')" config/backup.php; then
        if grep -q "BACKUP_ARCHIVE_PASSWORD=" .env; then
            check_item "Backup encryption" "pass" "Backups are encrypted"
        else
            check_item "Backup encryption" "warn" "Backup password not set"
        fi
    fi
else
    check_item "Backup configuration" "warn" "Backup not configured"
fi

echo ""
echo "======================================"
echo "Security Audit Complete"
echo "======================================"
echo ""
echo -e "${GREEN}Report saved to: $REPORT_FILE${NC}"
echo ""

# Count issues
FAIL_COUNT=$(grep -c "^\[fail\]" "$REPORT_FILE" || echo "0")
WARN_COUNT=$(grep -c "^\[warn\]" "$REPORT_FILE" || echo "0")
PASS_COUNT=$(grep -c "^\[pass\]" "$REPORT_FILE" || echo "0")

echo "Summary:"
echo -e "  ${GREEN}Passed: $PASS_COUNT${NC}"
echo -e "  ${YELLOW}Warnings: $WARN_COUNT${NC}"
echo -e "  ${RED}Failed: $FAIL_COUNT${NC}"
echo ""

if [ "$FAIL_COUNT" -gt 0 ]; then
    echo -e "${RED}⚠ Critical security issues found! Review the report immediately.${NC}"
    exit 1
elif [ "$WARN_COUNT" -gt 0 ]; then
    echo -e "${YELLOW}⚠ Some security warnings found. Review recommended.${NC}"
    exit 0
else
    echo -e "${GREEN}✓ No critical security issues found.${NC}"
    exit 0
fi
