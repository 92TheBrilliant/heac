<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CacheResponse
{
    /**
     * Handle an incoming request and cache the response.
     */
    public function handle(Request $request, Closure $next, int $minutes = 15): Response
    {
        // Only cache GET requests
        if (!$request->isMethod('GET')) {
            return $next($request);
        }

        // Don't cache authenticated requests
        if ($request->user()) {
            return $next($request);
        }

        // Generate cache key from URL and query parameters
        $cacheKey = 'page_cache:' . md5($request->fullUrl());

        // Try to get cached response
        if (Cache::has($cacheKey)) {
            $cachedResponse = Cache::get($cacheKey);
            
            return response($cachedResponse['content'])
                ->withHeaders($cachedResponse['headers'])
                ->header('X-Cache', 'HIT');
        }

        // Get fresh response
        $response = $next($request);

        // Only cache successful responses
        if ($response->isSuccessful() && $response->getStatusCode() === 200) {
            $cacheData = [
                'content' => $response->getContent(),
                'headers' => $response->headers->all(),
            ];

            Cache::put($cacheKey, $cacheData, now()->addMinutes($minutes));
            
            $response->header('X-Cache', 'MISS');
        }

        return $response;
    }
}
