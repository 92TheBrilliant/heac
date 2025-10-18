<?php

namespace App\Filament\Resources\Pages\Tables;

use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class PagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                
                TextColumn::make('slug')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->copyable()
                    ->toggleable(),
                
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'published' => 'success',
                        'archived' => 'warning',
                    })
                    ->sortable(),
                
                TextColumn::make('template')
                    ->badge()
                    ->toggleable(),
                
                TextColumn::make('published_at')
                    ->label('Published')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->placeholder('Not published'),
                
                TextColumn::make('parent.title')
                    ->label('Parent')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('None'),
                
                TextColumn::make('order')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'archived' => 'Archived',
                    ])
                    ->multiple(),
                
                SelectFilter::make('template')
                    ->options([
                        'default' => 'Default',
                        'full-width' => 'Full Width',
                        'sidebar' => 'With Sidebar',
                        'landing' => 'Landing Page',
                    ])
                    ->multiple(),
                
                Filter::make('published')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('published_at'))
                    ->toggle(),
                
                Filter::make('has_parent')
                    ->label('Has Parent')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('parent_id'))
                    ->toggle(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('publish')
                        ->label('Publish Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update([
                                    'status' => 'published',
                                    'published_at' => $record->published_at ?? now(),
                                ]);
                            });
                        })
                        ->deselectRecordsAfterCompletion(),
                    
                    BulkAction::make('unpublish')
                        ->label('Unpublish Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update(['status' => 'draft']);
                            });
                        })
                        ->deselectRecordsAfterCompletion(),
                    
                    BulkAction::make('archive')
                        ->label('Archive Selected')
                        ->icon('heroicon-o-archive-box')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update(['status' => 'archived']);
                            });
                        })
                        ->deselectRecordsAfterCompletion(),
                    
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
