<?php

namespace App\Filament\Resources\Research\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ResearchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Translations')
                    ->tabs([
                        Tabs\Tab::make('English')
                            ->schema([
                                Section::make('Research Information')
                                    ->schema([
                                        TextInput::make('title.en')
                                            ->label('Title')
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn (string $operation, $state, callable $set) => 
                                                $operation === 'create' ? $set('slug', Str::slug($state)) : null
                                            )
                                            ->columnSpanFull(),
                                        
                                        Textarea::make('abstract.en')
                                            ->label('Abstract')
                                            ->required()
                                            ->rows(5)
                                            ->maxLength(2000)
                                            ->helperText('Brief summary of the research')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                        
                        Tabs\Tab::make('Arabic')
                            ->schema([
                                Section::make('Research Information')
                                    ->schema([
                                        TextInput::make('title.ar')
                                            ->label('Title (العنوان)')
                                            ->maxLength(255)
                                            ->columnSpanFull(),
                                        
                                        Textarea::make('abstract.ar')
                                            ->label('Abstract (الملخص)')
                                            ->rows(5)
                                            ->maxLength(2000)
                                            ->helperText('Brief summary of the research')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
                
                Section::make('General Information')
                    ->schema([
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Auto-generated from title, but can be customized'),
                        
                        DatePicker::make('publication_date')
                            ->label('Publication Date')
                            ->native(false)
                            ->displayFormat('M d, Y')
                            ->required(),
                        
                        Repeater::make('authors')
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('affiliation')
                                    ->maxLength(255),
                                TextInput::make('email')
                                    ->email()
                                    ->maxLength(255),
                            ])
                            ->columns(3)
                            ->defaultItems(1)
                            ->addActionLabel('Add Author')
                            ->columnSpanFull()
                            ->collapsible(),
                    ])
                    ->columns(2),
                
                Section::make('File Upload')
                    ->schema([
                        FileUpload::make('file_path')
                            ->label('Research Document')
                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                            ->maxSize(51200) // 50MB
                            ->directory('research-files')
                            ->downloadable()
                            ->openable()
                            ->helperText('Accepted formats: PDF, DOC, DOCX (Max: 50MB)')
                            ->required()
                            ->columnSpanFull(),
                        
                        FileUpload::make('thumbnail')
                            ->label('Thumbnail Image')
                            ->image()
                            ->imageEditor()
                            ->directory('research-thumbnails')
                            ->helperText('Optional thumbnail for the research')
                            ->columnSpanFull(),
                    ]),
                
                Section::make('Categorization')
                    ->schema([
                        Select::make('categories')
                            ->relationship('categories', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255),
                                Textarea::make('description')
                                    ->rows(3),
                            ])
                            ->helperText('Select or create categories'),
                        
                        Select::make('tags')
                            ->relationship('tags', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->helperText('Select or create tags'),
                    ])
                    ->columns(2),
                
                Section::make('Settings')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Published',
                                'archived' => 'Archived',
                            ])
                            ->default('draft')
                            ->required()
                            ->native(false),
                        
                        Toggle::make('featured')
                            ->label('Featured Research')
                            ->helperText('Display this research prominently on the homepage')
                            ->default(false),
                        
                        TextInput::make('views_count')
                            ->label('Views Count')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Automatically tracked'),
                        
                        TextInput::make('downloads_count')
                            ->label('Downloads Count')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Automatically tracked'),
                    ])
                    ->columns(2),
            ]);
    }
}
