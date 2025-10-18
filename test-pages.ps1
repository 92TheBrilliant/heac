# Test All Pages Script
Write-Host "Testing HEAC Website Pages..." -ForegroundColor Green
Write-Host ""

$baseUrl = "http://127.0.0.1:8080"
$pages = @(
    @{Name="Homepage"; Url="/"}
    @{Name="About"; Url="/about"}
    @{Name="Services"; Url="/services"}
    @{Name="Training"; Url="/training"}
    @{Name="Team"; Url="/team"}
    @{Name="Research"; Url="/research"}
    @{Name="Contact"; Url="/contact"}
)

foreach ($page in $pages) {
    try {
        $url = $baseUrl + $page.Url
        $response = Invoke-WebRequest -Uri $url -Method GET -TimeoutSec 10 -UseBasicParsing
        if ($response.StatusCode -eq 200) {
            Write-Host "OK $($page.Name) - $($page.Url)" -ForegroundColor Green
        } else {
            Write-Host "FAIL $($page.Name) - Status: $($response.StatusCode)" -ForegroundColor Red
        }
    } catch {
        Write-Host "ERROR $($page.Name) - $($_.Exception.Message)" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "Test complete!" -ForegroundColor Cyan
