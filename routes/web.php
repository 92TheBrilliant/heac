<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ResearchController;
use Illuminate\Support\Facades\Route;

// Locale switching
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

// Homepage
Route::get('/', [HomeController::class, 'index'])->name('home');

// Search API
Route::get('/api/search', [HomeController::class, 'search'])->name('api.search');

// Contact routes
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'store'])
    ->middleware(['honeypot', 'contact.rate.limit'])
    ->name('contact.store');

// Research routes
Route::prefix('research')->name('research.')->group(function () {
    Route::get('/', [ResearchController::class, 'index'])->name('index');
    Route::get('/category/{slug}', [ResearchController::class, 'category'])->name('category');
    Route::get('/tag/{slug}', [ResearchController::class, 'tag'])->name('tag');
    Route::get('/{slug}', [ResearchController::class, 'show'])->name('show');
    Route::get('/{slug}/download', [ResearchController::class, 'download'])->name('download');
});

// Dynamic pages (catch-all route - must be last)
Route::get('/{slug}', [PageController::class, 'show'])->name('page.show');
