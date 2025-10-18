<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Research;
use App\Services\ContentService;
use App\Services\ResearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        protected ContentService $contentService,
        protected ResearchService $researchService,
        protected \App\Services\SEOService $seoService
    ) {}

    /**
     * Display the homepage with featured content and statistics
     */
    public function index(): View
    {
        // Cache homepage data for 1 hour
        $data = Cache::remember('homepage.data', 3600, function () {
            return [
                'featuredResearch' => $this->researchService->getFeaturedResearch(3),
                'latestPages' => $this->getLatestPages(2),
                'statistics' => $this->getStatistics(),
            ];
        });

        // Cache organization data for 24 hours
        $data['organizationData'] = Cache::remember('organization.data', 86400, function () {
            return $this->seoService->generateOrganizationStructuredData();
        });

        return view('home', $data);
    }

    /**
     * Get latest published pages/news
     */
    protected function getLatestPages(int $limit = 3)
    {
        return Page::published()
            ->latest('published_at')
            ->limit($limit)
            ->get(['id', 'title', 'slug', 'excerpt', 'published_at']);
    }

    /**
     * Get key statistics for homepage display
     */
    protected function getStatistics(): array
    {
        return [
            [
                'value' => '100+',
                'label' => 'Clients Served Globally',
            ],
            [
                'value' => '10+',
                'label' => 'Years of Excellence',
            ],
            [
                'value' => number_format(Research::published()->count()) . '+',
                'label' => 'Publications & Fatwas',
            ],
        ];
    }

    /**
     * Search API endpoint for live search
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->input('q', '');
        
        if (strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        // Cache search results for 5 minutes
        $cacheKey = 'search.' . md5($query);
        
        $results = Cache::remember($cacheKey, 300, function () use ($query) {
            $searchResults = [];

            // Search in research
            $research = Research::published()
                ->where(function ($q) use ($query) {
                    $q->where('title', 'LIKE', "%{$query}%")
                      ->orWhere('abstract', 'LIKE', "%{$query}%");
                })
                ->limit(5)
                ->get(['id', 'title', 'slug', 'abstract']);

            foreach ($research as $item) {
                $searchResults[] = [
                    'id' => 'research-' . $item->id,
                    'type' => 'research',
                    'title' => $item->title,
                    'excerpt' => $item->abstract ? substr($item->abstract, 0, 150) . '...' : null,
                    'url' => route('research.show', $item->slug),
                ];
            }

            // Search in pages
            $pages = Page::published()
                ->where(function ($q) use ($query) {
                    $q->where('title', 'LIKE', "%{$query}%")
                      ->orWhere('excerpt', 'LIKE', "%{$query}%");
                })
                ->limit(5)
                ->get(['id', 'title', 'slug', 'excerpt']);

            foreach ($pages as $item) {
                $searchResults[] = [
                    'id' => 'page-' . $item->id,
                    'type' => 'page',
                    'title' => $item->title,
                    'excerpt' => $item->excerpt,
                    'url' => route('page.show', $item->slug),
                ];
            }

            return $searchResults;
        });

        return response()->json(['results' => $results]);
    }
}
