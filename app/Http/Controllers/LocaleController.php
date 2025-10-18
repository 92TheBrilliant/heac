<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    /**
     * Switch the application locale.
     */
    public function switch(Request $request, string $locale): RedirectResponse
    {
        // Validate locale is supported
        if (!array_key_exists($locale, config('translatable.locales', []))) {
            abort(404);
        }

        // Store locale in session
        session(['locale' => $locale]);

        // Redirect back to previous page
        return redirect()->back();
    }
}
