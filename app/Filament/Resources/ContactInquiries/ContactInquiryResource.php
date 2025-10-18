<?php

namespace App\Filament\Resources\ContactInquiries;

use App\Filament\Resources\ContactInquiries\Pages\CreateContactInquiry;
use App\Filament\Resources\ContactInquiries\Pages\EditContactInquiry;
use App\Filament\Resources\ContactInquiries\Pages\ListContactInquiries;
use App\Filament\Resources\ContactInquiries\Pages\ViewContactInquiry;
use App\Filament\Resources\ContactInquiries\Schemas\ContactInquiryForm;
use App\Filament\Resources\ContactInquiries\Schemas\ContactInquiryInfolist;
use App\Filament\Resources\ContactInquiries\Tables\ContactInquiriesTable;
use App\Models\ContactInquiry;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ContactInquiryResource extends Resource
{
    protected static ?string $model = ContactInquiry::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';
    
    protected static string|UnitEnum|null $navigationGroup = 'Communications';
    
    protected static ?int $navigationSort = 1;
    
    protected static ?string $navigationLabel = 'Contact Inquiries';
    
    public static function canViewAny(): bool
    {
        return auth()->user()?->can('view_contact_inquiries') ?? false;
    }
    
    public static function canCreate(): bool
    {
        return false; // Inquiries are created from the public form only
    }

    public static function form(Schema $schema): Schema
    {
        return ContactInquiryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ContactInquiryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContactInquiriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContactInquiries::route('/'),
            'view' => ViewContactInquiry::route('/{record}'),
            'edit' => EditContactInquiry::route('/{record}/edit'),
        ];
    }
}
