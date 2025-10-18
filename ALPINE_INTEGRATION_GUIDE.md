# Alpine.js Components Integration Guide

This guide shows you how to integrate the Alpine.js components into your existing HEAC CMS views.

## Quick Start

### 1. Build the Assets

First, build the JavaScript assets to include Alpine.js:

```bash
cd heac-cms
npm run build
```

For development with hot reload:
```bash
npm run dev
```

### 2. Update Your Main Layout

Ensure your main layout (`resources/views/layouts/app.blade.php`) includes the Vite directives:

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'HEAC')</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <!-- Your content -->
</body>
</html>
```

## Component Integration

### Adding Search to Header/Navigation

Update your header or navigation section:

```blade
<!-- In resources/views/layouts/app.blade.php or partials/header.blade.php -->
<header class="bg-white shadow">
    <div class="container mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
            <!-- Logo -->
            <div class="flex items-center">
                <img src="{{ asset('images/logo.png') }}" alt="HEAC" class="h-10">
            </div>

            <!-- Desktop Navigation -->
            <nav class="hidden md:flex space-x-6">
                <a href="{{ route('home') }}" class="text-gray-700 hover:text-blue-600">Home</a>
                <a href="{{ route('research.index') }}" class="text-gray-700 hover:text-blue-600">Research</a>
                <a href="{{ route('contact.index') }}" class="text-gray-700 hover:text-blue-600">Contact</a>
            </nav>

            <!-- Search Component -->
            <div class="hidden md:block">
                <x-search />
            </div>

            <!-- Mobile Menu Button -->
            <x-mobile-nav :navigation="[]" />
        </div>
    </div>
</header>
```

### Adding Filter Sidebar to Research Page

Update your research index view (`resources/views/research/index.blade.php`):

```blade
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        
        <!-- Filter Sidebar (Desktop: Left, Mobile: Top) -->
        <aside class="lg:w-1/4">
            <x-filter-sidebar 
                :categories="$categories"
                :tags="$tags"
                :years="$years"
            />
        </aside>

        <!-- Research Results -->
        <main class="lg:w-3/4">
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Research Publications</h1>
                <p class="mt-2 text-gray-600">
                    Showing {{ $research->total() }} results
                </p>
            </div>

            <!-- Research Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($research as $item)
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-xl font-semibold">{{ $item->title }}</h3>
                        <p class="mt-2 text-gray-600">{{ $item->abstract }}</p>
                        <a href="{{ route('research.show', $item->slug) }}" 
                           class="mt-4 inline-block text-blue-600 hover:text-blue-800">
                            Read More →
                        </a>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $research->links() }}
            </div>
        </main>
    </div>
</div>
@endsection
```

### Update Research Controller

Make sure your `ResearchController` passes the filter data:

```php
// app/Http/Controllers/ResearchController.php

public function index(Request $request)
{
    // Get filter data
    $categories = Category::withCount('research')
        ->orderBy('name')
        ->get();
    
    $tags = Tag::withCount('research')
        ->orderBy('name')
        ->get();
    
    $years = Research::selectRaw('YEAR(publication_date) as year, COUNT(*) as count')
        ->whereNotNull('publication_date')
        ->groupBy('year')
        ->orderBy('year', 'desc')
        ->get();

    // Apply filters
    $query = Research::published();

    // Category filter
    if ($request->has('categories')) {
        $query->whereHas('categories', function ($q) use ($request) {
            $q->whereIn('categories.id', $request->input('categories'));
        });
    }

    // Tag filter
    if ($request->has('tags')) {
        $query->whereHas('tags', function ($q) use ($request) {
            $q->whereIn('tags.id', $request->input('tags'));
        });
    }

    // Year filter
    if ($request->filled('year')) {
        $query->whereYear('publication_date', $request->input('year'));
    }

    // Sort
    $sort = $request->input('sort', 'latest');
    switch ($sort) {
        case 'oldest':
            $query->oldest('publication_date');
            break;
        case 'popular':
            $query->orderBy('views_count', 'desc');
            break;
        case 'title':
            $query->orderBy('title', 'asc');
            break;
        default:
            $query->latest('publication_date');
    }

    $research = $query->paginate(12);

    return view('research.index', compact('research', 'categories', 'tags', 'years'));
}
```

### Adding Image Gallery

For pages with image galleries (e.g., research detail page):

```blade
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold">{{ $research->title }}</h1>
    
    <!-- Research content -->
    <div class="mt-6 prose max-w-none">
        {!! $research->content !!}
    </div>

    <!-- Image Gallery (if research has images) -->
    @if(!empty($research->images))
        <div class="mt-8">
            <h2 class="text-2xl font-bold mb-4">Gallery</h2>
            <x-gallery 
                :images="$research->images"
                :columns="3"
                :gap="4"
            />
        </div>
    @endif
