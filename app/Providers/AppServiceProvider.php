<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register model observers for cache invalidation
        \App\Models\Page::observe(\App\Observers\PageObserver::class);
        \App\Models\Research::observe(\App\Observers\ResearchObserver::class);
    }
}
