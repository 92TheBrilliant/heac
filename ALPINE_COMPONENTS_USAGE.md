# Alpine.js Interactive Components Usage Guide

This document provides instructions on how to use the Alpine.js interactive components implemented for the HEAC CMS.

## Components Overview

1. **Search Component** - Live search with debounced API calls
2. **Mobile Navigation** - Responsive hamburger menu with slide-in animation
3. **Filter Sidebar** - Collapsible filter groups for research browsing
4. **Image Gallery with Lightbox** - Full-screen image viewer with keyboard navigation

---

## 1. Search Component

### Usage

Add the search component to your Blade template:

```blade
<x-search />
```

### Features

- Debounced search (300ms delay)
- Live results dropdown
- Keyboard navigation (Arrow Up/Down, Enter, Escape)
- Loading indicator
- Clear button
- Displays both research and page results

### API Endpoint

The component calls `/api/search?q={query}` which returns:

```json
{
  "results": [
    {
      "id": "research-1",
      "type": "research",
      "title": "Research Title",
      "excerpt": "Brief description...",
      "url": "/research/slug"
    }
  ]
}
```

---

## 2. Mobile Navigation

### Usage

Add the mobile navigation component to your layout:

```blade
<x-mobile-nav :navigation="$navigationItems" />
```

### Props

- `navigation` (array, optional): Additional navigation items
  ```php
  $navigationItems = [
      ['title' => 'About', 'url' => '/about'],
      ['title' => 'Services', 'url' => '/services']
  ];
  ```

### Features

- Hamburger menu toggle
- Slide-in animation from left
- Body scroll locking when open
- Backdrop overlay
- Responsive (hidden on desktop)
- Keyboard support (Escape to close)
- Auto-close on window resize

---

## 3. Filter Sidebar

### Usage

Add the filter sidebar to your research index page:

```blade
<x-filter-sidebar 
    :categories="$categories"
    :tags="$tags"
    :years="$years"
/>
```

### Props

- `categories` (Collection): Categories with research count
- `tags` (Collection): Tags with research count
- `years` (Collection): Years with research count

### Example Controller Code

```php
public function index(Request $request)
{
    $categories = Category::withCount('research')->get();
    $tags = Tag::withCount('research')->get();
    $years = Research::selectRaw('YEAR(publication_date) as year, COUNT(*) as count')
        ->groupBy('year')
        ->orderBy('year', 'desc')
        ->get();

    return view('research.index', compact('categories', 'tags', 'years'));
}
```

### Features

- Collapsible filter groups
- Checkbox filters with counts
- Radio button for year and sort
- Active filters display with badges
- Clear all filters button
- URL-based filtering (maintains state on page reload)
- Filter count indicator

### URL Parameters

The component generates URLs with these parameters:
- `categories[]` - Array of category IDs
- `tags[]` - Array of tag IDs
- `year` - Publication year
- `sort` - Sort option (latest, oldest, popular, title)

---

## 4. Image Gallery with Lightbox

### Usage Option 1: Using the Gallery Component

```blade
<x-gallery 
    :images="$images"
    :columns="3"
    :gap="4"
/>
```

### Props

- `images` (array): Array of image data
  ```php
  $images = [
      [
          'src' => '/storage/images/full-size.jpg',
          'thumbnail' => '/storage/images/thumb.jpg',
          'alt' => 'Image description',
          'title' => 'Image Title',
          'description' => 'Detailed description'
      ]
  ];
  ```
- `columns` (int, default: 3): Number of grid columns
- `gap` (int, default: 4): Gap between images (Tailwind spacing)

### Usage Option 2: Standalone Lightbox

```blade
<x-lightbox :images="$images" />

<!-- Trigger from custom HTML -->
<div x-data>
    <img 
        src="image.jpg" 
        @click="$dispatch('open-lightbox', { 
            images: [{ src: 'image.jpg', alt: 'Description' }], 
            index: 0 
        })"
    />
</div>
```

### Features

- Full-screen modal view
- Previous/Next navigation
- Thumbnail navigation (for up to 10 images)
- Keyboard controls:
  - Arrow Left/Right: Navigate images
  - Escape: Close lightbox
- Touch-friendly on mobile
- Image title and description display
- Image counter (1/5)
- Body scroll locking
- Smooth transitions

---

## Installation & Setup

### 1. Install Alpine.js

```bash
npm install alpinejs --save-dev
```

### 2. Import in resources/js/app.js

```javascript
import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Import components
import './components/search';
import './components/mobileNav';
import './components/filterSidebar';
import './components/lightbox';

Alpine.start();
```

### 3. Build Assets

```bash
npm run build
# or for development
npm run dev
```

### 4. Include in Layout

Make sure your layout includes the compiled JavaScript:

```blade
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

---

## Customization

### Styling

All components use Tailwind CSS classes. You can customize the appearance by:

1. Modifying the Blade component files in `resources/views/components/`
2. Updating Tailwind configuration in `tailwind.config.js`
3. Adding custom CSS in `resources/css/app.css`

### Behavior

Component behavior can be customized by modifying the Alpine.js data functions in:
- `resources/js/components/search.js`
- `resources/js/components/mobileNav.js`
- `resources/js/components/filterSidebar.js`
- `resources/js/components/lightbox.js`

---

## Browser Support

These components work in all modern browsers that support:
- ES6 JavaScript
- CSS Grid and Flexbox
- CSS Transitions

Tested on:
- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

---

## Troubleshooting

### Search not working

1. Ensure the API route is registered in `routes/web.php`
2. Check that the `search()` method exists in `HomeController`
3. Verify the API returns the correct JSON format

### Mobile menu not closing

1. Check that Alpine.js is loaded before the component
2. Verify no JavaScript errors in the console
3. Ensure the `x-data="mobileNav"` attribute is present

### Filters not applying

1. Verify the filter data is passed to the component
2. Check that the URL parameters are being read by the controller
3. Ensure the research query applies the filters

### Lightbox images not loading

1. Check that image paths are correct
2. Verify the images array is properly formatted
3. Ensure the lightbox component is included in the page

---

## Performance Tips

1. **Search**: Results are cached for 5 minutes to reduce database queries
2. **Images**: Use lazy loading (`loading="lazy"`) for gallery images
3. **Filters**: Consider caching filter counts for better performance
4. **Mobile Menu**: Body scroll locking prevents layout shifts

---

## Accessibility

All components follow accessibility best practices:

- Semantic HTML elements
- ARIA labels and attributes
- Keyboard navigation support
- Focus management
- Screen reader friendly
- Color contrast compliance

---

## Future Enhancements

Potential improvements for these components:

1. **Search**: Add search history and suggestions
2. **Mobile Menu**: Add nested menu support
3. **Filters**: Add range sliders for numeric filters
4. **Lightbox**: Add zoom functionality and image download

---

For more information about Alpine.js, visit: https://alpinejs.dev/
