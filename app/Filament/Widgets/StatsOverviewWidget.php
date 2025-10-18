<?php

namespace App\Filament\Widgets;

use App\Models\Page;
use App\Models\Research;
use App\Models\Media;
use App\Models\ContactInquiry;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Get current counts
        $totalPages = Page::count();
        $publishedPages = Page::published()->count();
        $totalResearch = Research::count();
        $publishedResearch = Research::published()->count();
        $totalMedia = Media::count();
        $newInquiries = ContactInquiry::new()->count();

        // Get counts from 7 days ago for trend calculation
        $pagesLastWeek = Page::where('created_at', '<=', now()->subDays(7))->count();
        $researchLastWeek = Research::where('created_at', '<=', now()->subDays(7))->count();
        $mediaLastWeek = Media::where('created_at', '<=', now()->subDays(7))->count();

        // Calculate trends
        $pagesTrend = $pagesLastWeek > 0 
            ? round((($totalPages - $pagesLastWeek) / $pagesLastWeek) * 100, 1)
            : 0;
        $researchTrend = $researchLastWeek > 0 
            ? round((($totalResearch - $researchLastWeek) / $researchLastWeek) * 100, 1)
            : 0;
        $mediaTrend = $mediaLastWeek > 0 
            ? round((($totalMedia - $mediaLastWeek) / $mediaLastWeek) * 100, 1)
            : 0;

        // Get recent activity statistics
        $recentPages = Page::where('created_at', '>=', now()->subDays(7))->count();
        $recentResearch = Research::where('created_at', '>=', now()->subDays(7))->count();
        $recentInquiries = ContactInquiry::where('created_at', '>=', now()->subDays(7))->count();

        return [
            Stat::make('Total Pages', $totalPages)
                ->description("{$publishedPages} published")
                ->descriptionIcon('heroicon-m-document-text')
                ->chart([$pagesLastWeek, $totalPages])
                ->color('success')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

            Stat::make('Research Publications', $totalResearch)
                ->description("{$publishedResearch} published")
                ->descriptionIcon('heroicon-m-academic-cap')
                ->chart([$researchLastWeek, $totalResearch])
                ->color('info')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

            Stat::make('Media Files', $totalMedia)
                ->description($mediaTrend >= 0 ? "+{$mediaTrend}% from last week" : "{$mediaTrend}% from last week")
                ->descriptionIcon($mediaTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart([$mediaLastWeek, $totalMedia])
                ->color($mediaTrend >= 0 ? 'success' : 'danger')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),

            Stat::make('New Inquiries', $newInquiries)
                ->description("{$recentInquiries} this week")
                ->descriptionIcon('heroicon-m-envelope')
                ->color($newInquiries > 0 ? 'warning' : 'gray')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),
        ];
    }
}
