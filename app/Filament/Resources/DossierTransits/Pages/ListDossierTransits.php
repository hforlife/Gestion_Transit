<?php

namespace App\Filament\Resources\DossierTransits\Pages;

use App\Filament\Resources\DossierTransits\DossierTransitResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDossierTransits extends ListRecords
{
    protected static string $resource = DossierTransitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
