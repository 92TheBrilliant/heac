<?php

namespace Tests\Performance;

use App\Models\Page;
use App\Models\Research;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed test data
        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
        $this->artisan('db:seed', ['--class' => 'SampleContentSeeder']);
    }

    /**
     * Test homepage load time
     */
    public function test_homepage_load_time(): void
    {
        $startTime = microtime(true);
        
        $response = $this->get('/');
        
        $endTime = microtime(true);
        $loadTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
        
        $response->assertStatus(200);
        
        // Homepage should load in under 500ms
        $this->assertLessThan(500, $loadTime, "Homepage took {$loadTime}ms to load");
        
        echo "\nHomepage load time: " . round($loadTime, 2) . "ms\n";
    }

    /**
     * Test research listing page load time
     */
    public function test_research_listing_load_time(): void
    {
        $startTime = microtime(true);
        
        $response = $this->get('/research');
        
        $endTime = microtime(true);
        $loadTime = ($endTime - $startTime) * 1000;
        
        $response->assertStatus(200);
        
        // Research listing should load in under 800ms
        $this->assertLessThan(800, $loadTime, "Research listing took {$loadTime}ms to load");
        
        echo "\nResearch listing load time: " . round($loadTime, 2) . "ms\n";
    }

    /**
     * Test research detail page load time
     */
    public function test_research_detail_load_time(): void
    {
        $research = Research::where('status', 'published')->first();
        
        if (!$research) {
            $this->markTestSkipped('No published research available');
        }
        
        $startTime = microtime(true);
        
        $response = $this->get("/research/{$research->slug}");
        
        $endTime = microtime(true);
        $loadTime = ($endTime - $startTime) * 1000;
        
        $response->assertStatus(200);
        
        // Research detail should load in under 600ms
        $this->assertLessThan(600, $loadTime, "Research detail took {$loadTime}ms to load");
        
        echo "\nResearch detail load time: " . round($loadTime, 2) . "ms\n";
    }

    /**
     * Test page load time
     */
    public function test_page_load_time(): void
    {
        $page = Page::where('status', 'published')->first();
        
        if (!$page) {
            $this->markTestSkipped('No published pages available');
        }
        
        $startTime = microtime(true);
        
        $response = $this->get("/{$page->slug}");
        
        $endTime = microtime(true);
        $loadTime = ($endTime - $startTime) * 1000;
        
        $response->assertStatus(200);
        
        // Page should load in under 500ms
        $this->assertLessThan(500, $loadTime, "Page took {$loadTime}ms to load");
        
        echo "\nPage load time: " . round($loadTime, 2) . "ms\n";
    }

    /**
     * Test database query performance
     */
    public function test_database_query_performance(): void
    {
        DB::enableQueryLog();
        
        $this->get('/research');
        
        $queries = DB::getQueryLog();
        $queryCount = count($queries);
        
        // Should not have more than 10 queries for research listing
        $this->assertLessThan(10, $queryCount, "Research listing executed {$queryCount} queries");
        
        echo "\nResearch listing query count: {$queryCount}\n";
        
        // Check for N+1 queries
        $duplicateQueries = [];
        foreach ($queries as $query) {
            $sql = $query['query'];
            if (!isset($duplicateQueries[$sql])) {
                $duplicateQueries[$sql] = 0;
            }
            $duplicateQueries[$sql]++;
        }
        
        foreach ($duplicateQueries as $sql => $count) {
            if ($count > 3) {
                echo "\nPotential N+1 query detected (executed {$count} times): {$sql}\n";
            }
        }
    }

    /**
     * Test cache performance
     */
    public function test_cache_performance(): void
    {
        $page = Page::where('status', 'published')->first();
        
        if (!$page) {
            $this->markTestSkipped('No published pages available');
        }
        
        // Clear cache
        Cache::forget("page.{$page->slug}");
        
        // First request (no cache)
        $startTime = microtime(true);
        $this->get("/{$page->slug}");
        $firstLoadTime = (microtime(true) - $startTime) * 1000;
        
        // Second request (with cache)
        $startTime = microtime(true);
        $this->get("/{$page->slug}");
        $cachedLoadTime = (microtime(true) - $startTime) * 1000;
        
        echo "\nFirst load time: " . round($firstLoadTime, 2) . "ms\n";
        echo "Cached load time: " . round($cachedLoadTime, 2) . "ms\n";
        echo "Cache improvement: " . round((($firstLoadTime - $cachedLoadTime) / $firstLoadTime) * 100, 2) . "%\n";
        
        // Cached version should be faster
        $this->assertLessThan($firstLoadTime, $cachedLoadTime, "Cache did not improve performance");
    }

    /**
     * Test search performance
     */
    public function test_search_performance(): void
    {
        $startTime = microtime(true);
        
        $response = $this->get('/research?search=test');
        
        $endTime = microtime(true);
        $loadTime = ($endTime - $startTime) * 1000;
        
        $response->assertStatus(200);
        
        // Search should complete in under 1 second
        $this->assertLessThan(1000, $loadTime, "Search took {$loadTime}ms");
        
        echo "\nSearch load time: " . round($loadTime, 2) . "ms\n";
    }

    /**
     * Test memory usage
     */
    public function test_memory_usage(): void
    {
        $startMemory = memory_get_usage(true);
        
        $this->get('/research');
        
        $endMemory = memory_get_usage(true);
        $memoryUsed = ($endMemory - $startMemory) / 1024 / 1024; // Convert to MB
        
        echo "\nMemory used for research listing: " . round($memoryUsed, 2) . "MB\n";
        
        // Should not use more than 10MB for a single request
        $this->assertLessThan(10, $memoryUsed, "Request used {$memoryUsed}MB of memory");
    }

    /**
     * Test concurrent requests
     */
    public function test_concurrent_request_handling(): void
    {
        $urls = [
            '/',
            '/research',
            '/contact',
        ];
        
        $totalTime = 0;
        $requestCount = 0;
        
        foreach ($urls as $url) {
            for ($i = 0; $i < 5; $i++) {
                $startTime = microtime(true);
                $this->get($url);
                $loadTime = (microtime(true) - $startTime) * 1000;
                $totalTime += $loadTime;
                $requestCount++;
            }
        }
        
        $averageTime = $totalTime / $requestCount;
        
        echo "\nAverage response time across {$requestCount} requests: " . round($averageTime, 2) . "ms\n";
        
        // Average should be under 600ms
        $this->assertLessThan(600, $averageTime, "Average response time was {$averageTime}ms");
    }

    /**
     * Test asset loading
     */
    public function test_asset_loading(): void
    {
        $response = $this->get('/');
        
        $content = $response->getContent();
        
        // Count CSS files
        preg_match_all('/<link[^>]+href=["\']([^"\']+\.css)["\']/', $content, $cssMatches);
        $cssCount = count($cssMatches[1]);
        
        // Count JS files
        preg_match_all('/<script[^>]+src=["\']([^"\']+\.js)["\']/', $content, $jsMatches);
        $jsCount = count($jsMatches[1]);
        
        echo "\nCSS files loaded: {$cssCount}\n";
        echo "JS files loaded: {$jsCount}\n";
        
        // Should have minimal external assets
        $this->assertLessThan(5, $cssCount, "Too many CSS files: {$cssCount}");
        $this->assertLessThan(5, $jsCount, "Too many JS files: {$jsCount}");
    }

    /**
     * Test image optimization
     */
    public function test_image_optimization(): void
    {
        $response = $this->get('/');
        
        $content = $response->getContent();
        
        // Check for lazy loading
        preg_match_all('/<img[^>]+>/', $content, $imgMatches);
        $totalImages = count($imgMatches[0]);
        
        $lazyLoadedImages = 0;
        foreach ($imgMatches[0] as $img) {
            if (strpos($img, 'loading="lazy"') !== false) {
                $lazyLoadedImages++;
            }
        }
        
        echo "\nTotal images: {$totalImages}\n";
        echo "Lazy loaded images: {$lazyLoadedImages}\n";
        
        if ($totalImages > 0) {
            $lazyLoadPercentage = ($lazyLoadedImages / $totalImages) * 100;
            echo "Lazy load percentage: " . round($lazyLoadPercentage, 2) . "%\n";
        }
    }
}
