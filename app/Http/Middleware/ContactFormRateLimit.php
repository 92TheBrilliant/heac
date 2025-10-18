<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ContactFormRateLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Create a unique key based on IP address
        $key = 'contact-form:' . $request->ip();
        
        // Allow 3 submissions per hour
        $maxAttempts = 3;
        $decayMinutes = 60;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            $minutes = ceil($seconds / 60);

            // Log rate limit hit
            logger()->warning('Contact form rate limit exceeded', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'available_in' => $seconds,
            ]);

            return back()
                ->withErrors([
                    'rate_limit' => "You have submitted too many contact forms. Please try again in {$minutes} minute(s)."
                ])
                ->withInput();
        }

        // Process the request
        $response = $next($request);

        // Only increment the rate limiter if the form was successfully submitted
        // Check if the response is a redirect with success message
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            $session = $request->session();
            if ($session->has('success')) {
                RateLimiter::hit($key, $decayMinutes * 60);
            }
        }

        return $response;
    }
}
