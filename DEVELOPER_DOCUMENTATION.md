# HEAC CMS - Developer Documentation

This document provides technical documentation for developers working on the HEAC CMS project.

## Table of Contents

1. [Architecture Overview](#architecture-overview)
2. [Design Patterns](#design-patterns)
3. [Service Layer](#service-layer)
4. [Repository Layer](#repository-layer)
5. [Models and Relationships](#models-and-relationships)
6. [Filament Resources](#filament-resources)
7. [Frontend Architecture](#frontend-architecture)
8. [API Documentation](#api-documentation)
9. [Testing](#testing)
10. [Deployment](#deployment)
11. [Contributing Guidelines](#contributing-guidelines)

---

## Architecture Overview

The HEAC CMS follows a layered architecture pattern with clear separation of concerns.

### Application Layers

```
┌─────────────────────────────────────────┐
│         Presentation Layer              │
│  (Controllers, Filament Resources)      │
└─────────────────┬───────────────────────┘
                  │
┌─────────────────▼───────────────────────┐
│         Service Layer                   │
│  (Business Logic)                       │
└─────────────────┬───────────────────────┘
                  │
┌─────────────────▼───────────────────────┐
│         Repository Layer                │
│  (Data Access)                          │
└─────────────────┬───────────────────────┘
                  │
┌─────────────────▼───────────────────────┐
│         Model Layer                     │
│  (Eloquent ORM)                         │
└─────────────────┬───────────────────────┘
                  │
┌─────────────────▼───────────────────────┐
│         Database                        │
└─────────────────────────────────────────┘
```

### Key Principles

- **Separation of Concerns**: Each layer has a specific responsibility
- **Dependency Injection**: Services and repositories are injected
- **Single Responsibility**: Classes have one primary purpose
- **Interface Segregation**: Use interfaces for flexibility
- **DRY Principle**: Don't Repeat Yourself
- **SOLID Principles**: Applied throughout the codebase

### Technology Stack

**Backend**
- Laravel 11.x (PHP 8.2+)
- MySQL/PostgreSQL for database
- Redis for caching and queues
- Spatie packages for common functionality

**Frontend**
- Blade templating engine
- Alpine.js for reactive components
- Tailwind CSS for styling
- Vite for asset bundling

**Admin Panel**
- Filament 4.x
- TipTap rich text editor
- Custom widgets and resources

---

## Design Patterns

### Repository Pattern

Repositories abstract data access logic from business logic.

**Purpose**
- Centralize data access logic
- Make code more testable
- Provide consistent interface for data operations
- Enable easy switching of data sources

**Example: PageRepository**

```php
namespace App\Repositories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class PageRepository
{
    public function findBySlug(string $slug): ?Page
    {
        return Cache::remember(
            "page.slug.{$slug}",
            now()->addHours(24),
            fn() => Page::where('slug', $slug)
                ->where('status', 'published')
                ->where('published_at', '<=', now())
                ->first()
        );
    }

    public function getPublished(): Collection
    {
        return Cache::remember(
            'pages.published',
            now()->addHours(1),
            fn() => Page::where('status', 'published')
                ->where('published_at', '<=', now())
                ->orderBy('order')
                ->get()
        );
    }
}
```

### Service Pattern

Services contain business logic and orchestrate operations.

**Purpose**
- Encapsulate business logic
- Coordinate between repositories
- Handle complex operations
- Maintain transaction boundaries

**Example: ContentService**

```php
namespace App\Services;

use App\Models\Page;
use App\Repositories\PageRepository;
use Illuminate\Support\Str;

class ContentService
{
    public function __construct(
        private PageRepository $pageRepository,
        private CacheService $cacheService
    ) {}

    public function createPage(array $data): Page
    {
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        // Create the page
        $page = Page::create($data);

        // Clear relevant caches
        $this->cacheService->clearPageCaches();

        return $page;
    }

    public function updatePage(Page $page, array $data): Page
    {
        $page->update($data);

        // Clear specific page cache
        $this->cacheService->clearPageCache($page);

        return $page->fresh();
    }
}
```

### Observer Pattern

Observers handle model events automatically.

**Example: PageObserver**

```php
namespace App\Observers;

use App\Models\Page;
use App\Services\CacheService;
use App\Services\SEOService;

class PageObserver
{
    public function __construct(
        private CacheService $cacheService,
        private SEOService $seoService
    ) {}

    public function created(Page $page): void
    {
        $this->cacheService->clearPageCaches();
        $this->seoService->regenerateSitemap();
    }

    public function updated(Page $page): void
    {
        $this->cacheService->clearPageCache($page);
        
        if ($page->wasChanged('slug')) {
            $this->seoService->regenerateSitemap();
        }
    }

    public function deleted(Page $page): void
    {
        $this->cacheService->clearPageCaches();
        $this->seoService->regenerateSitemap();
    }
}
```

---

## Service Layer

Services contain the business logic of the application.

### ContentService

Manages page content operations.

**Key Methods**

```php
// Get published pages with caching
public function getPublishedPages(): Collection

// Find page by slug
public function getPageBySlug(string $slug): ?Page

// Create new page with slug generation
public function createPage(array $data): Page

// Update existing page
public function updatePage(Page $page, array $data): Page

// Delete page and clear caches
public function deletePage(Page $page): bool

// Reorder pages
public function reorderPages(array $order): void
```

**Usage Example**

```php
use App\Services\ContentService;

class PageController extends Controller
{
    public function __construct(
        private ContentService $contentService
    ) {}

    public function show(string $slug)
    {
        $page = $this->contentService->getPageBySlug($slug);
        
        if (!$page) {
            abort(404);
        }

        return view('pages.show', compact('page'));
    }
}
```

### ResearchService

Manages research paper operations.

**Key Methods**

```php
// Search research with filters
public function searchResearch(
    string $query, 
    array $filters = []
): LengthAwarePaginator

// Get featured research for homepage
public function getFeaturedResearch(int $limit = 6): Collection

// Get research by category
public function getResearchByCategory(
    Category $category
): LengthAwarePaginator

// Increment view count
public function incrementViews(Research $research): void

// Increment download count
public function incrementDownloads(Research $research): void

// Get popular research
public function getPopularResearch(int $limit = 10): Collection
```

### MediaService

Handles file uploads and media management.

**Key Methods**

```php
// Upload file with optimization
public function upload(
    UploadedFile $file, 
    ?string $folder = null
): Media

// Delete media and file
public function delete(Media $media): bool

// Generate image thumbnails
public function generateThumbnails(
    Media $media, 
    array $sizes
): array

// Optimize image (WebP conversion)
public function optimizeImage(Media $media): void

// Search media library
public function searchMedia(
    string $query, 
    array $filters = []
): Collection
```

**Usage Example**

```php
use App\Services\MediaService;

class MediaController extends Controller
{
    public function __construct(
        private MediaService $mediaService
    ) {}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|max:10240',
            'folder' => 'nullable|string'
        ]);

        $media = $this->mediaService->upload(
            $request->file('file'),
            $validated['folder'] ?? null
        );

        return response()->json($media);
    }
}
```

### SEOService

Manages SEO-related functionality.

**Key Methods**

```php
// Generate meta tags for a model
public function generateMetaTags(Page|Research $model): array

// Generate structured data (Schema.org)
public function generateStructuredData(
    Page|Research $model
): array

// Generate XML sitemap
public function generateSitemap(): string

// Generate Open Graph tags
public function generateOpenGraphTags(
    Page|Research $model
): array
```

### CacheService

Centralized cache management.

**Key Methods**

```php
// Clear all page caches
public function clearPageCaches(): void

// Clear specific page cache
public function clearPageCache(Page $page): void

// Clear research caches
public function clearResearchCaches(): void

// Warm cache for popular content
public function warmCache(): void
```

---

## Repository Layer

Repositories handle data access and query optimization.

### PageRepository

**Implementation**

```php
namespace App\Repositories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class PageRepository
{
    protected int $cacheTTL = 3600; // 1 hour

    public function findBySlug(string $slug): ?Page
    {
        return Cache::remember(
            "page.slug.{$slug}",
            $this->cacheTTL,
            fn() => Page::with(['parent', 'children'])
                ->where('slug', $slug)
                ->where('status', 'published')
                ->where('published_at', '<=', now())
                ->first()
        );
    }

    public function getPublished(): Collection
    {
        return Cache::remember(
            'pages.published',
            $this->cacheTTL,
            fn() => Page::where('status', 'published')
                ->where('published_at', '<=', now())
                ->orderBy('order')
                ->orderBy('title')
                ->get()
        );
    }

    public function getHierarchy(): Collection
    {
        return Cache::remember(
            'pages.hierarchy',
            $this->cacheTTL,
            fn() => Page::whereNull('parent_id')
                ->with('children')
                ->where('status', 'published')
                ->orderBy('order')
                ->get()
        );
    }
}
```

### ResearchRepository

**Implementation**

```php
namespace App\Repositories;

use App\Models\Research;
use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ResearchRepository
{
    public function search(
        string $query, 
        array $filters = []
    ): LengthAwarePaginator {
        $builder = Research::query()
            ->where('status', 'published');

        // Full-text search
        if (!empty($query)) {
            $builder->whereRaw(
                'MATCH(title, abstract) AGAINST(? IN BOOLEAN MODE)',
                [$query]
            );
        }

        // Category filter
        if (!empty($filters['category_id'])) {
            $builder->whereHas('categories', function ($q) use ($filters) {
                $q->where('categories.id', $filters['category_id']);
            });
        }

        // Tag filter
        if (!empty($filters['tag_id'])) {
            $builder->whereHas('tags', function ($q) use ($filters) {
                $q->where('tags.id', $filters['tag_id']);
            });
        }

        // Date range filter
        if (!empty($filters['date_from'])) {
            $builder->where('publication_date', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $builder->where('publication_date', '<=', $filters['date_to']);
        }

        return $builder->with(['categories', 'tags'])
            ->orderBy('publication_date', 'desc')
            ->paginate(12);
    }

    public function getFeatured(int $limit = 6): Collection
    {
        return Research::where('status', 'published')
            ->where('featured', true)
            ->orderBy('publication_date', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getPopular(int $limit = 10): Collection
    {
        return Research::where('status', 'published')
            ->orderBy('downloads_count', 'desc')
            ->orderBy('views_count', 'desc')
            ->limit($limit)
            ->get();
    }
}
```

---

## Models and Relationships

### Page Model

**Attributes**

```php
protected $fillable = [
    'title',
    'slug',
    'content',
    'excerpt',
    'meta_title',
    'meta_description',
    'og_image',
    'template',
    'status',
    'published_at',
    'parent_id',
    'order'
];

protected $casts = [
    'published_at' => 'datetime',
    'content' => 'array'
];
```

**Relationships**

```php
// Parent page
public function parent(): BelongsTo
{
    return $this->belongsTo(Page::class, 'parent_id');
}

// Child pages
public function children(): HasMany
{
    return $this->hasMany(Page::class, 'parent_id')
        ->orderBy('order');
}
```

**Scopes**

```php
// Published pages only
public function scopePublished($query)
{
    return $query->where('status', 'published')
        ->where('published_at', '<=', now());
}

// Draft pages
public function scopeDraft($query)
{
    return $query->where('status', 'draft');
}
```

**Accessors**

```php
// Get full URL
public function getUrlAttribute(): string
{
    return url($this->slug);
}

// Check if published
public function getIsPublishedAttribute(): bool
{
    return $this->status === 'published' 
        && $this->published_at <= now();
}
```

### Research Model

**Attributes**

```php
protected $fillable = [
    'title',
    'slug',
    'abstract',
    'authors',
    'publication_date',
    'file_path',
    'file_type',
    'file_size',
    'thumbnail',
    'views_count',
    'downloads_count',
    'status',
    'featured'
];

protected $casts = [
    'publication_date' => 'date',
    'authors' => 'array',
    'featured' => 'boolean'
];
```

**Relationships**

```php
// Many-to-many with categories
public function categories(): BelongsToMany
{
    return $this->belongsToMany(Category::class, 'research_category');
}

// Many-to-many with tags
public function tags(): BelongsToMany
{
    return $this->belongsToMany(Tag::class, 'research_tag');
}
```

**Methods**

```php
// Increment view count
public function incrementViews(): void
{
    $this->increment('views_count');
}

// Increment download count
public function incrementDownloads(): void
{
    $this->increment('downloads_count');
}

// Get download URL
public function getDownloadUrlAttribute(): string
{
    return route('research.download', $this->slug);
}
```

### Media Model

**Attributes**

```php
protected $fillable = [
    'name',
    'file_name',
    'mime_type',
    'path',
    'disk',
    'size',
    'alt_text',
    'title',
    'caption',
    'folder_id'
];

protected $appends = ['url', 'thumbnail_url'];
```

**Accessors**

```php
// Get full URL
public function getUrlAttribute(): string
{
    return Storage::disk($this->disk)->url($this->path);
}

// Get thumbnail URL
public function getThumbnailUrlAttribute(): ?string
{
    if (!$this->isImage()) {
        return null;
    }

    $thumbnailPath = str_replace(
        basename($this->path),
        'thumbnails/' . basename($this->path),
        $this->path
    );

    return Storage::disk($this->disk)->url($thumbnailPath);
}

// Check if file is an image
public function isImage(): bool
{
    return str_starts_with($this->mime_type, 'image/');
}
```

### Category Model

**Hierarchical Structure**

```php
// Parent category
public function parent(): BelongsTo
{
    return $this->belongsTo(Category::class, 'parent_id');
}

// Child categories
public function children(): HasMany
{
    return $this->hasMany(Category::class, 'parent_id')
        ->orderBy('order');
}

// Research in this category
public function research(): BelongsToMany
{
    return $this->belongsToMany(Research::class, 'research_category');
}
```

---

## Filament Resources

Filament resources provide the admin panel interface.

### Resource Structure

```php
namespace App\Filament\Resources;

use Filament\Resources\Resource;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Form fields defined in separate schema class
            ...PageForm::schema()
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Table columns defined in separate table class
                ...PagesTable::columns()
            ])
            ->filters([
                ...PagesTable::filters()
            ])
            ->actions([
                ...PagesTable::actions()
            ]);
    }
}
```

### Form Schemas

Form schemas are separated for reusability:

```php
namespace App\Filament\Resources\Pages\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;

class PageForm
{
    public static function schema(): array
    {
        return [
            TextInput::make('title')
                ->required()
                ->maxLength(255)
                ->live(onBlur: true)
                ->afterStateUpdated(fn ($state, $set) => 
                    $set('slug', Str::slug($state))
                ),

            TextInput::make('slug')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),

            RichEditor::make('content')
                ->required()
                ->columnSpanFull(),

            Select::make('status')
                ->options([
                    'draft' => 'Draft',
                    'published' => 'Published',
                    'archived' => 'Archived'
                ])
                ->default('draft')
                ->required(),

            // Additional fields...
        ];
    }
}
```

### Custom Widgets

Dashboard widgets display key metrics:

```php
namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Page;
use App\Models\Research;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Pages', Page::count())
                ->description('Published pages')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('success'),

            Stat::make('Research Papers', Research::count())
                ->description('Total publications')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('info'),

            Stat::make('Total Downloads', Research::sum('downloads_count'))
                ->description('All time downloads')
                ->descriptionIcon('heroicon-m-arrow-down-tray')
                ->color('warning'),
        ];
    }
}
```

---

## Frontend Architecture

### Blade Components

Reusable Blade components for consistent UI:

**Layout Component**

```blade
<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('styles')
</head>
<body class="antialiased">
    <x-navigation />
    
    <main>
        {{ $slot }}
    </main>
    
    <x-footer />
    
    @stack('scripts')
</body>
</html>
```

**Component Usage**

```blade
<x-app-layout>
    <x-slot:title>{{ $page->title }}</x-slot:title>
    
    <div class="container mx-auto px-4 py-8">
        <h1>{{ $page->title }}</h1>
        <div class="prose max-w-none">
            {!! $page->content !!}
        </div>
    </div>
</x-app-layout>
```

### Alpine.js Components

Interactive components using Alpine.js:

**Search Component**

```javascript
// resources/js/components/search.js
export default function search() {
    return {
        query: '',
        results: [],
        loading: false,
        
        async performSearch() {
            if (this.query.length < 3) {
                this.results = [];
                return;
            }
            
            this.loading = true;
            
            try {
                const response = await fetch(
                    `/api/search?q=${encodeURIComponent(this.query)}`
                );
                this.results = await response.json();
            } catch (error) {
                console.error('Search failed:', error);
            } finally {
                this.loading = false;
            }
        }
    }
}
```

**Usage in Blade**

```blade
<div x-data="search()" x-init="$watch('query', () => performSearch())">
    <input 
        type="text" 
        x-model="query"
        placeholder="Search..."
        class="w-full px-4 py-2 border rounded"
    >
    
    <div x-show="loading">Loading...</div>
    
    <div x-show="results.length > 0">
        <template x-for="result in results" :key="result.id">
            <div x-text="result.title"></div>
        </template>
    </div>
</div>
```

### Tailwind CSS Customization

Custom theme configuration:

```javascript
// tailwind.config.js
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    50: '#f0f9ff',
                    100: '#e0f2fe',
                    // ... custom color palette
                    900: '#0c4a6e',
                },
            },
            fontFamily: {
                sans: ['Inter', 'sans-serif'],
            },
        },
    },
    plugins: [
        require('@tailwindcss/typography'),
        require('@tailwindcss/forms'),
    ],
}
```

---

## API Documentation

### Internal API Endpoints

The system includes internal API endpoints for AJAX operations.

**Search API**

```php
// routes/api.php
Route::get('/search', [SearchController::class, 'search']);
```

```php
// app/Http/Controllers/Api/SearchController.php
public function search(Request $request)
{
    $query = $request->input('q');
    
    $results = [
        'pages' => Page::search($query)->take(5)->get(),
        'research' => Research::search($query)->take(5)->get(),
    ];
    
    return response()->json($results);
}
```

**Response Format**

```json
{
    "pages": [
        {
            "id": 1,
            "title": "About HEAC",
            "slug": "about-heac",
            "excerpt": "Learn about our mission..."
        }
    ],
    "research": [
        {
            "id": 1,
            "title": "Quality Assurance in Higher Education",
            "slug": "quality-assurance-higher-education",
            "abstract": "This research explores..."
        }
    ]
}
```

### Download Tracking

Research downloads are tracked:

```php
// app/Http/Controllers/ResearchController.php
public function download(string $slug)
{
    $research = Research::where('slug', $slug)->firstOrFail();
    
    // Increment download count
    $this->researchService->incrementDownloads($research);
    
    // Return file download
    return Storage::disk('public')->download(
        $research->file_path,
        $research->title . '.' . $research->file_type
    );
}
```

---

## Testing

### Test Structure

```
tests/
├── Unit/
│   ├── Services/
│   │   ├── ContentServiceTest.php
│   │   ├── ResearchServiceTest.php
│   │   └── MediaServiceTest.php
│   └── Repositories/
│       ├── PageRepositoryTest.php
│       └── ResearchRepositoryTest.php
├── Feature/
│   ├── PageManagementTest.php
│   ├── ResearchManagementTest.php
│   └── ContactFormTest.php
└── Browser/
    └── AdminPanelTest.php
```

### Unit Testing Example

```php
namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\ContentService;
use App\Repositories\PageRepository;
use App\Models\Page;
use Mockery;

class ContentServiceTest extends TestCase
{
    public function test_creates_page_with_auto_generated_slug()
    {
        $repository = Mockery::mock(PageRepository::class);
        $service = new ContentService($repository);
        
        $data = [
            'title' => 'Test Page',
            'content' => 'Test content',
            'status' => 'draft'
        ];
        
        $page = $service->createPage($data);
        
        $this->assertEquals('test-page', $page->slug);
    }
}
```

### Feature Testing Example

```php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PageManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_page()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        
        $response = $this->actingAs($admin)
            ->post('/admin/pages', [
                'title' => 'New Page',
                'content' => 'Page content',
                'status' => 'published'
            ]);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('pages', [
            'title' => 'New Page',
            'slug' => 'new-page'
        ]);
    }

    public function test_published_page_is_visible_to_public()
    {
        $page = Page::factory()->create([
            'status' => 'published',
            'published_at' => now()
        ]);
        
        $response = $this->get('/' . $page->slug);
        
        $response->assertOk();
        $response->assertSee($page->title);
    }
}
```

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage

# Run specific test file
php artisan test tests/Unit/Services/ContentServiceTest.php
```

---

## Deployment

### Deployment Checklist

**Pre-Deployment**

1. Run tests: `php artisan test`
2. Check code quality: `./vendor/bin/phpstan analyse`
3. Review security: `composer audit`
4. Update dependencies: `composer update`
5. Build assets: `npm run build`

**Deployment Steps**

```bash
# 1. Pull latest code
git pull origin main

# 2. Install dependencies
composer install --optimize-autoloader --no-dev

# 3. Run migrations
php artisan migrate --force

# 4. Clear and cache config
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Build assets
npm ci
npm run build

# 6. Restart services
php artisan queue:restart
php artisan optimize
```

**Post-Deployment**

1. Verify application is running
2. Check error logs
3. Test critical functionality
4. Monitor performance
5. Verify scheduled tasks are running

### Environment Configuration

**Production .env Settings**

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_DATABASE=heac_cms
DB_USERNAME=your-username
DB_PASSWORD=your-secure-password

# Cache & Sessions
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=your-redis-password
REDIS_PORT=6379

# Mail
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls

# Backup
BACKUP_DISK=s3
AWS_ACCESS_KEY_ID=your-key
AWS_SECRET_ACCESS_KEY=your-secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket
```

### Server Requirements

**Web Server (Nginx)**

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/heac-cms/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

**Scheduled Tasks (Cron)**

```bash
* * * * * cd /var/www/heac-cms && php artisan schedule:run >> /dev/null 2>&1
```

**Queue Worker (Supervisor)**

```ini
[program:heac-cms-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/heac-cms/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/heac-cms/storage/logs/worker.log
stopwaitsecs=3600
```

---

## Contributing Guidelines

### Code Style

Follow PSR-12 coding standards:

```bash
# Format code with Laravel Pint
./vendor/bin/pint

# Check code quality
./vendor/bin/phpstan analyse
```

### Git Workflow

**Branch Naming**

- `feature/description` - New features
- `bugfix/description` - Bug fixes
- `hotfix/description` - Urgent production fixes
- `refactor/description` - Code refactoring

**Commit Messages**

Follow conventional commits:

```
feat: add research download tracking
fix: resolve cache clearing issue
docs: update API documentation
refactor: extract service methods
test: add unit tests for ContentService
```

**Pull Request Process**

1. Create feature branch from `main`
2. Make changes and commit
3. Write/update tests
4. Update documentation
5. Run tests and code quality checks
6. Create pull request
7. Request code review
8. Address feedback
9. Merge after approval

### Code Review Checklist

**Functionality**
- [ ] Code works as intended
- [ ] Edge cases are handled
- [ ] Error handling is appropriate
- [ ] No breaking changes

**Code Quality**
- [ ] Follows PSR-12 standards
- [ ] No code duplication
- [ ] Proper naming conventions
- [ ] Comments where necessary
- [ ] No debug code left

**Testing**
- [ ] Unit tests added/updated
- [ ] Feature tests added/updated
- [ ] All tests pass
- [ ] Coverage is adequate

**Security**
- [ ] Input validation present
- [ ] SQL injection prevented
- [ ] XSS protection in place
- [ ] Authorization checks correct

**Performance**
- [ ] No N+1 queries
- [ ] Proper indexing used
- [ ] Caching implemented where needed
- [ ] Efficient algorithms used

### Adding New Features

**1. Create Migration**

```bash
php artisan make:migration create_feature_table
```

**2. Create Model**

```bash
php artisan make:model Feature
```

**3. Create Repository**

```php
namespace App\Repositories;

class FeatureRepository
{
    // Data access methods
}
```

**4. Create Service**

```php
namespace App\Services;

class FeatureService
{
    public function __construct(
        private FeatureRepository $repository
    ) {}
    
    // Business logic methods
}
```

**5. Create Controller**

```bash
php artisan make:controller FeatureController
```

**6. Create Filament Resource**

```bash
php artisan make:filament-resource Feature
```

**7. Add Tests**

```bash
php artisan make:test FeatureTest
```

**8. Update Documentation**

- Add to this documentation
- Update README if needed
- Add inline code comments

### Database Conventions

**Table Names**
- Plural, snake_case: `research_papers`, `contact_inquiries`
- Pivot tables: alphabetical order: `research_category`

**Column Names**
- snake_case: `publication_date`, `file_path`
- Foreign keys: `{table}_id`: `category_id`, `user_id`
- Timestamps: `created_at`, `updated_at`, `deleted_at`

**Indexes**
- Prefix with `idx_`: `idx_slug`, `idx_status`
- Foreign keys: `fk_{table}_{column}`

### Performance Best Practices

**Query Optimization**

```php
// Bad: N+1 query problem
$pages = Page::all();
foreach ($pages as $page) {
    echo $page->author->name; // Queries for each page
}

// Good: Eager loading
$pages = Page::with('author')->get();
foreach ($pages as $page) {
    echo $page->author->name; // No additional queries
}
```

**Caching**

```php
// Cache expensive queries
$popularResearch = Cache::remember(
    'research.popular',
    now()->addHours(1),
    fn() => Research::orderBy('downloads_count', 'desc')
        ->take(10)
        ->get()
);
```

**Chunking Large Datasets**

```php
// Process large datasets in chunks
Research::chunk(100, function ($research) {
    foreach ($research as $paper) {
        // Process each paper
    }
});
```

### Security Best Practices

**Input Validation**

```php
// Always validate user input
$validated = $request->validate([
    'title' => 'required|string|max:255',
    'email' => 'required|email',
    'file' => 'required|file|mimes:pdf|max:10240'
]);
```

**Authorization**

```php
// Check permissions before actions
$this->authorize('update', $page);

// Or in controllers
if (Gate::denies('update-page', $page)) {
    abort(403);
}
```

**SQL Injection Prevention**

```php
// Good: Use parameter binding
$users = DB::table('users')
    ->where('email', $email)
    ->get();

// Bad: String concatenation
$users = DB::select("SELECT * FROM users WHERE email = '$email'");
```

**XSS Prevention**

```blade
{{-- Good: Escaped output --}}
{{ $page->title }}

{{-- Only when HTML is safe --}}
{!! $page->content !!}
```

---

## Troubleshooting

### Common Development Issues

**Issue: Class not found**

```bash
composer dump-autoload
php artisan optimize:clear
```

**Issue: Changes not reflecting**

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
npm run build
```

**Issue: Database connection error**

- Check `.env` database credentials
- Verify database server is running
- Test connection: `php artisan db:show`

**Issue: Permission denied**

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Debugging Tools

**Laravel Telescope**

```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

Access at: `/telescope`

**Laravel Debugbar**

```bash
composer require barryvdh/laravel-debugbar --dev
```

**Query Logging**

```php
DB::enableQueryLog();
// Your queries here
dd(DB::getQueryLog());
```

---

## Additional Resources

### Documentation Links

- [Laravel Documentation](https://laravel.com/docs)
- [Filament Documentation](https://filamentphp.com/docs)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Alpine.js Documentation](https://alpinejs.dev)

### Useful Commands

```bash
# Clear all caches
php artisan optimize:clear

# Generate IDE helper files
php artisan ide-helper:generate
php artisan ide-helper:models

# Database operations
php artisan migrate:fresh --seed
php artisan db:seed

# Queue operations
php artisan queue:work
php artisan queue:restart

# Maintenance mode
php artisan down
php artisan up

# Generate sitemap
php artisan sitemap:generate

# Run backups
php artisan backup:run
```

---

**Last Updated**: October 2025  
**Version**: 1.0  
**Maintainer**: HEAC Development Team
