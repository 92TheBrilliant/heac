# Analytics and Tracking Implementation

This document provides an overview of the analytics and tracking system implemented for the HEAC CMS.

## Overview

The analytics system tracks user interactions across the website, including page views, research views, and research downloads. It integrates with Google Analytics 4 for external analytics and maintains an internal database for detailed tracking and reporting.

## Components

### 1. Google Analytics 4 Integration

**Configuration:**
- Environment variable: `GOOGLE_ANALYTICS_ID` in `.env`
- Configuration: `config/services.php` under `google_analytics.id`
- Only loads in production environment

**Implementation:**
- Blade component: `resources/views/components/google-analytics.blade.php`
- Included in main layout: `resources/views/layouts/app.blade.php`

**Custom Events:**
- `research_download` - Tracks when research files are downloaded
- `research_view` - Tracks when research pages are viewed
- `page_view` - Tracks general page views
- `contact_form_submit` - Tracks contact form submissions
- `search` - Tracks search queries

**JavaScript Helper Functions:**
```javascript
window.trackResearchDownload(researchId, researchTitle)
window.trackResearchView(researchId, researchTitle)
window.trackPageView(pageTitle, pagePath)
window.trackContactFormSubmission()
window.trackSearch(searchTerm, resultCount)
```

### 2. Database Analytics

**Table:** `analytics`

**Columns:**
- `id` - Primary key
- `event_type` - Type of event (page_view, research_view, research_download)
- `trackable_type` - Polymorphic model type (Page, Research, etc.)
- `trackable_id` - Polymorphic model ID
- `url` - Full URL of the request
- `referrer` - HTTP referrer
- `user_agent` - Browser user agent
- `ip_address` - User IP address
- `user_id` - Authenticated user ID (nullable)
- `metadata` - JSON field for additional data
- `created_at` - Timestamp

**Model:** `App\Models\Analytic`

**Scopes:**
- `ofType($type)` - Filter by event type
- `dateRange($start, $end)` - Filter by date range
- `pageViews()` - Get page view events
- `researchViews()` - Get research view events
- `researchDownloads()` - Get research download events

### 3. Middleware

**TrackPageViews Middleware:**
- Location: `app/Http/Middleware/TrackPageViews.php`
- Automatically tracks all successful GET requests to public pages
- Excludes admin panel, Livewire, and AJAX requests
- Registered in `bootstrap/app.php` as part of web middleware group

### 4. Controller Tracking

**ResearchController:**
- `trackResearchView()` - Tracks research page views
- `trackResearchDownload()` - Tracks research file downloads
- Automatically called in `show()` and `download()` methods

### 5. Dashboard Widgets

**PageViewsChartWidget:**
- Location: `app/Filament/Widgets/PageViewsChartWidget.php`
- Displays page views over the last 30 days
- Line chart visualization
- Sort order: 4

**PopularResearchTableWidget:**
- Location: `app/Filament/Widgets/PopularResearchTableWidget.php`
- Shows top 10 most popular research in the last 30 days
- Displays total views, downloads, and recent activity
- Sort order: 5

**DownloadStatisticsWidget:**
- Location: `app/Filament/Widgets/DownloadStatisticsWidget.php`
- Bar chart showing research downloads
- Filterable by 7, 30, or 90 days
- Sort order: 6

## Usage

### Setting Up Google Analytics

1. Create a Google Analytics 4 property
2. Get your Measurement ID (format: G-XXXXXXXXXX)
3. Add to `.env`:
   ```
   GOOGLE_ANALYTICS_ID=G-XXXXXXXXXX
   ```
4. Deploy to production (GA4 only loads in production)

### Viewing Analytics in Admin Panel

1. Log in to the admin panel at `/admin`
2. Navigate to the Dashboard
3. View the following widgets:
   - **Stats Overview** - Quick metrics
   - **Popular Content** - Most viewed pages
   - **Recent Activity** - Latest actions
   - **Page Views Over Time** - 30-day trend
   - **Most Popular Research** - Top research by views
   - **Research Downloads** - Download statistics

### Querying Analytics Data

```php
use App\Models\Analytic;
use Illuminate\Support\Carbon;

// Get page views for today
$todayViews = Analytic::pageViews()
    ->whereDate('created_at', Carbon::today())
    ->count();

// Get research downloads in the last 7 days
$recentDownloads = Analytic::researchDownloads()
    ->where('created_at', '>=', Carbon::now()->subDays(7))
    ->count();

// Get most viewed research
$popularResearch = Analytic::researchViews()
    ->selectRaw('trackable_id, COUNT(*) as view_count')
    ->groupBy('trackable_id')
    ->orderByDesc('view_count')
    ->limit(10)
    ->get();

// Get analytics for specific research
$researchAnalytics = Analytic::where('trackable_type', Research::class)
    ->where('trackable_id', $researchId)
    ->get();
```

### Custom Event Tracking

To track custom events in your views:

```blade
@push('scripts')
<script>
// Track a custom event
if (typeof window.trackEvent === 'function') {
    window.trackEvent('custom_event_name', {
        'event_category': 'Category',
        'event_label': 'Label',
        'value': 1
    });
}
</script>
@endpush
```

## Performance Considerations

1. **Database Indexing:** The analytics table has indexes on frequently queried columns
2. **Error Handling:** All tracking operations are wrapped in try-catch to prevent disruption
3. **Async Processing:** Consider moving analytics to a queue for high-traffic sites
4. **Data Retention:** Implement a cleanup job to archive old analytics data

## Privacy and Compliance

1. **IP Anonymization:** Consider anonymizing IP addresses for GDPR compliance
2. **User Consent:** Implement cookie consent for GA4 tracking
3. **Data Retention:** Define and implement data retention policies
4. **User Rights:** Provide mechanisms for users to request data deletion

## Future Enhancements

1. Real-time analytics dashboard
2. Export analytics data to CSV/Excel
3. Email reports for administrators
4. A/B testing integration
5. Heatmap tracking
6. Session recording
7. Conversion funnel tracking
8. Custom dashboard filters and date ranges

## Troubleshooting

### Analytics Not Recording

1. Check database connection
2. Verify middleware is registered in `bootstrap/app.php`
3. Check error logs in `storage/logs/laravel.log`
4. Ensure analytics table exists (run migrations)

### Google Analytics Not Loading

1. Verify `GOOGLE_ANALYTICS_ID` is set in `.env`
2. Check that `APP_ENV=production` (GA4 only loads in production)
3. Verify the component is included in the layout
4. Check browser console for JavaScript errors

### Widget Not Displaying Data

1. Verify analytics data exists in the database
2. Check widget sort order in AdminPanelProvider
3. Clear Filament cache: `php artisan filament:cache-components`
4. Check for PHP errors in the widget class

## Related Files

- `app/Models/Analytic.php` - Analytics model
- `app/Http/Middleware/TrackPageViews.php` - Page view tracking middleware
- `app/Http/Controllers/ResearchController.php` - Research tracking
- `resources/views/components/google-analytics.blade.php` - GA4 component
- `database/migrations/2025_10_09_110346_create_analytics_table.php` - Analytics table migration
- `app/Filament/Widgets/PageViewsChartWidget.php` - Page views chart
- `app/Filament/Widgets/PopularResearchTableWidget.php` - Popular research table
- `app/Filament/Widgets/DownloadStatisticsWidget.php` - Download statistics chart
