<?php

namespace App\Filament\Widgets;

use App\Models\Research;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class PopularContentWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    
    protected int | string | array $columnSpan = 'full';

    public ?string $dateFrom = null;
    public ?string $dateTo = null;

    public function mount(): void
    {
        // Default to last 30 days
        $this->dateFrom = now()->subDays(30)->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Research::query()
                    ->published()
                    ->when($this->dateFrom, function (Builder $query) {
                        $query->where('created_at', '>=', $this->dateFrom);
                    })
                    ->when($this->dateTo, function (Builder $query) {
                        $query->where('created_at', '<=', $this->dateTo . ' 23:59:59');
                    })
                    ->orderBy('views_count', 'desc')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Research Title')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->title),

                Tables\Columns\TextColumn::make('views_count')
                    ->label('Views')
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state) => number_format($state)),

                Tables\Columns\TextColumn::make('downloads_count')
                    ->label('Downloads')
                    ->sortable()
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(fn ($state) => number_format($state)),

                Tables\Columns\TextColumn::make('publication_date')
                    ->label('Published')
                    ->date()
                    ->sortable(),

                Tables\Columns\IconColumn::make('featured')
                    ->label('Featured')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray'),
            ])
            ->heading('Most Popular Research')
            ->description('Top 10 most viewed research publications')
            ->headerActions([
                Tables\Actions\Action::make('filter')
                    ->label('Filter by Date')
                    ->icon('heroicon-o-funnel')
                    ->form([
                        DatePicker::make('dateFrom')
                            ->label('From Date')
                            ->default($this->dateFrom)
                            ->maxDate(now()),
                        DatePicker::make('dateTo')
                            ->label('To Date')
                            ->default($this->dateTo)
                            ->maxDate(now()),
                    ])
                    ->action(function (array $data): void {
                        $this->dateFrom = $data['dateFrom'];
                        $this->dateTo = $data['dateTo'];
                    }),
                Tables\Actions\Action::make('reset')
                    ->label('Reset')
                    ->icon('heroicon-o-arrow-path')
                    ->color('gray')
                    ->action(function (): void {
                        $this->dateFrom = now()->subDays(30)->format('Y-m-d');
                        $this->dateTo = now()->format('Y-m-d');
                    }),
            ])
            ->paginated(false);
    }

    protected function getTableHeading(): string
    {
        $from = \Carbon\Carbon::parse($this->dateFrom)->format('M d, Y');
        $to = \Carbon\Carbon::parse($this->dateTo)->format('M d, Y');
        
        return "Most Popular Research ({$from} - {$to})";
    }
}
