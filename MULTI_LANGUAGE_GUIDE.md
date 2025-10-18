# Multi-Language Support Guide

## Overview

The HEAC CMS includes multi-language support using the Spatie Laravel Translatable package. This allows content to be managed in multiple languages (currently English and Arabic) with easy expansion to additional languages.

## Features

- **Translatable Models**: Page and Research models support translations
- **Language Switcher**: Frontend component for users to switch between languages
- **Admin Panel**: Filament resources with translation tabs for easy content management
- **Automatic Fallback**: Falls back to default language (English) when translation is missing

## Supported Languages

Currently configured languages:
- **English (en)**: Default language
- **Arabic (ar)**: Secondary language

## Configuration

### Language Configuration

Languages are configured in `config/translatable.php`:

```php
'locales' => [
    'en' => 'English',
    'ar' => 'العربية',
],
```

### Adding New Languages

To add a new language:

1. Update `config/translatable.php`:
```php
'locales' => [
    'en' => 'English',
    'ar' => 'العربية',
    'fr' => 'Français', // New language
],
```

2. Add a new tab in Filament resources (PageForm.php, ResearchForm.php):
```php
Tabs\Tab::make('French')
    ->schema([
        TextInput::make('title.fr')
            ->label('Title')
            ->maxLength(255),
        // ... other fields
    ]),
```

## Translatable Fields

### Page Model
- `title`
- `content`
- `excerpt`
- `meta_title`
- `meta_description`

### Research Model
- `title`
- `abstract`

## Usage

### In Blade Templates

Get translated content:
```blade
{{ $page->title }} {{-- Automatically uses current locale --}}
{{ $page->getTranslation('title', 'ar') }} {{-- Get specific locale --}}
```

### In Controllers

```php
// Set locale
app()->setLocale('ar');

// Get translated content
$page->title; // Returns Arabic title if available

// Get specific translation
$page->getTranslation('title', 'en');

// Check if translation exists
$page->hasTranslation('title', 'ar');
```

### In Filament Admin Panel

1. Navigate to Pages or Research resource
2. Click on the language tab (English/Arabic)
3. Enter content for that language
4. Save the record

## Language Switcher

The language switcher component is included in the main layout:

```blade
<x-language-switcher />
```

Users can click to switch between available languages. The selected language is stored in the session.

## Middleware

The `SetLocale` middleware automatically sets the application locale based on the user's session:

```php
// Registered in bootstrap/app.php
\App\Http\Middleware\SetLocale::class,
```

## Routes

Language switching route:
```php
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');
```

## Database Schema

The Spatie Translatable package stores translations as JSON in the existing columns. No additional tables are required.

Example of stored data:
```json
{
  "en": "Welcome to HEAC",
  "ar": "مرحبا بكم في HEAC"
}
```

## Best Practices

1. **Always provide English content**: English is the fallback language
2. **Use translation keys consistently**: Keep field names consistent across models
3. **Test with RTL**: Arabic requires right-to-left layout testing
4. **Validate translations**: Ensure important content is translated before publishing
5. **SEO considerations**: Use `hreflang` tags for multilingual SEO (future enhancement)

## Future Enhancements

- [ ] URL-based locale detection (e.g., `/en/about`, `/ar/about`)
- [ ] Automatic translation suggestions using AI
- [ ] Translation progress indicators in admin panel
- [ ] Export/import translations for professional translation services
- [ ] Additional language support (French, Spanish, etc.)
- [ ] RTL layout optimization for Arabic
- [ ] Language-specific SEO metadata

## Troubleshooting

### Translation not showing

1. Check if the translation exists:
```php
$page->hasTranslation('title', 'ar');
```

2. Verify locale is set correctly:
```php
app()->getLocale(); // Should return 'ar' or 'en'
```

3. Clear cache:
```bash
php artisan cache:clear
php artisan config:clear
```

### Language switcher not working

1. Verify route is registered:
```bash
php artisan route:list | grep locale
```

2. Check session configuration in `.env`:
```env
SESSION_DRIVER=file
```

3. Ensure middleware is registered in `bootstrap/app.php`

## Resources

- [Spatie Laravel Translatable Documentation](https://github.com/spatie/laravel-translatable)
- [Laravel Localization](https://laravel.com/docs/localization)
- [Filament Forms](https://filamentphp.com/docs/forms)
