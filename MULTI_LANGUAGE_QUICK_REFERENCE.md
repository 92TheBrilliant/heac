# Multi-Language Quick Reference

## Quick Start

### Switch Language (Frontend)
Click the language dropdown in the navigation bar and select your preferred language.

### Add Translations (Admin)
1. Go to Pages or Research in Filament
2. Click the language tab (English/Arabic)
3. Enter content for that language
4. Save

## Code Examples

### Get Translated Content
```php
// Automatic (uses current locale)
$page->title

// Specific locale
$page->getTranslation('title', 'ar')

// Check if translation exists
$page->hasTranslation('title', 'ar')
```

### Set Locale
```php
// In controller
app()->setLocale('ar');

// Get current locale
app()->getLocale();
```

### Blade Templates
```blade
{{-- Current locale --}}
{{ $page->title }}

{{-- Specific locale --}}
{{ $page->getTranslation('title', 'ar') }}

{{-- Language switcher --}}
<x-language-switcher />
```

## Configuration

### Supported Languages
Edit `config/translatable.php`:
```php
'locales' => [
    'en' => 'English',
    'ar' => 'العربية',
],
```

### Translatable Fields

**Page Model:**
- title
- content
- excerpt
- meta_title
- meta_description

**Research Model:**
- title
- abstract

## Routes

| Route | Purpose |
|-------|---------|
| `/locale/{locale}` | Switch language |

## Components

| Component | Location | Purpose |
|-----------|----------|---------|
| `<x-language-switcher />` | `resources/views/components/language-switcher.blade.php` | Language dropdown |

## Middleware

| Middleware | Purpose |
|------------|---------|
| `SetLocale` | Sets application locale from session |

## Common Tasks

### Add New Language
1. Update `config/translatable.php`
2. Add tab in `PageForm.php` and `ResearchForm.php`
3. Clear cache: `php artisan config:clear`

### Test Translations
```bash
# In tinker
php artisan tinker

# Create test page
$page = Page::create([
    'title' => ['en' => 'Welcome', 'ar' => 'مرحبا'],
    'slug' => 'welcome',
    'status' => 'published'
]);

# Get translations
$page->getTranslation('title', 'en'); // "Welcome"
$page->getTranslation('title', 'ar'); // "مرحبا"
```

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## Troubleshooting

| Issue | Solution |
|-------|----------|
| Translation not showing | Check if translation exists with `hasTranslation()` |
| Language not switching | Clear session and cache |
| Admin tabs not showing | Clear view cache |
| Wrong language displayed | Check `app()->getLocale()` |

## Best Practices

✅ Always provide English content (fallback language)  
✅ Test with both languages before publishing  
✅ Use consistent field names across models  
✅ Clear cache after configuration changes  
✅ Validate important content is translated  

## Resources

- Full Guide: `MULTI_LANGUAGE_GUIDE.md`
- Implementation Details: `MULTI_LANGUAGE_IMPLEMENTATION_SUMMARY.md`
- Package Docs: https://github.com/spatie/laravel-translatable
