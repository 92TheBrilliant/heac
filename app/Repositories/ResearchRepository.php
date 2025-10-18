<?php

namespace App\Repositories;

use App\Models\Research;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ResearchRepository
{
    /**
     * Cache duration in seconds (15 minutes)
     */
    private const CACHE_TTL = 900;

    /**
     * Cache duration for popular research (5 minutes)
     */
    private const POPULAR_CACHE_TTL = 300;

    /**
     * Find research by ID with eager loading.
     */
    public function findById(int $id, array $relations = []): ?Research
    {
        $cacheKey = "research.{$id}." . md5(json_encode($relations));

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($id, $relations) {
            $query = Research::query();

            if (!empty($relations)) {
                $query->with($relations);
            }

            return $query->find($id);
        });
    }

    /**
     * Find research by slug with eager loading.
     */
    public function findBySlug(string $slug, array $relations = ['categories', 'tags']): ?Research
    {
        $cacheKey = "research.slug.{$slug}." . md5(json_encode($relations));

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($slug, $relations) {
            $query = Research::query();

            if (!empty($relations)) {
                $query->with($relations);
            }

            return $query->where('slug', $slug)->first();
        });
    }

    /**
     * Search research with full-text search and filters.
     */
    public function search(
        ?string $query = null,
        array $filters = [],
        int $perPage = 15,
        string $sortBy = 'publication_date',
        string $sortDirection = 'desc'
    ): LengthAwarePaginator {
        $builder = Research::published();

        // Full-text search
        if ($query) {
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

        // Eager load relationships
        $builder->with(['categories', 'tags']);

        // Sorting
        $builder->orderBy($sortBy, $sortDirection);

        return $builder->paginate($perPage);
    }

    /**
     * Get published research with eager loading.
     */
    public function getPublished(array $relations = ['categories', 'tags'], int $perPage = 15): LengthAwarePaginator
    {
        $query = Research::published()->latest();

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->paginate($perPage);
    }

    /**
     * Get featured research.
     */
    public function getFeatured(int $limit = 6, array $relations = ['categories', 'tags']): Collection
    {
        $cacheKey = "research.featured.{$limit}." . md5(json_encode($relations));

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($limit, $relations) {
            $query = Research::published()->featured()->latest();

            if (!empty($relations)) {
                $query->with($relations);
            }

            return $query->limit($limit)->get();
        });
    }

    /**
     * Get research by category.
     */
    public function getByCategory(
        Category $category,
        int $perPage = 15,
        array $relations = ['categories', 'tags']
    ): LengthAwarePaginator {
        $query = Research::published()
            ->whereHas('categories', function ($q) use ($category) {
                $q->where('categories.id', $category->id);
            })
            ->latest();

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->paginate($perPage);
    }

    /**
     * Get research by tag.
     */
    public function getByTag(
        Tag $tag,
        int $perPage = 15,
        array $relations = ['categories', 'tags']
    ): LengthAwarePaginator {
        $query = Research::published()
            ->whereHas('tags', function ($q) use ($tag) {
                $q->where('tags.id', $tag->id);
            })
            ->latest();

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->paginate($perPage);
    }

    /**
     * Get popular research (most viewed).
     */
    public function getPopular(int $limit = 10, array $relations = ['categories', 'tags']): Collection
    {
        $cacheKey = "research.popular.{$limit}." . md5(json_encode($relations));

        return Cache::remember($cacheKey, self::POPULAR_CACHE_TTL, function () use ($limit, $relations) {
            $query = Research::published()->popular();

            if (!empty($relations)) {
                $query->with($relations);
            }

            return $query->limit($limit)->get();
        });
    }

    /**
     * Get most downloaded research.
     */
    public function getMostDownloaded(int $limit = 10, array $relations = ['categories', 'tags']): Collection
    {
        $cacheKey = "research.most_downloaded.{$limit}." . md5(json_encode($relations));

        return Cache::remember($cacheKey, self::POPULAR_CACHE_TTL, function () use ($limit, $relations) {
            $query = Research::published()->orderBy('downloads_count', 'desc');

            if (!empty($relations)) {
                $query->with($relations);
            }

            return $query->limit($limit)->get();
        });
    }

    /**
     * Get latest research.
     */
    public function getLatest(int $limit = 10, array $relations = ['categories', 'tags']): Collection
    {
        $cacheKey = "research.latest.{$limit}." . md5(json_encode($relations));

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($limit, $relations) {
            $query = Research::published()->latest();

            if (!empty($relations)) {
                $query->with($relations);
            }

            return $query->limit($limit)->get();
        });
    }

    /**
     * Get related research based on categories and tags.
     */
    public function getRelated(Research $research, int $limit = 5): Collection
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
     * Get all research (for admin).
     */
    public function getAll(array $relations = ['categories', 'tags']): Collection
    {
        $query = Research::latest();

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->get();
    }

    /**
     * Get research statistics.
     */
    public function getStatistics(): array
    {
        $cacheKey = 'research.statistics';

        return Cache::remember($cacheKey, self::CACHE_TTL, function () {
            return [
                'total' => Research::count(),
                'published' => Research::published()->count(),
                'featured' => Research::published()->featured()->count(),
                'total_views' => Research::sum('views_count'),
                'total_downloads' => Research::sum('downloads_count'),
            ];
        });
    }

    /**
     * Clear cache for specific research.
     */
    public function clearCache(?int $researchId = null, ?string $slug = null): void
    {
        if ($researchId) {
            Cache::forget("research.{$researchId}.*");
        }

        if ($slug) {
            Cache::forget("research.slug.{$slug}.*");
        }

        // Clear general caches
        Cache::forget('research.featured.*');
        Cache::forget('research.popular.*');
        Cache::forget('research.most_downloaded.*');
        Cache::forget('research.latest.*');
        Cache::forget('research.statistics');
    }

    /**
     * Clear all research caches.
     */
    public function clearAllCache(): void
    {
        Cache::tags(['research'])->flush();
    }
}
