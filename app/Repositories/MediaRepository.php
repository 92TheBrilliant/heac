<?php

namespace App\Repositories;

use App\Models\Media;
use App\Models\MediaFolder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class MediaRepository
{
    /**
     * Cache duration in seconds (15 minutes)
     */
    private const CACHE_TTL = 900;

    /**
     * Find media by ID with eager loading.
     */
    public function findById(int $id, array $relations = []): ?Media
    {
        $query = Media::query();

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->find($id);
    }

    /**
     * Get all media with pagination.
     */
    public function getAll(int $perPage = 20, array $relations = ['folder']): LengthAwarePaginator
    {
        $query = Media::orderBy('created_at', 'desc');

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->paginate($perPage);
    }

    /**
     * Search media by name or filename.
     */
    public function search(
        string $query,
        array $filters = [],
        int $perPage = 20
    ): LengthAwarePaginator {
        $builder = Media::query();

        // Search by name or filename
        $builder->where(function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
                ->orWhere('file_name', 'like', "%{$query}%")
                ->orWhere('alt_text', 'like', "%{$query}%");
        });

        // Apply filters
        $this->applyFilters($builder, $filters);

        return $builder->with(['folder'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get media by folder.
     */
    public function getByFolder(
        ?int $folderId = null,
        int $perPage = 20,
        array $relations = ['folder']
    ): LengthAwarePaginator {
        $query = Media::query();

        if ($folderId === null) {
            $query->whereNull('folder_id');
        } else {
            $query->where('folder_id', $folderId);
        }

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Get media by type (images, documents, etc.).
     */
    public function getByType(
        string $type,
        int $perPage = 20,
        array $relations = ['folder']
    ): LengthAwarePaginator {
        $query = Media::query();

        switch ($type) {
            case 'image':
                $query->where('mime_type', 'like', 'image/%');
                break;
            case 'document':
                $query->whereIn('mime_type', [
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ]);
                break;
            case 'video':
                $query->where('mime_type', 'like', 'video/%');
                break;
            case 'audio':
                $query->where('mime_type', 'like', 'audio/%');
                break;
            default:
                $query->where('mime_type', $type);
        }

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Get images only.
     */
    public function getImages(int $perPage = 20): LengthAwarePaginator
    {
        return $this->getByType('image', $perPage);
    }

    /**
     * Get documents only.
     */
    public function getDocuments(int $perPage = 20): LengthAwarePaginator
    {
        return $this->getByType('document', $perPage);
    }

    /**
     * Filter media with multiple criteria.
     */
    public function filter(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = Media::query();

        $this->applyFilters($query, $filters);

        return $query->with(['folder'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get recent media.
     */
    public function getRecent(int $limit = 10, array $relations = ['folder']): Collection
    {
        $cacheKey = "media.recent.{$limit}." . md5(json_encode($relations));

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($limit, $relations) {
            $query = Media::orderBy('created_at', 'desc');

            if (!empty($relations)) {
                $query->with($relations);
            }

            return $query->limit($limit)->get();
        });
    }

    /**
     * Get media statistics.
     */
    public function getStatistics(): array
    {
        $cacheKey = 'media.statistics';

        return Cache::remember($cacheKey, self::CACHE_TTL, function () {
            return [
                'total' => Media::count(),
                'images' => Media::where('mime_type', 'like', 'image/%')->count(),
                'documents' => Media::whereIn('mime_type', [
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                ])->count(),
                'total_size' => Media::sum('size'),
            ];
        });
    }

    /**
     * Get unused media (not referenced anywhere).
     * Note: This is a placeholder - actual implementation would need to check
     * references in pages, research, etc.
     */
    public function getUnused(int $perPage = 20): LengthAwarePaginator
    {
        // This would need custom logic to check if media is used
        // For now, return empty collection
        return Media::query()->whereRaw('1 = 0')->paginate($perPage);
    }

    /**
     * Get media by IDs.
     */
    public function getByIds(array $ids, array $relations = ['folder']): Collection
    {
        $query = Media::whereIn('id', $ids);

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->get();
    }

    /**
     * Apply filters to query builder.
     */
    private function applyFilters($query, array $filters): void
    {
        // Filter by folder
        if (isset($filters['folder_id'])) {
            if ($filters['folder_id'] === null || $filters['folder_id'] === 'null') {
                $query->whereNull('folder_id');
            } else {
                $query->where('folder_id', $filters['folder_id']);
            }
        }

        // Filter by mime type
        if (!empty($filters['mime_type'])) {
            $query->where('mime_type', $filters['mime_type']);
        }

        // Filter by type category
        if (!empty($filters['type'])) {
            switch ($filters['type']) {
                case 'image':
                    $query->where('mime_type', 'like', 'image/%');
                    break;
                case 'document':
                    $query->whereIn('mime_type', [
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    ]);
                    break;
                case 'video':
                    $query->where('mime_type', 'like', 'video/%');
                    break;
                case 'audio':
                    $query->where('mime_type', 'like', 'audio/%');
                    break;
            }
        }

        // Filter by date range
        if (!empty($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        // Filter by size range
        if (!empty($filters['size_min'])) {
            $query->where('size', '>=', $filters['size_min']);
        }

        if (!empty($filters['size_max'])) {
            $query->where('size', '<=', $filters['size_max']);
        }
    }

    /**
     * Clear cache for specific media.
     */
    public function clearCache(?int $mediaId = null): void
    {
        if ($mediaId) {
            Cache::forget("media.{$mediaId}.*");
        }

        // Clear general caches
        Cache::forget('media.recent.*');
        Cache::forget('media.statistics');
    }

    /**
     * Clear all media caches.
     */
    public function clearAllCache(): void
    {
        Cache::tags(['media'])->flush();
    }
}
