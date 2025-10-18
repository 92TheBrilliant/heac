<?php

namespace App\Services;

use App\Models\Research;
use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ResearchService
{
    /**
     * Cache duration in seconds (15 minutes)
     */
    private const CACHE_DURATION = 900;

    /**
     * Items per page for pagination
     */
    private const PER_PAGE = 12;

    /**
     * Search research with full-text search and filters.
     */
    public function searchResearch(string $query = '', array $filters = []): LengthAwarePaginator
    {
        $builder = Research::published()
            ->with(['categories', 'tags']);

        // Full-text search if query provided
        if (!empty($query)) {
            $builder->whereRaw(
                'MATCH(title, abstract) AGAINST(? IN NATURAL LANGUAGE MODE)',
                [$query]
            );
        }

        // Filter by category
        if (!empty($filters['category_id'])) {
            $builder->whereHas('categories', function ($q) use ($filters) {
                $q->where('categories.id', $filters['category_id']);
            });
        }

        // Filter by tag
        if (!empty($filters['tag_id'])) {
            $builder->whereHas('tags', function ($q) use ($filters) {
                $q->where('tags.id', $filters['tag_id']);
            });
        }

        // Filter by date range
        if (!empty($filters['date_from'])) {
            $builder->where('publication_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('publication_date', '<=', $filters['date_to']);
        }

        // Filter by featured
        if (!empty($filters['featured'])) {
            $builder->featured();
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'latest';
        switch ($sortBy) {
            case 'popular':
                $builder->popular();
                break;
            case 'downloads':
                $builder->orderBy('downloads_count', 'desc');
                break;
            case 'oldest':
                $builder->orderBy('publication_date', 'asc');
                break;
            case 'latest':
            default:
                $builder->latest();
                break;
        }

        $perPage = $filters['per_page'] ?? self::PER_PAGE;

        return $builder->paginate($perPage);
    }

    /**
     * Get featured research for homepage.
     */
    public function getFeaturedResearch(int $limit = 6): Collection
    {
        return Cache::remember("research.featured.{$limit}", self::CACHE_DURATION, function () use ($limit) {
            return Research::published()
                ->featured()
                ->latest()
                ->with(['categories', 'tags'])
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get research by category with pagination.
     */
    public function getResearchByCategory(Category $category, int $perPage = self::PER_PAGE): LengthAwarePaginator
    {
        return Research::published()
            ->whereHas('categories', function ($query) use ($category) {
                $query->where('categories.id', $category->id);
            })
            ->with(['categories', 'tags'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Increment views count with caching to reduce database writes.
     */
    public function incrementViews(Research $research): void
    {
        $cacheKey = "research.views.{$research->id}";
        
        // Increment in cache
        $views = Cache::get($cacheKey, 0);
        Cache::put($cacheKey, $views + 1, now()->addMinutes(5));

        // Update database every 5 views or after 5 minutes
        if (($views + 1) % 5 === 0) {
            $research->incrementViews();
            Cache::forget($cacheKey);
        }
    }

    /**
     * Increment downloads count with caching to reduce database writes.
     */
    public function incrementDownloads(Research $research): void
    {
        $cacheKey = "research.downloads.{$research->id}";
        
        // Increment in cache
        $downloads = Cache::get($cacheKey, 0);
        Cache::put($cacheKey, $downloads + 1, now()->addMinutes(5));

        // Update database every 3 downloads or after 5 minutes
        if (($downloads + 1) % 3 === 0) {
            $research->incrementDownloads();
            Cache::forget($cacheKey);
        }
    }

    /**
     * Get popular research based on views.
     */
    public function getPopularResearch(int $limit = 10): Collection
    {
        return Cache::remember("research.popular.{$limit}", self::CACHE_DURATION, function () use ($limit) {
            return Research::published()
                ->popular()
                ->with(['categories', 'tags'])
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get latest research.
     */
    public function getLatestResearch(int $limit = 10): Collection
    {
        return Cache::remember("research.latest.{$limit}", self::CACHE_DURATION, function () use ($limit) {
            return Research::published()
                ->latest()
                ->with(['categories', 'tags'])
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get related research based on shared categories and tags.
     */
    public function getRelatedResearch(Research $research, int $limit = 3): Collection
    {
        $categoryIds = $research->categories->pluck('id')->toArray();
        $tagIds = $research->tags->pluck('id')->toArray();

        return Research::published()
            ->where('id', '!=', $research->id)
            ->where(function ($query) use ($categoryIds, $tagIds) {
                if (!empty($categoryIds)) {
                    $query->whereHas('categories', function ($q) use ($categoryIds) {
                        $q->whereIn('categories.id', $categoryIds);
                    });
                }
                if (!empty($tagIds)) {
                    $query->orWhereHas('tags', function ($q) use ($tagIds) {
                        $q->whereIn('tags.id', $tagIds);
                    });
                }
            })
            ->with(['categories', 'tags'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Clear research-related cache.
     */
    public function clearCache(): void
    {
        Cache::forget('research.featured.*');
        Cache::forget('research.popular.*');
        Cache::forget('research.latest.*');
    }
}
