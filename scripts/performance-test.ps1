# Performance Testing Script for HEAC CMS (PowerShell)
# This script runs various performance tests and generates a report

param(
    [string]$Domain = "http://localhost:8000",
    [int]$Requests = 100,
    [int]$Concurrency = 10
)

Write-Host "======================================" -ForegroundColor Cyan
Write-Host "HEAC CMS Performance Testing" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""

# Configuration
$OutputDir = "storage/performance-reports"
$Timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
$ReportFile = "$OutputDir/performance_report_$Timestamp.txt"

# Create output directory
if (-not (Test-Path $OutputDir)) {
    New-Item -ItemType Directory -Path $OutputDir | Out-Null
}

Write-Host "Testing domain: $Domain" -ForegroundColor Yellow
Write-Host "Report will be saved to: $ReportFile" -ForegroundColor Yellow
Write-Host ""

# Start report
@"
======================================
HEAC CMS Performance Test Report
======================================
Date: $(Get-Date)
Domain: $Domain

"@ | Out-File -FilePath $ReportFile -Encoding UTF8

# Function to test URL
function Test-Url {
    param(
        [string]$Url,
        [string]$Name,
        [int]$Count = 10
    )
    
    Write-Host "Testing: $Name" -ForegroundColor Yellow
    Write-Host "URL: $Url"
    
    @"
======================================
Test: $Name
URL: $Url
======================================
"@ | Out-File -FilePath $ReportFile -Append -Encoding UTF8
    
    $times = @()
    $failed = 0
    
    for ($i = 1; $i -le $Count; $i++) {
        try {
            $sw = [System.Diagnostics.Stopwatch]::StartNew()
            $response = Invoke-WebRequest -Uri $Url -UseBasicParsing -TimeoutSec 30
            $sw.Stop()
            
            if ($response.StatusCode -eq 200) {
                $times += $sw.ElapsedMilliseconds
                Write-Progress -Activity "Testing $Name" -Status "Request $i of $Count" -PercentComplete (($i / $Count) * 100)
            } else {
                $failed++
            }
        } catch {
            $failed++
            Write-Host "  Request $i failed: $_" -ForegroundColor Red
        }
    }
    
    Write-Progress -Activity "Testing $Name" -Completed
    
    if ($times.Count -gt 0) {
        $avgTime = ($times | Measure-Object -Average).Average
        $minTime = ($times | Measure-Object -Minimum).Minimum
        $maxTime = ($times | Measure-Object -Maximum).Maximum
        
        Write-Host "  Average time: $([math]::Round($avgTime, 2))ms" -ForegroundColor Green
        Write-Host "  Min time: $([math]::Round($minTime, 2))ms"
        Write-Host "  Max time: $([math]::Round($maxTime, 2))ms"
        Write-Host "  Failed requests: $failed"
        
        @"
Average time: $([math]::Round($avgTime, 2))ms
Min time: $([math]::Round($minTime, 2))ms
Max time: $([math]::Round($maxTime, 2))ms
Failed requests: $failed
Successful requests: $($times.Count)

"@ | Out-File -FilePath $ReportFile -Append -Encoding UTF8
    } else {
        Write-Host "  All requests failed!" -ForegroundColor Red
        "All requests failed!`n" | Out-File -FilePath $ReportFile -Append -Encoding UTF8
    }
    
    Write-Host ""
}

# Function to check page size
function Get-PageSize {
    param(
        [string]$Url,
        [string]$Name
    )
    
    Write-Host "Checking page size: $Name" -ForegroundColor Yellow
    
    try {
        $response = Invoke-WebRequest -Uri $Url -UseBasicParsing
        $sizeKB = [math]::Round($response.RawContentLength / 1KB, 2)
        
        Write-Host "  Size: ${sizeKB}KB" -ForegroundColor Green
        
        @"
Page Size: $Name
  URL: $Url
  Size: ${sizeKB}KB

"@ | Out-File -FilePath $ReportFile -Append -Encoding UTF8
        
        if ($sizeKB -lt 2048) {
            Write-Host "  ✓ Page size is acceptable" -ForegroundColor Green
        } else {
            Write-Host "  ⚠ Page size is large (>2MB)" -ForegroundColor Yellow
        }
    } catch {
        Write-Host "  Failed to get page size: $_" -ForegroundColor Red
    }
    
    Write-Host ""
}

# Run tests
Write-Host "======================================" -ForegroundColor Cyan
Write-Host "Running Load Tests" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""

Test-Url -Url "$Domain/" -Name "Homepage" -Count 50
Test-Url -Url "$Domain/research" -Name "Research Listing" -Count 30
Test-Url -Url "$Domain/contact" -Name "Contact Page" -Count 20
Test-Url -Url "$Domain/research?search=test" -Name "Research Search" -Count 20

Write-Host "======================================" -ForegroundColor Cyan
Write-Host "Checking Page Sizes" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""

Get-PageSize -Url "$Domain/" -Name "Homepage"
Get-PageSize -Url "$Domain/research" -Name "Research Listing"
Get-PageSize -Url "$Domain/contact" -Name "Contact Page"

Write-Host "======================================" -ForegroundColor Cyan
Write-Host "Running PHPUnit Performance Tests" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""

# Run PHPUnit performance tests
php artisan test --testsuite=Performance | Out-File -FilePath $ReportFile -Append -Encoding UTF8

Write-Host ""
Write-Host "======================================" -ForegroundColor Cyan
Write-Host "Performance Test Complete" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Report saved to: $ReportFile" -ForegroundColor Green
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "1. Review the full report at: $ReportFile"
Write-Host "2. Test with Google PageSpeed Insights: https://pagespeed.web.dev/"
Write-Host "3. Test with GTmetrix: https://gtmetrix.com/"
Write-Host "4. Monitor application with Laravel Telescope"
Write-Host ""
