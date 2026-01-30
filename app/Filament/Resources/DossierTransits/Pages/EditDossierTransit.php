<?php

namespace App\Filament\Resources\DossierTransits\Pages;

use App\Filament\Resources\DossierTransits\DossierTransitResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDossierTransit extends EditRecord
{
    protected static string $resource = DossierTransitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
