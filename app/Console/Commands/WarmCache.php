<?php

namespace App\Console\Commands;

use App\Models\Page;
use App\Models\Research;
use App\Services\CacheService;
use App\Services\ContentService;
use App\Services\ResearchService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class WarmCache extends Command
{
    protected $signature = 'cache:warm';
    protected $description = 'Warm up application caches with frequently accessed data';

    public function __construct(
        protected ContentService $contentService,
        protected ResearchService $researchService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Warming up caches...');

        // Warm homepage data
        $this->info('Caching homepage data...');
        Cache::remember('homepage.data', 900, function () {
            return [
                'featuredResearch' => $this->researchService->getFeaturedResearch(6),
                'latestPages' => Page::published()->latest('published_at')->limit(3)->get(),
                'statistics' => [
                    'total_research' => Research::published()->count(),
                    'total_downloads' => Research::published()->sum('downloads_count'),
                    'total_views' => Research::published()->sum('views_count'),
                    'total_pages' => Page::published()->count(),
                ],
            ];
        });

        // Warm popular pages
        $this->info('Caching popular pages...');
        $popularPages = Page::published()
            ->orderBy('views_count', 'desc')
            ->limit(10)
            ->get();

        foreach ($popularPages as $page) {
            $cacheKey = "page.{$page->slug}";
            Cache::remember($cacheKey, 900, function () use ($page) {
                return $this->contentService->getPageBySlug($page->slug);
            });
        }

        // Warm popular research
        $this->info('Caching popular research...');
        $popularResearch = Research::published()
            ->orderBy('views_count', 'desc')
            ->limit(10)
            ->get();

        foreach ($popularResearch as $research) {
            $cacheKey = "research.{$research->slug}";
            Cache::remember($cacheKey, 900, function () use ($research) {
                return Research::with(['categories', 'tags'])
                    ->where('slug', $research->slug)
                    ->first();
            });
        }

        $this->info('Cache warming completed successfully!');

        return Command::SUCCESS;
    }
}