</div>
@endsection
```

### Standalone Lightbox Usage

For custom image implementations:

```blade
<!-- Include lightbox component -->
<x-lightbox />

<!-- Your custom images -->
<div x-data>
    @foreach($images as $index => $image)
        <img 
            src="{{ $image['thumbnail'] }}"
            alt="{{ $image['alt'] }}"
            @click="$dispatch('open-lightbox', { 
                images: @js($images), 
                index: {{ $index }} 
            })"
            class="cursor-pointer hover:opacity-80 transition"
        />
    @endforeach
</div>
```

## Testing Your Integration

### 1. Test Search Component

1. Navigate to your homepage
2. Type in the search box
3. Verify results appear after 300ms
4. Test keyboard navigation (arrows, enter, escape)
5. Click a result to navigate

### 2. Test Mobile Navigation

1. Resize browser to mobile width (< 768px)
2. Click hamburger menu
3. Verify menu slides in from left
4. Verify body scroll is locked
5. Click backdrop or close button
6. Verify menu closes smoothly

### 3. Test Filter Sidebar

1. Navigate to research page
2. Select a category checkbox
3. Verify URL updates with filter
4. Verify research list updates
5. Add more filters
6. Click "Clear all" button
7. Verify all filters are removed

### 4. Test Lightbox

1. Navigate to a page with gallery
2. Click an image
3. Verify lightbox opens full-screen
4. Test arrow keys for navigation
5. Test escape key to close
6. Click backdrop to close

## Troubleshooting

### Search Not Working

**Problem:** Search doesn't return results

**Solution:**
1. Check that the API route is registered:
   ```php
   Route::get('/api/search', [HomeController::class, 'search'])->name('api.search');
   ```
2. Verify the search method exists in HomeController
3. Check browser console for errors
4. Test the API directly: `/api/search?q=test`

### Mobile Menu Not Appearing

**Problem:** Hamburger button doesn't show on mobile

**Solution:**
1. Verify Tailwind CSS is compiled: `npm run build`
2. Check that `md:hidden` class is working
3. Ensure Alpine.js is loaded (check browser console)
4. Verify `x-data="mobileNav"` is present

### Filters Not Applying

**Problem:** Selecting filters doesn't update results

**Solution:**
1. Check that filter data is passed to the component
2. Verify the controller reads URL parameters
3. Check that the query applies filters correctly
4. Test URL directly with parameters: `/research?categories[]=1`

### Lightbox Not Opening

**Problem:** Clicking images doesn't open lightbox

**Solution:**
1. Verify Alpine.js is loaded
2. Check that `x-data="lightbox"` is present
3. Ensure images array is properly formatted
4. Check browser console for JavaScript errors

## Performance Tips

1. **Enable Caching:**
   ```php
   // In your controller
   $categories = Cache::remember('research.categories', 3600, function () {
       return Category::withCount('research')->get();
   });
   ```

2. **Optimize Images:**
   - Use WebP format for gallery images
   - Generate thumbnails for large images
   - Implement lazy loading

3. **Reduce API Calls:**
   - Search results are already cached for 5 minutes
   - Consider increasing cache time for production

4. **Database Optimization:**
   - Add indexes on filtered columns
   - Use eager loading for relationships

## Next Steps

1. Customize component styling to match your brand
2. Add more filter options (date range, author, etc.)
3. Implement search suggestions/autocomplete
4. Add analytics tracking for search queries
5. Enhance lightbox with zoom functionality

## Support

For issues or questions:
- Check the detailed usage guide: `ALPINE_COMPONENTS_USAGE.md`
- Review the implementation summary: `ALPINE_COMPONENTS_SUMMARY.md`
- Check Alpine.js documentation: https://alpinejs.dev/

---

**Last Updated:** October 9, 2025
