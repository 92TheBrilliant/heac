<?php

namespace App\Filament\Widgets;

use App\Models\Page;
use App\Models\Research;
use App\Models\ContactInquiry;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Collection;

class RecentActivityWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // We'll use a custom query to combine different models
                fn () => $this->getRecentActivities()
            )
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Page' => 'info',
                        'Research' => 'success',
                        'Inquiry' => 'warning',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'Page' => 'heroicon-o-document-text',
                        'Research' => 'heroicon-o-academic-cap',
                        'Inquiry' => 'heroicon-o-envelope',
                        default => 'heroicon-o-bell',
                    }),

                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->limit(60)
                    ->tooltip(fn ($record) => $record['title']),

                Tables\Columns\TextColumn::make('action')
                    ->label('Action')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Created' => 'success',
                        'Updated' => 'info',
                        'Submitted' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'warning',
                        'archived' => 'gray',
                        'new' => 'danger',
                        'in_progress' => 'info',
                        'resolved' => 'success',
                        'closed' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => $state ? ucfirst(str_replace('_', ' ', $state)) : 'N/A'),

                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->heading('Recent Activity')
            ->description('Latest updates across pages, research, and inquiries')
            ->defaultSort('date', 'desc')
            ->paginated([10, 25, 50]);
    }

    protected function getRecentActivities(): Collection
    {
        $activities = collect();

        // Get recent pages
        $recentPages = Page::latest('updated_at')
            ->limit(20)
            ->get()
            ->map(function ($page) {
                return [
                    'id' => 'page-' . $page->id,
                    'type' => 'Page',
                    'title' => $page->title,
                    'action' => $page->created_at->eq($page->updated_at) ? 'Created' : 'Updated',
                    'status' => $page->status,
                    'date' => $page->updated_at,
                ];
            });

        // Get recent research
        $recentResearch = Research::latest('updated_at')
            ->limit(20)
            ->get()
            ->map(function ($research) {
                return [
                    'id' => 'research-' . $research->id,
                    'type' => 'Research',
                    'title' => $research->title,
                    'action' => $research->created_at->eq($research->updated_at) ? 'Created' : 'Updated',
                    'status' => $research->status,
                    'date' => $research->updated_at,
                ];
            });

        // Get recent inquiries
        $recentInquiries = ContactInquiry::latest('created_at')
            ->limit(20)
            ->get()
            ->map(function ($inquiry) {
                return [
                    'id' => 'inquiry-' . $inquiry->id,
                    'type' => 'Inquiry',
                    'title' => $inquiry->subject ?? 'Contact from ' . $inquiry->name,
                    'action' => 'Submitted',
                    'status' => $inquiry->status,
                    'date' => $inquiry->created_at,
                ];
            });

        // Combine and sort by date
        $activities = $activities
            ->concat($recentPages)
            ->concat($recentResearch)
            ->concat($recentInquiries)
            ->sortByDesc('date')
            ->take(50);

        // Convert to a query builder-like structure for Filament
        return $activities;
    }
}
