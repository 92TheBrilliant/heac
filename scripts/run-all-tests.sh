#!/bin/bash

# Run All Tests Script for HEAC CMS
# This script runs all test suites and generates a comprehensive report

echo "======================================"
echo "HEAC CMS - Running All Tests"
echo "======================================"
echo ""

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
OUTPUT_DIR="storage/test-reports"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
REPORT_FILE="${OUTPUT_DIR}/test_report_${TIMESTAMP}.txt"

# Create output directory
mkdir -p "$OUTPUT_DIR"

echo "Report will be saved to: $REPORT_FILE"
echo ""

# Start report
{
    echo "======================================"
    echo "HEAC CMS Test Report"
    echo "======================================"
    echo "Date: $(date)"
    echo ""
} > "$REPORT_FILE"

# Track test results
TOTAL_TESTS=0
PASSED_TESTS=0
FAILED_TESTS=0

# Function to run test suite
run_test_suite() {
    local suite_name=$1
    local command=$2
    
    echo -e "${BLUE}Running $suite_name...${NC}"
    {
        echo "======================================"
        echo "$suite_name"
        echo "======================================"
    } >> "$REPORT_FILE"
    
    if eval "$command" >> "$REPORT_FILE" 2>&1; then
        echo -e "${GREEN}✓ $suite_name passed${NC}"
        PASSED_TESTS=$((PASSED_TESTS + 1))
    else
        echo -e "${RED}✗ $suite_name failed${NC}"
        FAILED_TESTS=$((FAILED_TESTS + 1))
    fi
    
    TOTAL_TESTS=$((TOTAL_TESTS + 1))
    echo ""
}

echo "======================================"
echo "1. Unit Tests"
echo "======================================"
echo ""

run_test_suite "Unit Tests" "php artisan test --testsuite=Unit"

echo "======================================"
echo "2. Feature Tests"
echo "======================================"
echo ""

run_test_suite "Feature Tests" "php artisan test --testsuite=Feature"

echo "======================================"
echo "3. Performance Tests"
echo "======================================"
echo ""

run_test_suite "Performance Tests" "php artisan test --testsuite=Performance"

echo "======================================"
echo "4. Security Tests"
echo "======================================"
echo ""

run_test_suite "Security Tests" "php artisan test --testsuite=Security"

echo "======================================"
echo "5. Browser Tests (Dusk)"
echo "======================================"
echo ""

if [ -d "tests/Browser" ]; then
    run_test_suite "Browser Tests" "php artisan dusk"
else
    echo -e "${YELLOW}⚠ Browser tests not configured${NC}"
    echo ""
fi

echo "======================================"
echo "6. Code Quality Checks"
echo "======================================"
echo ""

# Run PHPStan (if installed)
if command -v phpstan &> /dev/null; then
    run_test_suite "PHPStan Analysis" "phpstan analyse"
else
    echo -e "${YELLOW}⚠ PHPStan not installed${NC}"
    echo ""
fi

# Run Laravel Pint (if installed)
if [ -f "vendor/bin/pint" ]; then
    echo -e "${BLUE}Running Laravel Pint...${NC}"
    {
        echo "======================================"
        echo "Laravel Pint - Code Style"
        echo "======================================"
    } >> "$REPORT_FILE"
    
    if ./vendor/bin/pint --test >> "$REPORT_FILE" 2>&1; then
        echo -e "${GREEN}✓ Code style check passed${NC}"
    else
        echo -e "${YELLOW}⚠ Code style issues found${NC}"
    fi
    echo ""
else
    echo -e "${YELLOW}⚠ Laravel Pint not installed${NC}"
    echo ""
fi

echo "======================================"
echo "7. Security Audit"
echo "======================================"
echo ""

if [ -f "scripts/security-audit.sh" ]; then
    bash scripts/security-audit.sh
else
    echo -e "${YELLOW}⚠ Security audit script not found${NC}"
    echo ""
fi

echo "======================================"
echo "8. Performance Testing"
echo "======================================"
echo ""

if [ -f "scripts/performance-test.sh" ]; then
    bash scripts/performance-test.sh
else
    echo -e "${YELLOW}⚠ Performance test script not found${NC}"
    echo ""
fi

echo ""
echo "======================================"
echo "Test Summary"
echo "======================================"
echo ""

{
    echo ""
    echo "======================================"
    echo "Test Summary"
    echo "======================================"
    echo "Total Test Suites: $TOTAL_TESTS"
    echo "Passed: $PASSED_TESTS"
    echo "Failed: $FAILED_TESTS"
    echo ""
} >> "$REPORT_FILE"

echo "Total Test Suites: $TOTAL_TESTS"
echo -e "${GREEN}Passed: $PASSED_TESTS${NC}"
echo -e "${RED}Failed: $FAILED_TESTS${NC}"
echo ""

if [ $FAILED_TESTS -eq 0 ]; then
    echo -e "${GREEN}✓ All tests passed!${NC}"
    EXIT_CODE=0
else
    echo -e "${RED}✗ Some tests failed. Review the report for details.${NC}"
    EXIT_CODE=1
fi

echo ""
echo -e "${GREEN}Full report saved to: $REPORT_FILE${NC}"
echo ""

# Generate HTML report (optional)
if command -v pandoc &> /dev/null; then
    HTML_REPORT="${OUTPUT_DIR}/test_report_${TIMESTAMP}.html"
    pandoc "$REPORT_FILE" -o "$HTML_REPORT" --standalone --metadata title="HEAC CMS Test Report"
    echo -e "${GREEN}HTML report generated: $HTML_REPORT${NC}"
    echo ""
fi

exit $EXIT_CODE
