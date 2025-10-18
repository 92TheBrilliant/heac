<?php

namespace App\Http\Middleware;

use App\Models\Analytic;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TrackPageViews
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only track successful GET requests to public pages
        if (
            $request->isMethod('GET') &&
            $response->isSuccessful() &&
            !$request->is('admin/*') &&
            !$request->is('livewire/*') &&
            !$request->is('filament/*') &&
            !$request->ajax()
        ) {
            $this->trackPageView($request);
        }

        return $response;
    }

    /**
     * Track the page view
     */
    protected function trackPageView(Request $request): void
    {
        try {
            Analytic::create([
                'event_type' => 'page_view',
                'url' => $request->fullUrl(),
                'referrer' => $request->header('referer'),
                'user_agent' => $request->userAgent(),
                'ip_address' => $request->ip(),
                'user_id' => Auth::id(),
                'metadata' => [
                    'route_name' => $request->route()?->getName(),
                    'method' => $request->method(),
                ],
            ]);
        } catch (\Exception $e) {
            // Silently fail to not disrupt user experience
            logger()->error('Failed to track page view: ' . $e->getMessage());
        }
    }
}
