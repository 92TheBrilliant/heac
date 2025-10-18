<?php

namespace App\Observers;

use App\Models\Page;
use App\Services\CacheService;

class PageObserver
{
    public function __construct(
        protected CacheService $cacheService
    ) {}

    /**
     * Handle the Page "created" event.
     */
    public function created(Page $page): void
    {
        $this->clearCaches();
    }

    /**
     * Handle the Page "updated" event.
     */
    public function updated(Page $page): void
    {
        $this->clearCaches();
    }

    /**
     * Handle the Page "deleted" event.
     */
    public function deleted(Page $page): void
    {
        $this->clearCaches();
    }

    /**
     * Clear relevant caches
     */
    protected function clearCaches(): void
    {
        $this->cacheService->clearPageCache();
        $this->cacheService->clearHomepageCache();
    }
}
