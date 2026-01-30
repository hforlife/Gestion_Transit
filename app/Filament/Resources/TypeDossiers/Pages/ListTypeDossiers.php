<?php

namespace App\Filament\Resources\TypeDossiers\Pages;

use App\Filament\Resources\TypeDossiers\TypeDossierResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTypeDossiers extends ListRecords
{
    protected static string $resource = TypeDossierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
