<?php

namespace App\Services;

use App\Models\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ContentService
{
    /**
     * Cache duration in seconds (15 minutes)
     */
    private const CACHE_DURATION = 900;

    /**
     * Get all published pages with caching.
     */
    public function getPublishedPages(): Collection
    {
        return Cache::remember('pages.published', self::CACHE_DURATION, function () {
            return Page::published()
                ->ordered()
                ->with(['parent', 'children'])
                ->get();
        });
    }

    /**
     * Get a page by slug with eager loading.
     */
    public function getPageBySlug(string $slug): ?Page
    {
        return Cache::remember("page.slug.{$slug}", self::CACHE_DURATION, function () use ($slug) {
            return Page::where('slug', $slug)
                ->with(['parent', 'children'])
                ->first();
        });
    }

    /**
     * Create a new page with automatic slug generation.
     */
    public function createPage(array $data): Page
    {
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = $this->generateUniqueSlug($data['title']);
        } else {
            $data['slug'] = $this->generateUniqueSlug($data['slug']);
        }

        $page = Page::create($data);

        // Clear cache
        $this->clearPageCache();

        return $page->load(['parent', 'children']);
    }

    /**
     * Update an existing page with slug generation if needed.
     */
    public function updatePage(Page $page, array $data): Page
    {
        // Generate new slug if title changed and slug not provided
        if (isset($data['title']) && $data['title'] !== $page->title && empty($data['slug'])) {
            $data['slug'] = $this->generateUniqueSlug($data['title'], $page->id);
        } elseif (isset($data['slug']) && $data['slug'] !== $page->slug) {
            $data['slug'] = $this->generateUniqueSlug($data['slug'], $page->id);
        }

        $page->update($data);

        // Clear cache
        $this->clearPageCache();
        Cache::forget("page.slug.{$page->slug}");

        return $page->load(['parent', 'children']);
    }

    /**
     * Delete a page with soft deletes.
     */
    public function deletePage(Page $page): bool
    {
        $slug = $page->slug;
        $deleted = $page->delete();

        if ($deleted) {
            // Clear cache
            $this->clearPageCache();
            Cache::forget("page.slug.{$slug}");
        }

        return $deleted;
    }

    /**
     * Generate a unique slug from a string.
     */
    private function generateUniqueSlug(string $string, ?int $ignoreId = null): string
    {
        $slug = Str::slug($string);
        $originalSlug = $slug;
        $counter = 1;

        while ($this->slugExists($slug, $ignoreId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Check if a slug already exists.
     */
    private function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        $query = Page::where('slug', $slug);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->exists();
    }

    /**
     * Clear all page-related cache.
     */
    private function clearPageCache(): void
    {
        Cache::forget('pages.published');
    }
}
