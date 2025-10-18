<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get locale from session, or use default
        $locale = session('locale', config('app.locale'));

        // Validate locale is supported
        if (!array_key_exists($locale, config('translatable.locales', []))) {
            $locale = config('app.locale');
        }

        // Set application locale
        app()->setLocale($locale);

        return $next($request);
    }
}
