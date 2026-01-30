<?php

namespace App\Filament\Resources\TypeDossiers\Pages;

use App\Filament\Resources\TypeDossiers\TypeDossierResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTypeDossier extends EditRecord
{
    protected static string $resource = TypeDossierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
