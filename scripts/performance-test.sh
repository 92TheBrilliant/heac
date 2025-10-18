#!/bin/bash

# Performance Testing Script for HEAC CMS
# This script runs various performance tests and generates a report

echo "======================================"
echo "HEAC CMS Performance Testing"
echo "======================================"
echo ""

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Configuration
DOMAIN="${1:-http://localhost:8000}"
OUTPUT_DIR="storage/performance-reports"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
REPORT_FILE="${OUTPUT_DIR}/performance_report_${TIMESTAMP}.txt"

# Create output directory
mkdir -p "$OUTPUT_DIR"

echo "Testing domain: $DOMAIN"
echo "Report will be saved to: $REPORT_FILE"
echo ""

# Start report
{
    echo "======================================"
    echo "HEAC CMS Performance Test Report"
    echo "======================================"
    echo "Date: $(date)"
    echo "Domain: $DOMAIN"
    echo ""
} > "$REPORT_FILE"

# Function to test URL with Apache Bench
test_url() {
    local url=$1
    local name=$2
    local requests=${3:-100}
    local concurrency=${4:-10}
    
    echo -e "${YELLOW}Testing: $name${NC}"
    echo "URL: $url"
    echo "Requests: $requests, Concurrency: $concurrency"
    
    {
        echo "======================================"
        echo "Test: $name"
        echo "URL: $url"
        echo "======================================"
    } >> "$REPORT_FILE"
    
    # Run Apache Bench
    ab -n "$requests" -c "$concurrency" -g "${OUTPUT_DIR}/gnuplot_${name// /_}.tsv" "$url" >> "$REPORT_FILE" 2>&1
    
    # Extract key metrics
    local time_per_request=$(grep "Time per request:" "$REPORT_FILE" | tail -1 | awk '{print $4}')
    local requests_per_second=$(grep "Requests per second:" "$REPORT_FILE" | tail -1 | awk '{print $4}')
    local failed_requests=$(grep "Failed requests:" "$REPORT_FILE" | tail -1 | awk '{print $3}')
    
    echo "  Time per request: ${time_per_request}ms"
    echo "  Requests per second: $requests_per_second"
    echo "  Failed requests: $failed_requests"
    
    if [ "$failed_requests" -eq 0 ]; then
        echo -e "  ${GREEN}✓ No failed requests${NC}"
    else
        echo -e "  ${RED}✗ $failed_requests failed requests${NC}"
    fi
    
    echo ""
}

# Check if Apache Bench is installed
if ! command -v ab &> /dev/null; then
    echo -e "${RED}Error: Apache Bench (ab) is not installed${NC}"
    echo "Install it with: sudo apt-get install apache2-utils (Ubuntu/Debian)"
    echo "or: brew install httpd (macOS)"
    exit 1
fi

# Run tests
echo "======================================"
echo "Running Load Tests"
echo "======================================"
echo ""

test_url "${DOMAIN}/" "Homepage" 1000 10
test_url "${DOMAIN}/research" "Research Listing" 500 5
test_url "${DOMAIN}/contact" "Contact Page" 200 5

# Test with search query
test_url "${DOMAIN}/research?search=test" "Research Search" 200 5

echo ""
echo "======================================"
echo "Running PHPUnit Performance Tests"
echo "======================================"
echo ""

# Run PHPUnit performance tests
php artisan test --testsuite=Performance >> "$REPORT_FILE" 2>&1

echo ""
echo "======================================"
echo "Checking Page Size"
echo "======================================"
echo ""

# Function to check page size
check_page_size() {
    local url=$1
    local name=$2
    
    echo "Checking: $name"
    
    # Get page size
    local size=$(curl -s -w '%{size_download}' -o /dev/null "$url")
    local size_kb=$((size / 1024))
    
    echo "  Size: ${size_kb}KB"
    
    {
        echo "Page Size: $name"
        echo "  URL: $url"
        echo "  Size: ${size_kb}KB"
        echo ""
    } >> "$REPORT_FILE"
    
    if [ "$size_kb" -lt 2048 ]; then
        echo -e "  ${GREEN}✓ Page size is acceptable${NC}"
    else
        echo -e "  ${YELLOW}⚠ Page size is large (>${size_kb}KB)${NC}"
    fi
    
    echo ""
}

check_page_size "${DOMAIN}/" "Homepage"
check_page_size "${DOMAIN}/research" "Research Listing"
check_page_size "${DOMAIN}/contact" "Contact Page"

echo ""
echo "======================================"
echo "Database Query Analysis"
echo "======================================"
echo ""

# Enable query logging and check for slow queries
php artisan tinker --execute="
    DB::enableQueryLog();
    \$response = app()->handle(Request::create('/research'));
    \$queries = DB::getQueryLog();
    echo 'Total queries: ' . count(\$queries) . PHP_EOL;
    foreach (\$queries as \$query) {
        if (\$query['time'] > 100) {
            echo 'Slow query (' . \$query['time'] . 'ms): ' . \$query['query'] . PHP_EOL;
        }
    }
" >> "$REPORT_FILE" 2>&1

echo ""
echo "======================================"
echo "Cache Performance"
echo "======================================"
echo ""

# Test cache hit rate
{
    echo "Cache Statistics:"
    php artisan tinker --execute="
        if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
            \$redis = Cache::getStore()->connection();
            \$info = \$redis->info('stats');
            echo 'Keyspace hits: ' . (\$info['keyspace_hits'] ?? 'N/A') . PHP_EOL;
            echo 'Keyspace misses: ' . (\$info['keyspace_misses'] ?? 'N/A') . PHP_EOL;
            if (isset(\$info['keyspace_hits']) && isset(\$info['keyspace_misses'])) {
                \$total = \$info['keyspace_hits'] + \$info['keyspace_misses'];
                if (\$total > 0) {
                    \$hitRate = (\$info['keyspace_hits'] / \$total) * 100;
                    echo 'Hit rate: ' . round(\$hitRate, 2) . '%' . PHP_EOL;
                }
            }
        } else {
            echo 'Redis cache not configured' . PHP_EOL;
        }
    "
} >> "$REPORT_FILE" 2>&1

echo ""
echo "======================================"
echo "Performance Test Complete"
echo "======================================"
echo ""
echo -e "${GREEN}Report saved to: $REPORT_FILE${NC}"
echo ""
echo "Summary:"
grep -E "(Time per request|Requests per second|Failed requests)" "$REPORT_FILE" | tail -12
echo ""
echo "Next steps:"
echo "1. Review the full report at: $REPORT_FILE"
echo "2. Test with Google PageSpeed Insights: https://pagespeed.web.dev/"
echo "3. Test with GTmetrix: https://gtmetrix.com/"
echo "4. Monitor application with Laravel Telescope"
echo ""
