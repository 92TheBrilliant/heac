<?php

namespace App\Filament\Resources\Media\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

class MediaTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('path')
                    ->label('Preview')
                    ->disk('public')
                    ->height(60)
                    ->width(60)
                    ->defaultImageUrl(url('/images/file-icon.png'))
                    ->extraImgAttributes(['class' => 'object-cover']),
                
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->name),
                
                TextColumn::make('file_name')
                    ->label('File')
                    ->searchable()
                    ->limit(25)
                    ->copyable()
                    ->toggleable(),
                
                TextColumn::make('mime_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        str_starts_with($state, 'image/') => 'success',
                        str_starts_with($state, 'video/') => 'info',
                        str_starts_with($state, 'application/pdf') => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => strtoupper(explode('/', $state)[0] ?? 'FILE'))
                    ->toggleable(),
                
                TextColumn::make('size')
                    ->label('Size')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state / 1024, 2) . ' KB' : 'N/A')
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('folder.name')
                    ->label('Folder')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->placeholder('Root')
                    ->toggleable(),
                
                TextColumn::make('alt_text')
                    ->label('Alt Text')
                    ->limit(30)
                    ->placeholder('Not set')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('created_at')
                    ->label('Uploaded')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('mime_type')
                    ->label('File Type')
                    ->options([
                        'image/jpeg' => 'JPEG Image',
                        'image/png' => 'PNG Image',
                        'image/gif' => 'GIF Image',
                        'image/webp' => 'WebP Image',
                        'application/pdf' => 'PDF Document',
                        'video/mp4' => 'MP4 Video',
                    ])
                    ->multiple(),
                
                SelectFilter::make('folder_id')
                    ->label('Folder')
                    ->relationship('folder', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
            ])
            ->recordActions([
                Action::make('download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn ($record) => Storage::disk('public')->url($record->path))
                    ->openUrlInNewTab(),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('move_to_folder')
                        ->label('Move to Folder')
                        ->icon('heroicon-o-folder')
                        ->form([
                            \Filament\Forms\Components\Select::make('folder_id')
                                ->label('Select Folder')
                                ->relationship('folder', 'name')
                                ->searchable()
                                ->preload()
                                ->nullable(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $records->each(function ($record) use ($data) {
                                $record->update(['folder_id' => $data['folder_id']]);
                            });
                        })
                        ->deselectRecordsAfterCompletion(),
                    
                    DeleteBulkAction::make()
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                // Delete the actual file
                                if (Storage::disk('public')->exists($record->path)) {
                                    Storage::disk('public')->delete($record->path);
                                }
                                $record->delete();
                            });
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s'); // Auto-refresh every 30 seconds
    }
}
