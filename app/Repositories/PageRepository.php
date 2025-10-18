<?php

namespace App\Repositories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class PageRepository
{
    /**
     * Cache duration in seconds (15 minutes)
     */
    private const CACHE_TTL = 900;

    /**
     * Find a page by ID with eager loading.
     */
    public function findById(int $id, array $relations = []): ?Page
    {
        $cacheKey = "page.{$id}." . md5(json_encode($relations));

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($id, $relations) {
            $query = Page::query();

            if (!empty($relations)) {
                $query->with($relations);
            }

            return $query->find($id);
        });
    }

    /**
     * Find a page by slug with eager loading.
     */
    public function findBySlug(string $slug, array $relations = []): ?Page
    {
        $cacheKey = "page.slug.{$slug}." . md5(json_encode($relations));

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($slug, $relations) {
            $query = Page::query();

            if (!empty($relations)) {
                $query->with($relations);
            }

            return $query->where('slug', $slug)->first();
        });
    }

    /**
     * Get all published pages with eager loading.
     */
    public function getPublished(array $relations = []): Collection
    {
        $cacheKey = 'pages.published.' . md5(json_encode($relations));

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($relations) {
            $query = Page::published()->ordered();

            if (!empty($relations)) {
                $query->with($relations);
            }

            return $query->get();
        });
    }

    /**
     * Get published page by slug with eager loading.
     */
    public function getPublishedBySlug(string $slug, array $relations = ['parent', 'children']): ?Page
    {
        $cacheKey = "page.published.slug.{$slug}." . md5(json_encode($relations));

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($slug, $relations) {
            $query = Page::published();

            if (!empty($relations)) {
                $query->with($relations);
            }

            return $query->where('slug', $slug)->first();
        });
    }

    /**
     * Get all root pages (pages without parent).
     */
    public function getRootPages(array $relations = []): Collection
    {
        $cacheKey = 'pages.root.' . md5(json_encode($relations));

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($relations) {
            $query = Page::whereNull('parent_id')->ordered();

            if (!empty($relations)) {
                $query->with($relations);
            }

            return $query->get();
        });
    }

    /**
     * Get children of a specific page.
     */
    public function getChildren(int $parentId, array $relations = []): Collection
    {
        $cacheKey = "pages.children.{$parentId}." . md5(json_encode($relations));

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($parentId, $relations) {
            $query = Page::where('parent_id', $parentId)->ordered();

            if (!empty($relations)) {
                $query->with($relations);
            }

            return $query->get();
        });
    }

    /**
     * Get published root pages (for navigation).
     */
    public function getPublishedRootPages(array $relations = ['children']): Collection
    {
        $cacheKey = 'pages.published.root.' . md5(json_encode($relations));

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($relations) {
            $query = Page::published()
                ->whereNull('parent_id')
                ->ordered();

            if (!empty($relations)) {
                $query->with($relations);
            }

            return $query->get();
        });
    }

    /**
     * Get all pages (for admin).
     */
    public function getAll(array $relations = []): Collection
    {
        $query = Page::ordered();

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->get();
    }

    /**
     * Get pages by status.
     */
    public function getByStatus(string $status, array $relations = []): Collection
    {
        $query = Page::where('status', $status)->ordered();

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->get();
    }

    /**
     * Clear cache for a specific page.
     */
    public function clearCache(?int $pageId = null, ?string $slug = null): void
    {
        if ($pageId) {
            Cache::forget("page.{$pageId}.*");
        }

        if ($slug) {
            Cache::forget("page.slug.{$slug}.*");
            Cache::forget("page.published.slug.{$slug}.*");
        }

        // Clear general caches
        Cache::forget('pages.published.*');
        Cache::forget('pages.root.*');
        Cache::forget('pages.published.root.*');
    }

    /**
     * Clear all page caches.
     */
    public function clearAllCache(): void
    {
        Cache::tags(['pages'])->flush();
    }
}
