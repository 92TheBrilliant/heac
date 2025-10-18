<?php

namespace App\Filament\Resources\Research\Schemas;

use App\Models\Research;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ResearchInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title'),
                TextEntry::make('slug'),
                TextEntry::make('abstract')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('authors')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('publication_date')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('file_path')
                    ->placeholder('-'),
                TextEntry::make('file_type')
                    ->placeholder('-'),
                TextEntry::make('file_size')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('thumbnail')
                    ->placeholder('-'),
                TextEntry::make('views_count')
                    ->numeric(),
                TextEntry::make('downloads_count')
                    ->numeric(),
                TextEntry::make('status')
                    ->badge(),
                IconEntry::make('featured')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Research $record): bool => $record->trashed()),
            ]);
    }
}
