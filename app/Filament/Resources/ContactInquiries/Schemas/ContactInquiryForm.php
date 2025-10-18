<?php

namespace App\Filament\Resources\ContactInquiries\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ContactInquiryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Inquiry Details')
                    ->description('Contact information submitted by the visitor')
                    ->schema([
                        TextInput::make('name')
                            ->label('Full Name')
                            ->disabled()
                            ->dehydrated(false),
                        
                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->disabled()
                            ->dehydrated(false)
                            ->copyable(),
                        
                        TextInput::make('phone')
                            ->label('Phone Number')
                            ->tel()
                            ->disabled()
                            ->dehydrated(false)
                            ->copyable()
                            ->placeholder('Not provided'),
                        
                        TextInput::make('subject')
                            ->label('Subject')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('No subject'),
                        
                        Textarea::make('message')
                            ->label('Message')
                            ->rows(6)
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),
                        
                        Placeholder::make('created_at')
                            ->label('Submitted At')
                            ->content(fn ($record) => $record?->created_at?->format('F d, Y \a\t h:i A') ?? 'N/A'),
                    ])
                    ->columns(2)
                    ->collapsible(),
                
                Section::make('Status Management')
                    ->description('Update the inquiry status and track responses')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'new' => 'New',
                                'in_progress' => 'In Progress',
                                'resolved' => 'Resolved',
                                'closed' => 'Closed',
                            ])
                            ->default('new')
                            ->required()
                            ->native(false)
                            ->live(),
                        
                        DateTimePicker::make('responded_at')
                            ->label('Response Date')
                            ->native(false)
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Not responded yet'),
                        
                        Select::make('responded_by')
                            ->label('Responded By')
                            ->relationship('responder', 'name')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Not responded yet'),
                    ])
                    ->columns(3),
                
                Section::make('Technical Information')
                    ->description('Metadata captured during submission')
                    ->schema([
                        TextInput::make('ip_address')
                            ->label('IP Address')
                            ->disabled()
                            ->dehydrated(false)
                            ->copyable()
                            ->placeholder('Not captured'),
                        
                        Textarea::make('user_agent')
                            ->label('User Agent')
                            ->rows(2)
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Not captured')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }
}
