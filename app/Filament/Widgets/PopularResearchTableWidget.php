<?php

namespace App\Filament\Widgets;

use App\Models\Analytic;
use App\Models\Research;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class PopularResearchTableWidget extends BaseWidget
{
    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        // Get research IDs with most views in the last 30 days
        $popularResearchIds = Analytic::researchViews()
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->selectRaw('trackable_id, COUNT(*) as view_count')
            ->groupBy('trackable_id')
            ->orderByDesc('view_count')
            ->limit(10)
            ->pluck('trackable_id');

        return $table
            ->heading('Most Popular Research (Last 30 Days)')
            ->query(
                Research::query()
                    ->whereIn('id', $popularResearchIds)
                    ->orderByRaw('FIELD(id, ' . $popularResearchIds->implode(',') . ')')
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                
                Tables\Columns\TextColumn::make('views_count')
                    ->label('Total Views')
                    ->sortable()
                    ->alignEnd(),
                
                Tables\Columns\TextColumn::make('downloads_count')
                    ->label('Total Downloads')
                    ->sortable()
                    ->alignEnd(),
                
                Tables\Columns\TextColumn::make('recent_views')
                    ->label('Views (30d)')
                    ->getStateUsing(function (Research $record) {
                        return Analytic::researchViews()
                            ->where('trackable_id', $record->id)
                            ->where('created_at', '>=', Carbon::now()->subDays(30))
                            ->count();
                    })
                    ->alignEnd()
                    ->badge()
                    ->color('success'),
                
                Tables\Columns\TextColumn::make('recent_downloads')
                    ->label('Downloads (30d)')
                    ->getStateUsing(function (Research $record) {
                        return Analytic::researchDownloads()
                            ->where('trackable_id', $record->id)
                            ->where('created_at', '>=', Carbon::now()->subDays(30))
                            ->count();
                    })
                    ->alignEnd()
                    ->badge()
                    ->color('primary'),
            ])
            ->paginated(false);
    }
}
