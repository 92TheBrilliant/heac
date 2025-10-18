<?php

namespace App\Observers;

use App\Models\Research;
use App\Services\CacheService;

class ResearchObserver
{
    public function __construct(
        protected CacheService $cacheService
    ) {}

    /**
     * Handle the Research "created" event.
     */
    public function created(Research $research): void
    {
        $this->clearCaches();
    }

    /**
     * Handle the Research "updated" event.
     */
    public function updated(Research $research): void
    {
        $this->clearCaches();
    }

    /**
     * Handle the Research "deleted" event.
     */
    public function deleted(Research $research): void
    {
        $this->clearCaches();
    }

    /**
     * Clear relevant caches
     */
    protected function clearCaches(): void
    {
        $this->cacheService->clearResearchCache();
        $this->cacheService->clearHomepageCache();
    }
}
