<?php

namespace App\Filament\Resources\Media\Schemas;

use App\Models\Media;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class MediaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('file_name'),
                TextEntry::make('mime_type')
                    ->placeholder('-'),
                TextEntry::make('path'),
                TextEntry::make('disk'),
                TextEntry::make('size')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('alt_text')
                    ->placeholder('-'),
                TextEntry::make('title')
                    ->placeholder('-'),
                TextEntry::make('caption')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('folder.name')
                    ->label('Folder')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Media $record): bool => $record->trashed()),
            ]);
    }
}
