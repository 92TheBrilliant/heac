# Run All Tests Script for HEAC CMS (PowerShell)
# This script runs all test suites and generates a comprehensive report

Write-Host "======================================" -ForegroundColor Cyan
Write-Host "HEAC CMS - Running All Tests" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""

# Configuration
$OutputDir = "storage/test-reports"
$Timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
$ReportFile = "$OutputDir/test_report_$Timestamp.txt"

# Create output directory
if (-not (Test-Path $OutputDir)) {
    New-Item -ItemType Directory -Path $OutputDir | Out-Null
}

Write-Host "Report will be saved to: $ReportFile" -ForegroundColor Yellow
Write-Host ""

# Start report
@"
======================================
HEAC CMS Test Report
======================================
Date: $(Get-Date)

"@ | Out-File -FilePath $ReportFile -Encoding UTF8

# Track test results
$script:TotalTests = 0
$script:PassedTests = 0
$script:FailedTests = 0

# Function to run test suite
function Run-TestSuite {
    param(
        [string]$SuiteName,
        [scriptblock]$Command
    )
    
    Write-Host "Running $SuiteName..." -ForegroundColor Blue
    
    @"
======================================
$SuiteName
======================================
"@ | Out-File -FilePath $ReportFile -Append -Encoding UTF8
    
    try {
        $output = & $Command 2>&1
        $output | Out-File -FilePath $ReportFile -Append -Encoding UTF8
        
        if ($LASTEXITCODE -eq 0) {
            Write-Host "✓ $SuiteName passed" -ForegroundColor Green
            $script:PassedTests++
        } else {
            Write-Host "✗ $SuiteName failed" -ForegroundColor Red
            $script:FailedTests++
        }
    } catch {
        Write-Host "✗ $SuiteName failed: $_" -ForegroundColor Red
        $_ | Out-File -FilePath $ReportFile -Append -Encoding UTF8
        $script:FailedTests++
    }
    
    $script:TotalTests++
    Write-Host ""
}

Write-Host "======================================" -ForegroundColor Cyan
Write-Host "1. Unit Tests" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""

Run-TestSuite "Unit Tests" { php artisan test --testsuite=Unit }

Write-Host "======================================" -ForegroundColor Cyan
Write-Host "2. Feature Tests" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""

Run-TestSuite "Feature Tests" { php artisan test --testsuite=Feature }

Write-Host "======================================" -ForegroundColor Cyan
Write-Host "3. Performance Tests" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""

Run-TestSuite "Performance Tests" { php artisan test --testsuite=Performance }

Write-Host "======================================" -ForegroundColor Cyan
Write-Host "4. Security Tests" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""

Run-TestSuite "Security Tests" { php artisan test --testsuite=Security }

Write-Host "======================================" -ForegroundColor Cyan
Write-Host "5. Browser Tests (Dusk)" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""

if (Test-Path "tests/Browser") {
    Run-TestSuite "Browser Tests" { php artisan dusk }
} else {
    Write-Host "⚠ Browser tests not configured" -ForegroundColor Yellow
    Write-Host ""
}

Write-Host "======================================" -ForegroundColor Cyan
Write-Host "6. Code Quality Checks" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""

# Run PHPStan (if installed)
if (Get-Command phpstan -ErrorAction SilentlyContinue) {
    Run-TestSuite "PHPStan Analysis" { phpstan analyse }
} else {
    Write-Host "⚠ PHPStan not installed" -ForegroundColor Yellow
    Write-Host ""
}

# Run Laravel Pint (if installed)
if (Test-Path "vendor/bin/pint") {
    Write-Host "Running Laravel Pint..." -ForegroundColor Blue
    
    @"
======================================
Laravel Pint - Code Style
======================================
"@ | Out-File -FilePath $ReportFile -Append -Encoding UTF8
    
    $pintOutput = ./vendor/bin/pint --test 2>&1
    $pintOutput | Out-File -FilePath $ReportFile -Append -Encoding UTF8
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✓ Code style check passed" -ForegroundColor Green
    } else {
        Write-Host "⚠ Code style issues found" -ForegroundColor Yellow
    }
    Write-Host ""
} else {
    Write-Host "⚠ Laravel Pint not installed" -ForegroundColor Yellow
    Write-Host ""
}

Write-Host "======================================" -ForegroundColor Cyan
Write-Host "7. Security Audit" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""

if (Test-Path "scripts/security-audit.ps1") {
    & "scripts/security-audit.ps1"
} else {
    Write-Host "⚠ Security audit script not found" -ForegroundColor Yellow
    Write-Host ""
}

Write-Host "======================================" -ForegroundColor Cyan
Write-Host "8. Performance Testing" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""

if (Test-Path "scripts/performance-test.ps1") {
    & "scripts/performance-test.ps1"
} else {
    Write-Host "⚠ Performance test script not found" -ForegroundColor Yellow
    Write-Host ""
}

Write-Host ""
Write-Host "======================================" -ForegroundColor Cyan
Write-Host "Test Summary" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""

@"

======================================
Test Summary
======================================
Total Test Suites: $script:TotalTests
Passed: $script:PassedTests
Failed: $script:FailedTests

"@ | Out-File -FilePath $ReportFile -Append -Encoding UTF8

Write-Host "Total Test Suites: $script:TotalTests"
Write-Host "Passed: $script:PassedTests" -ForegroundColor Green
Write-Host "Failed: $script:FailedTests" -ForegroundColor Red
Write-Host ""

if ($script:FailedTests -eq 0) {
    Write-Host "✓ All tests passed!" -ForegroundColor Green
    $ExitCode = 0
} else {
    Write-Host "✗ Some tests failed. Review the report for details." -ForegroundColor Red
    $ExitCode = 1
}

Write-Host ""
Write-Host "Full report saved to: $ReportFile" -ForegroundColor Green
Write-Host ""

exit $ExitCode
