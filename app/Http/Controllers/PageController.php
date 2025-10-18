<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Services\ContentService;
use App\Services\SEOService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class PageController extends Controller
{
    public function __construct(
        protected ContentService $contentService,
        protected SEOService $seoService
    ) {}

    /**
     * Display a page by slug with caching and SEO optimization
     */
    public function show(Request $request, string $slug): View
    {
        // Cache key based on slug
        $cacheKey = "page.{$slug}";
        
        // Try to get page from cache (15 minutes)
        $page = Cache::remember($cacheKey, 900, function () use ($slug) {
            return $this->contentService->getPageBySlug($slug);
        });

        // Handle 404 for non-existent pages
        if (!$page) {
            abort(404, 'Page not found');
        }

        // Generate SEO meta tags
        $metaTags = $this->seoService->generateMetaTags($page);
        $structuredData = $this->seoService->generateStructuredData($page);
        $openGraphTags = $this->seoService->generateOpenGraphTags($page);

        return view('pages.show', compact('page', 'metaTags', 'structuredData', 'openGraphTags'));
    }
}
