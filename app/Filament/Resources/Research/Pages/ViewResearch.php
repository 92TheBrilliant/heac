<?php

namespace App\Filament\Resources\Research\Pages;

use App\Filament\Resources\Research\ResearchResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewResearch extends ViewRecord
{
    protected static string $resource = ResearchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
