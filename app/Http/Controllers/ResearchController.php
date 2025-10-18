<?php

namespace App\Http\Controllers;

use App\Models\Analytic;
use App\Models\Category;
use App\Models\Research;
use App\Models\Tag;
use App\Services\ResearchService;
use App\Services\SEOService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ResearchController extends Controller
{
    public function __construct(
        protected ResearchService $researchService,
        protected SEOService $seoService
    ) {}

    /**
     * Display a listing of research with search and filters
     */
    public function index(Request $request): View
    {
        $query = $request->input('q', '');
        $categoryId = $request->input('category');
        $tagId = $request->input('tag');
        $sortBy = $request->input('sort', 'latest');

        $filters = [
            'category_id' => $categoryId,
            'tag_id' => $tagId,
            'sort_by' => $sortBy,
        ];

        // Search research with filters
        $research = $this->researchService->searchResearch($query, $filters);

        // Get categories and tags for filter sidebar
        $categories = Category::withCount('research')
            ->orderBy('name')
            ->get();
        
        $tags = Tag::withCount('research')
            ->orderBy('name')
            ->get();

        return view('research.index', compact('research', 'categories', 'tags', 'query', 'categoryId', 'tagId', 'sortBy'));
    }

    /**
     * Display individual research with SEO optimization
     */
    public function show(Request $request, string $slug): View
    {
        $research = Research::where('slug', $slug)
            ->where('status', 'published')
            ->with(['categories', 'tags'])
            ->firstOrFail();

        // Increment view count
        $this->researchService->incrementViews($research);

        // Track research view in analytics
        $this->trackResearchView($request, $research);

        // Generate SEO meta tags
        $metaTags = $this->seoService->generateMetaTags($research);
        $structuredData = $this->seoService->generateStructuredData($research);
        $openGraphTags = $this->seoService->generateOpenGraphTags($research);

        // Get related research
        $relatedResearch = $this->researchService->getRelatedResearch($research, 3);

        return view('research.show', compact('research', 'metaTags', 'structuredData', 'openGraphTags', 'relatedResearch'));
    }

    /**
     * Download research file with tracking
     */
    public function download(Request $request, string $slug): BinaryFileResponse
    {
        $research = Research::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        // Verify file exists
        if (!$research->file_path || !Storage::disk('public')->exists($research->file_path)) {
            abort(404, 'File not found');
        }

        // Increment download count
        $this->researchService->incrementDownloads($research);

        // Track research download in analytics
        $this->trackResearchDownload($request, $research);

        // Return file download
        return response()->download(
            Storage::disk('public')->path($research->file_path),
            $research->file_name ?? basename($research->file_path)
        );
    }

    /**
     * Display research by category
     */
    public function category(Request $request, string $slug): View
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        
        $research = $this->researchService->getResearchByCategory($category);

        return view('research.category', compact('category', 'research'));
    }

    /**
     * Display research by tag
     */
    public function tag(Request $request, string $slug): View
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();
        
        $research = $tag->research()
            ->where('status', 'published')
            ->orderBy('publication_date', 'desc')
            ->paginate(12);

        return view('research.tag', compact('tag', 'research'));
    }

    /**
     * Track research view in analytics
     */
    protected function trackResearchView(Request $request, Research $research): void
    {
        try {
            Analytic::create([
                'event_type' => 'research_view',
                'trackable_type' => Research::class,
                'trackable_id' => $research->id,
                'url' => $request->fullUrl(),
                'referrer' => $request->header('referer'),
                'user_agent' => $request->userAgent(),
                'ip_address' => $request->ip(),
                'user_id' => Auth::id(),
                'metadata' => [
                    'research_title' => $research->title,
                    'research_slug' => $research->slug,
                    'categories' => $research->categories->pluck('name')->toArray(),
                    'tags' => $research->tags->pluck('name')->toArray(),
                ],
            ]);
        } catch (\Exception $e) {
            logger()->error('Failed to track research view: ' . $e->getMessage());
        }
    }

    /**
     * Track research download in analytics
     */
    protected function trackResearchDownload(Request $request, Research $research): void
    {
        try {
            Analytic::create([
                'event_type' => 'research_download',
                'trackable_type' => Research::class,
                'trackable_id' => $research->id,
                'url' => $request->fullUrl(),
                'referrer' => $request->header('referer'),
                'user_agent' => $request->userAgent(),
                'ip_address' => $request->ip(),
                'user_id' => Auth::id(),
                'metadata' => [
                    'research_title' => $research->title,
                    'research_slug' => $research->slug,
                    'file_type' => $research->file_type,
                    'file_size' => $research->file_size,
                ],
            ]);
        } catch (\Exception $e) {
            logger()->error('Failed to track research download: ' . $e->getMessage());
        }
    }
}
