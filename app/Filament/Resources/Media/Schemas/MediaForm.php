<?php

namespace App\Filament\Resources\Media\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Schema;

class MediaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('File Upload')
                    ->schema([
                        FileUpload::make('path')
                            ->label('Upload File')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                null,
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->directory('media')
                            ->visibility('public')
                            ->downloadable()
                            ->openable()
                            ->previewable()
                            ->maxSize(10240) // 10MB
                            ->helperText('Upload images or documents (Max: 10MB)')
                            ->required()
                            ->columnSpanFull()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if ($state) {
                                    // Auto-populate name from filename if not set
                                    if (!$get('name')) {
                                        $set('name', pathinfo($state, PATHINFO_FILENAME));
                                    }
                                }
                            }),
                    ])
                    ->visible(fn ($operation) => $operation === 'create'),
                
                Section::make('File Information')
                    ->schema([
                        TextInput::make('name')
                            ->label('Display Name')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Friendly name for this media file'),
                        
                        TextInput::make('file_name')
                            ->label('File Name')
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn ($operation) => $operation === 'edit'),
                        
                        TextInput::make('mime_type')
                            ->label('MIME Type')
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn ($operation) => $operation === 'edit'),
                        
                        TextInput::make('size')
                            ->label('File Size')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($state) => $state ? number_format($state / 1024, 2) . ' KB' : 'N/A')
                            ->visible(fn ($operation) => $operation === 'edit'),
                    ])
                    ->columns(2),
                
                Section::make('Metadata')
                    ->schema([
                        TextInput::make('title')
                            ->label('Title')
                            ->maxLength(255)
                            ->helperText('Optional title for SEO'),
                        
                        TextInput::make('alt_text')
                            ->label('Alt Text')
                            ->maxLength(255)
                            ->helperText('Alternative text for accessibility (important for images)'),
                        
                        Textarea::make('caption')
                            ->label('Caption')
                            ->rows(3)
                            ->maxLength(500)
                            ->helperText('Optional caption or description')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                
                Section::make('Organization')
                    ->schema([
                        Select::make('folder_id')
                            ->label('Folder')
                            ->relationship('folder', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255),
                                Textarea::make('description')
                                    ->rows(3),
                                Select::make('parent_id')
                                    ->label('Parent Folder')
                                    ->relationship('parent', 'name')
                                    ->searchable()
                                    ->preload(),
                            ])
                            ->helperText('Organize media into folders'),
                        
                        TextInput::make('disk')
                            ->default('public')
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn ($operation) => $operation === 'edit'),
                    ])
                    ->columns(2),
            ]);
    }
}
