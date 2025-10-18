<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    /**
     * Cache tags for different content types
     */
    const TAG_PAGES = 'pages';
    const TAG_RESEARCH = 'research';
    const TAG_MEDIA = 'media';
    const TAG_CATEGORIES = 'categories';
    const TAG_TAGS = 'tags';

    /**
     * Clear all page caches
     */
    public function clearPageCache(): void
    {
        if ($this->supportsTagging()) {
            Cache::tags([self::TAG_PAGES])->flush();
        }
        
        // Clear specific page cache keys
        $this->clearCacheByPattern('page.*');
        $this->clearCacheByPattern('page_cache:*');
    }

    /**
     * Clear all research caches
     */
    public function clearResearchCache(): void
    {
        if ($this->supportsTagging()) {
            Cache::tags([self::TAG_RESEARCH])->flush();
        }
        
        $this->clearCacheByPattern('research.*');
        $this->clearCacheByPattern('page_cache:*/research/*');
    }

    /**
     * Clear all media caches
     */
    public function clearMediaCache(): void
    {
        if ($this->supportsTagging()) {
            Cache::tags([self::TAG_MEDIA])->flush();
        }
        
        $this->clearCacheByPattern('media.*');
    }

    /**
     * Clear homepage cache
     */
    public function clearHomepageCache(): void
    {
        Cache::forget('homepage.data');
        $this->clearCacheByPattern('page_cache:*/');
        $this->clearCacheByPattern('page_cache:*/?');
    }

    /**
     * Clear all caches
     */
    public function clearAllCache(): void
    {
        Cache::flush();
    }

    /**
     * Warm up frequently accessed caches
     */
    public function warmCache(): void
    {
        // This method can be called after deployment to pre-populate caches
        // Implementation depends on specific caching needs
    }

    /**
     * Check if the cache driver supports tagging
     */
    protected function supportsTagging(): bool
    {
        $driver = config('cache.default');
        return in_array($driver, ['redis', 'memcached', 'array']);
    }

    /**
     * Clear cache keys matching a pattern (Redis only)
     */
    protected function clearCacheByPattern(string $pattern): void
    {
        if (config('cache.default') !== 'redis') {
            return;
        }

        try {
            $redis = Cache::getRedis();
            $prefix = config('cache.prefix');
            $keys = $redis->keys($prefix . $pattern);
            
            if (!empty($keys)) {
                $redis->del($keys);
            }
        } catch (\Exception $e) {
            // Silently fail if Redis is not available
            logger()->warning('Failed to clear cache by pattern: ' . $e->getMessage());
        }
    }

    /**
     * Remember with tags
     */
    public function rememberWithTags(array $tags, string $key, int $ttl, callable $callback)
    {
        if ($this->supportsTagging()) {
            return Cache::tags($tags)->remember($key, $ttl, $callback);
        }
        
        return Cache::remember($key, $ttl, $callback);
    }
}
