<?php

namespace App\Filament\Resources\ContactInquiries\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ContactInquiriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->limit(35),
                
                TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->copyable()
                    ->placeholder('N/A')
                    ->toggleable(),
                
                TextColumn::make('subject')
                    ->label('Subject')
                    ->searchable()
                    ->limit(40)
                    ->placeholder('No subject')
                    ->wrap(),
                
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'danger',
                        'in_progress' => 'warning',
                        'resolved' => 'success',
                        'closed' => 'gray',
                    })
                    ->sortable(),
                
                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->since()
                    ->tooltip(fn ($record) => $record->created_at->format('F d, Y \a\t h:i A')),
                
                TextColumn::make('responded_at')
                    ->label('Responded')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->placeholder('Not responded')
                    ->toggleable(),
                
                TextColumn::make('responder.name')
                    ->label('Responded By')
                    ->placeholder('N/A')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('ip_address')
                    ->label('IP')
                    ->searchable()
                    ->copyable()
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
                        'new' => 'New',
                        'in_progress' => 'In Progress',
                        'resolved' => 'Resolved',
                        'closed' => 'Closed',
                    ])
                    ->multiple()
                    ->default(['new', 'in_progress']),
                
                Filter::make('responded')
                    ->label('Responded')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('responded_at'))
                    ->toggle(),
                
                Filter::make('unresponded')
                    ->label('Not Responded')
                    ->query(fn (Builder $query): Builder => $query->whereNull('responded_at'))
                    ->toggle(),
                
                Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from')
                            ->label('From Date'),
                        \Filament\Forms\Components\DatePicker::make('until')
                            ->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                Action::make('mark_in_progress')
                    ->label('In Progress')
                    ->icon('heroicon-o-clock')
                    ->color('warning')
                    ->visible(fn ($record) => $record->status === 'new')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['status' => 'in_progress']);
                    }),
                
                Action::make('mark_resolved')
                    ->label('Resolve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => in_array($record->status, ['new', 'in_progress']))
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'resolved',
                            'responded_at' => now(),
                            'responded_by' => auth()->id(),
                        ]);
                    }),
                
                Action::make('email_reply')
                    ->label('Reply')
                    ->icon('heroicon-o-envelope')
                    ->color('info')
                    ->url(fn ($record) => 'mailto:' . $record->email . '?subject=Re: ' . urlencode($record->subject ?? 'Your Inquiry'))
                    ->openUrlInNewTab(),
                
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('mark_in_progress')
                        ->label('Mark as In Progress')
                        ->icon('heroicon-o-clock')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update(['status' => 'in_progress']);
                            });
                        })
                        ->deselectRecordsAfterCompletion(),
                    
                    BulkAction::make('mark_resolved')
                        ->label('Mark as Resolved')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update([
                                    'status' => 'resolved',
                                    'responded_at' => now(),
                                    'responded_by' => auth()->id(),
                                ]);
                            });
                        })
                        ->deselectRecordsAfterCompletion(),
                    
                    BulkAction::make('mark_closed')
                        ->label('Mark as Closed')
                        ->icon('heroicon-o-x-circle')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update(['status' => 'closed']);
                            });
                        })
                        ->deselectRecordsAfterCompletion(),
                    
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('60s'); // Auto-refresh every 60 seconds
    }
}
