<?php

namespace App\Filament\Resources\Pages\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Translations')
                    ->tabs([
                        Tabs\Tab::make('English')
                            ->schema([
                                Section::make('Content')
                                    ->schema([
                                        TextInput::make('title.en')
                                            ->label('Title')
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn (string $operation, $state, callable $set) => 
                                                $operation === 'create' ? $set('slug', Str::slug($state)) : null
                                            ),
                                        
                                        RichEditor::make('content.en')
                                            ->label('Content')
                                            ->toolbarButtons([
                                                'bold',
                                                'italic',
                                                'underline',
                                                'strike',
                                                'link',
                                                'heading',
                                                'bulletList',
                                                'orderedList',
                                                'blockquote',
                                                'codeBlock',
                                                'undo',
                                                'redo',
                                            ])
                                            ->columnSpanFull(),
                                        
                                        Textarea::make('excerpt.en')
                                            ->label('Excerpt')
                                            ->rows(3)
                                            ->maxLength(500)
                                            ->helperText('Brief summary of the page content')
                                            ->columnSpanFull(),
                                        
                                        TextInput::make('meta_title.en')
                                            ->label('Meta Title')
                                            ->maxLength(60)
                                            ->helperText('Recommended: 50-60 characters'),
                                        
                                        Textarea::make('meta_description.en')
                                            ->label('Meta Description')
                                            ->rows(3)
                                            ->maxLength(160)
                                            ->helperText('Recommended: 150-160 characters')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),
                            ]),
                        
                        Tabs\Tab::make('Arabic')
                            ->schema([
                                Section::make('Content')
                                    ->schema([
                                        TextInput::make('title.ar')
                                            ->label('Title (العنوان)')
                                            ->maxLength(255),
                                        
                                        RichEditor::make('content.ar')
                                            ->label('Content (المحتوى)')
                                            ->toolbarButtons([
                                                'bold',
                                                'italic',
                                                'underline',
                                                'strike',
                                                'link',
                                                'heading',
                                                'bulletList',
                                                'orderedList',
                                                'blockquote',
                                                'codeBlock',
                                                'undo',
                                                'redo',
                                            ])
                                            ->columnSpanFull(),
                                        
                                        Textarea::make('excerpt.ar')
                                            ->label('Excerpt (المقتطف)')
                                            ->rows(3)
                                            ->maxLength(500)
                                            ->helperText('Brief summary of the page content')
                                            ->columnSpanFull(),
                                        
                                        TextInput::make('meta_title.ar')
                                            ->label('Meta Title (عنوان الميتا)')
                                            ->maxLength(60)
                                            ->helperText('Recommended: 50-60 characters'),
                                        
                                        Textarea::make('meta_description.ar')
                                            ->label('Meta Description (وصف الميتا)')
                                            ->rows(3)
                                            ->maxLength(160)
                                            ->helperText('Recommended: 150-160 characters')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),
                            ]),
                    ])
                    ->columnSpanFull(),
                
                Section::make('General Settings')
                    ->schema([
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Auto-generated from title, but can be customized'),
                    ])
                    ->columns(1),
                
                Section::make('SEO & Meta')
                    ->schema([
                        TextInput::make('meta_title')
                            ->maxLength(60)
                            ->helperText('Recommended: 50-60 characters'),
                        
                        Textarea::make('meta_description')
                            ->rows(3)
                            ->maxLength(160)
                            ->helperText('Recommended: 150-160 characters')
                            ->columnSpanFull(),
                        
                        FileUpload::make('og_image')
                            ->label('Open Graph Image')
                            ->image()
                            ->imageEditor()
                            ->directory('og-images')
                            ->helperText('Recommended size: 1200x630px'),
                    ])
                    ->columns(2)
                    ->collapsible(),
                
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
                        
                        DateTimePicker::make('published_at')
                            ->label('Publish Date')
                            ->native(false)
                            ->helperText('Leave empty to publish immediately'),
                        
                        Select::make('template')
                            ->options([
                                'default' => 'Default',
                                'full-width' => 'Full Width',
                                'sidebar' => 'With Sidebar',
                                'landing' => 'Landing Page',
                            ])
                            ->default('default')
                            ->required()
                            ->native(false),
                        
                        Select::make('parent_id')
                            ->label('Parent Page')
                            ->relationship('parent', 'title')
                            ->searchable()
                            ->preload()
                            ->helperText('Create a hierarchical page structure'),
                        
                        TextInput::make('order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->helperText('Used for sorting pages in navigation'),
                    ])
                    ->columns(2),
            ]);
    }
}
