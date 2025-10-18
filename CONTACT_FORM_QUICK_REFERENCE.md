# Contact Form Quick Reference

## How It Works

### User Flow
1. User visits `/contact`
2. Fills out the form (name, email, phone, subject, message)
3. Submits the form
4. System validates and checks for spam
5. If valid, inquiry is saved and emails are sent
6. User sees success message

### Spam Protection Flow
```
Form Submission
    ↓
Honeypot Middleware (checks hidden fields & timing)
    ↓
Rate Limit Middleware (checks submission count)
    ↓
Form Validation (validates input)
    ↓
Save to Database
    ↓
Send Emails (admin + auto-reply)
    ↓
Success Message
```

## Key Components

### 1. Form Request Validation
**File:** `app/Http/Requests/ContactFormRequest.php`
- Validates all form fields
- Custom error messages
- Email format validation with DNS check
- Phone number regex validation

### 2. Honeypot Protection
**File:** `app/Http/Middleware/HoneypotProtection.php`
- Checks hidden `website` field (should be empty)
- Checks `form_load_time` (should be > 3 seconds)
- Silently rejects spam
- Logs spam attempts

### 3. Rate Limiting
**File:** `app/Http/Middleware/ContactFormRateLimit.php`
- 3 submissions per hour per IP
- Shows user-friendly error message
- Logs rate limit violations

### 4. Email Notifications
**Files:** 
- `app/Mail/ContactInquiryNotification.php` (admin)
- `app/Mail/ContactInquiryAutoReply.php` (user)

## Configuration

### Environment Variables
```env
# Email Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@heac.org
MAIL_FROM_NAME="HEAC"

# Admin Email for Contact Form
MAIL_ADMIN_EMAIL=admin@heac.org
```

### Adjustable Settings

#### Rate Limiting
In `app/Http/Middleware/ContactFormRateLimit.php`:
```php
$maxAttempts = 3;        // Change number of allowed submissions
$decayMinutes = 60;      // Change time window in minutes
```

#### Honeypot Timing
In `app/Http/Middleware/HoneypotProtection.php`:
```php
if ($timeTaken < 3) {    // Change minimum seconds required
```

## Testing

### Test Valid Submission
```bash
curl -X POST http://localhost/contact \
  -d "name=John Doe" \
  -d "email=john@example.com" \
  -d "message=Test message" \
  -d "form_load_time=$(date +%s)"
```

### Test Rate Limiting
Submit the form 4 times within an hour - the 4th should fail.

### Test Honeypot
Submit with the hidden `website` field filled - should silently reject.

### Test Email Delivery
Check that both emails are sent:
1. Admin notification to `MAIL_ADMIN_EMAIL`
2. Auto-reply to submitter's email

## Troubleshooting

### Emails Not Sending
1. Check `.env` mail configuration
2. Verify `MAIL_ADMIN_EMAIL` is set
3. Check Laravel logs: `storage/logs/laravel.log`
4. Test mail config: `php artisan tinker` then `Mail::raw('Test', function($m) { $m->to('test@example.com')->subject('Test'); });`

### Rate Limiting Too Strict
Adjust `$maxAttempts` and `$decayMinutes` in `ContactFormRateLimit.php`

### Too Much Spam
1. Check spam logs in `storage/logs/laravel.log`
2. Consider reducing honeypot timing threshold
3. Consider adding Google reCAPTCHA

### Form Not Submitting
1. Check browser console for JavaScript errors
2. Verify CSRF token is present
3. Check validation errors in response
4. Verify middleware is registered in `bootstrap/app.php`

## Monitoring

### Check Spam Attempts
```bash
grep "Honeypot spam detected" storage/logs/laravel.log
grep "Fast submission detected" storage/logs/laravel.log
```

### Check Rate Limit Hits
```bash
grep "Contact form rate limit exceeded" storage/logs/laravel.log
```

### View Contact Inquiries
Admin panel: `/admin/contact-inquiries`

## Security Best Practices

✓ CSRF protection enabled
✓ Input validation and sanitization
✓ Rate limiting per IP
✓ Honeypot spam protection
✓ Time-based submission check
✓ Email validation with DNS check
✓ Logging of suspicious activity
✓ IP address tracking

## Maintenance

### Regular Tasks
1. Monitor spam logs weekly
2. Review rate limit effectiveness
3. Check email delivery success rate
4. Update spam protection rules as needed
5. Clean up old inquiries (consider archiving after 90 days)

### Performance
- Emails are sent synchronously (consider queuing for better performance)
- Rate limiting uses Redis (ensure Redis is running)
- Consider adding queue workers for email sending

## Future Enhancements

Consider adding:
- [ ] Google reCAPTCHA v3
- [ ] Queue email sending for better performance
- [ ] File attachment support
- [ ] Multi-language support
- [ ] Custom form fields per department
- [ ] Integration with CRM system
- [ ] SMS notifications for urgent inquiries
