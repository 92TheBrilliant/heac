<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Schedule sitemap generation daily at 2 AM
Schedule::command('sitemap:generate')->dailyAt('02:00');

// Schedule backup daily at 1 AM
Schedule::command('backup:run')->dailyAt('01:00');

// Schedule backup cleanup daily at 3 AM
Schedule::command('backup:clean')->dailyAt('03:00');

// Schedule backup monitoring daily at 4 AM
Schedule::command('backup:monitor')->dailyAt('04:00');

// Schedule backup health check daily at 5 AM
Schedule::command('backup:health-check')->dailyAt('05:00');

// Schedule cache warming after deployments (can be triggered manually or scheduled)
// This command should be run manually after deployments: php artisan cache:warm
// Optionally, schedule it to run daily at 6 AM to keep caches fresh
Schedule::command('cache:warm')->dailyAt('06:00');

// Schedule cleanup of old files weekly on Sunday at 3 AM
Schedule::command('cleanup:old-files')->weeklyOn(0, '03:00');
