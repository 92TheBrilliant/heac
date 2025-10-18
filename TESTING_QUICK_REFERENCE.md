# Testing Quick Reference Guide

## Quick Commands

### Run All Tests
```bash
# Linux/macOS
bash scripts/run-all-tests.sh

# Windows
.\scripts\run-all-tests.ps1
```

### Run Specific Test Suites
```bash
# Unit tests
php artisan test --testsuite=Unit

# Feature tests
php artisan test --testsuite=Feature

# Performance tests
php artisan test --testsuite=Performance

# Security tests
php artisan test --testsuite=Security

# Browser tests
php artisan dusk

# Run all PHPUnit tests
php artisan test
```

### Performance Testing
```bash
# Linux/macOS
bash scripts/performance-test.sh http://localhost:8000

# Windows
.\scripts\performance-test.ps1 -Domain "http://localhost:8000"
```

### Security Audit
```bash
# Linux/macOS
bash scripts/security-audit.sh

# Windows
.\scripts\security-audit.ps1
```

---

## Test Files Location

```
heac-cms/
├── tests/
│   ├── Browser/
│   │   └── CrossBrowserTest.php          # Browser/UI tests
│   ├── Feature/
│   │   └── UserAcceptanceTest.php        # UAT tests
│   ├── Performance/
│   │   └── PerformanceTest.php           # Performance tests
│   └── Security/
│       └── SecurityTest.php              # Security tests
├── scripts/
│   ├── run-all-tests.sh                  # Run all tests (Linux/macOS)
│   ├── run-all-tests.ps1                 # Run all tests (Windows)
│   ├── performance-test.sh               # Performance testing (Linux/macOS)
│   ├── performance-test.ps1              # Performance testing (Windows)
│   ├── security-audit.sh                 # Security audit (Linux/macOS)
│   └── security-audit.ps1                # Security audit (Windows)
├── TESTING_GUIDE.md                      # Comprehensive testing guide
├── UAT_CHECKLIST.md                      # User acceptance testing checklist
└── TESTING_IMPLEMENTATION_SUMMARY.md     # Implementation summary
```

---

## Test Reports Location

```
storage/
├── test-reports/                         # General test reports
├── performance-reports/                  # Performance test reports
└── security-reports/                     # Security audit reports
```

---

## Before Running Tests

### Setup Requirements
```bash
# Install dependencies
composer install

# Install Dusk (for browser tests)
composer require --dev laravel/dusk
php artisan dusk:install

# Update ChromeDriver
php artisan dusk:chrome-driver

# Run migrations
php artisan migrate --env=testing

# Seed test data
php artisan db:seed --env=testing
```

### Environment Configuration
Ensure `.env.testing` is configured:
```env
APP_ENV=testing
DB_CONNECTION=mysql
DB_DATABASE=heac_cms_test
CACHE_DRIVER=array
SESSION_DRIVER=array
QUEUE_CONNECTION=sync
```

---

## Quick Test Checklist

### Before Deployment
- [ ] All automated tests pass
- [ ] Performance tests meet targets (< 3s load time)
- [ ] Security audit shows no critical issues
- [ ] Cross-browser testing completed
- [ ] UAT checklist completed and signed off

### After Deployment
- [ ] Smoke tests pass
- [ ] Performance monitoring active
- [ ] Error tracking configured
- [ ] Backup system verified

---

## Performance Targets

| Metric | Target |
|--------|--------|
| Homepage Load Time | < 500ms |
| Research Listing | < 800ms |
| Research Detail | < 600ms |
| Page Load Time | < 500ms |
| Search Response | < 1000ms |
| Database Queries | < 10 per page |
| Memory Usage | < 10MB per request |
| Google PageSpeed (Mobile) | 85+ |
| Google PageSpeed (Desktop) | 90+ |
| GTmetrix Grade | A |

---

## Security Checklist

- [ ] CSRF protection enabled
- [ ] XSS protection working
- [ ] SQL injection protection verified
- [ ] File upload restrictions enforced
- [ ] Rate limiting configured
- [ ] Security headers present
- [ ] HTTPS enforced (production)
- [ ] Passwords hashed
- [ ] Session security enabled
- [ ] Audit logging active

---

## Browser Support

| Browser | Minimum Version |
|---------|----------------|
| Chrome | Latest 2 versions |
| Firefox | Latest 2 versions |
| Safari | Latest 2 versions |
| Edge | Latest 2 versions |

---

## Device Testing

| Device Type | Screen Sizes |
|-------------|--------------|
| Mobile | 320px - 767px |
| Tablet | 768px - 1023px |
| Desktop | 1024px+ |

---

## Accessibility Standards

- WCAG 2.1 AA compliance
- Screen reader compatible
- Keyboard navigation support
- Color contrast ratios met
- Alt text on all images

---

## Common Issues & Solutions

### Issue: Tests failing with database errors
**Solution:**
```bash
php artisan migrate:fresh --env=testing
php artisan db:seed --env=testing
```

### Issue: Browser tests failing
**Solution:**
```bash
php artisan dusk:chrome-driver --detect
php artisan dusk:chrome-driver
```

### Issue: Performance tests showing slow times
**Solution:**
```bash
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Issue: Security tests failing
**Solution:**
```bash
composer update
php artisan config:clear
# Review .env security settings
```

---

## External Testing Tools

### Performance
- Google PageSpeed Insights: https://pagespeed.web.dev/
- GTmetrix: https://gtmetrix.com/
- WebPageTest: https://www.webpagetest.org/

### Security
- SSL Labs: https://www.ssllabs.com/ssltest/
- Security Headers: https://securityheaders.com/
- OWASP ZAP: https://www.zaproxy.org/

### Accessibility
- WAVE: https://wave.webaim.org/
- axe DevTools: Browser extension
- Lighthouse: Built into Chrome DevTools

---

## Continuous Integration

### GitHub Actions Example
```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.2
      - name: Install Dependencies
        run: composer install
      - name: Run Tests
        run: php artisan test
```

---

## Support

For detailed testing procedures, see:
- **TESTING_GUIDE.md** - Comprehensive testing guide
- **UAT_CHECKLIST.md** - User acceptance testing checklist
- **TESTING_IMPLEMENTATION_SUMMARY.md** - Implementation details

---

## Test Execution Time

Approximate execution times:
- Unit Tests: 1-2 minutes
- Feature Tests: 3-5 minutes
- Performance Tests: 5-10 minutes
- Security Tests: 2-3 minutes
- Browser Tests: 10-15 minutes
- **Total**: ~25-35 minutes for full test suite
