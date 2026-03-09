<?php

namespace App\Filament\Resources\ColisUnites\Pages;

use App\Filament\Resources\ColisUnites\ColisUniteResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditColisUnite extends EditRecord
{
    protected static string $resource = ColisUniteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
