<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HoneypotProtection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if honeypot field is filled (spam indicator)
        if ($request->filled('website')) {
            // Log potential spam attempt
            logger()->warning('Honeypot spam detected', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
            ]);

            // Silently reject spam with fake success message
            return redirect()->back()
                ->with('success', 'Thank you for your message. We will get back to you soon.');
        }

        // Check time-based submission (form should take at least 3 seconds to fill)
        $formLoadTime = $request->input('form_load_time');
        if ($formLoadTime && is_numeric($formLoadTime)) {
            $timeTaken = time() - (int) $formLoadTime;
            
            // If form was submitted too quickly (less than 3 seconds), likely a bot
            if ($timeTaken < 3) {
                logger()->warning('Fast submission detected (possible bot)', [
                    'ip' => $request->ip(),
                    'time_taken' => $timeTaken,
                    'user_agent' => $request->userAgent(),
                ]);

                // Silently reject spam with fake success message
                return redirect()->back()
                    ->with('success', 'Thank you for your message. We will get back to you soon.');
            }
        }

        return $next($request);
    }
}
