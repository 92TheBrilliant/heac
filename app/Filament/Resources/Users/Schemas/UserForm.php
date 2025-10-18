<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('User Information')
                    ->schema([
                        TextInput::make('name')
                            ->label('Full Name')
                            ->required()
                            ->maxLength(255),
                        
                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        
                        TextInput::make('phone')
                            ->label('Phone Number')
                            ->tel()
                            ->maxLength(50),
                        
                        FileUpload::make('avatar')
                            ->label('Profile Picture')
                            ->image()
                            ->imageEditor()
                            ->directory('avatars')
                            ->avatar()
                            ->circleCropper(),
                        
                        Textarea::make('bio')
                            ->label('Biography')
                            ->rows(4)
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                
                Section::make('Authentication')
                    ->schema([
                        TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->maxLength(255)
                            ->revealable()
                            ->helperText('Leave blank to keep current password')
                            ->suffixAction(
                                Action::make('generate')
                                    ->icon('heroicon-o-sparkles')
                                    ->action(function ($set) {
                                        $password = Str::password(12);
                                        $set('password', $password);
                                        // In a real app, you'd want to show this to the admin
                                        // or send it via email
                                    })
                            ),
                        
                        TextInput::make('password_confirmation')
                            ->password()
                            ->dehydrated(false)
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->maxLength(255)
                            ->revealable()
                            ->same('password')
                            ->visible(fn ($get) => filled($get('password'))),
                        
                        DateTimePicker::make('email_verified_at')
                            ->label('Email Verified At')
                            ->native(false)
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn ($operation) => $operation === 'edit'),
                        
                        DateTimePicker::make('last_login_at')
                            ->label('Last Login')
                            ->native(false)
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn ($operation) => $operation === 'edit'),
                    ])
                    ->columns(2),
                
                Section::make('Roles & Permissions')
                    ->schema([
                        Select::make('roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->helperText('Assign roles to control user permissions')
                            ->columnSpanFull(),
                        
                        Select::make('permissions')
                            ->relationship('permissions', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->helperText('Optional: Assign specific permissions beyond role permissions')
                            ->columnSpanFull(),
                    ]),
                
                Section::make('Status')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'suspended' => 'Suspended',
                            ])
                            ->default('active')
                            ->required()
                            ->native(false)
                            ->helperText('Control user access to the system'),
                    ]),
            ]);
    }
}
