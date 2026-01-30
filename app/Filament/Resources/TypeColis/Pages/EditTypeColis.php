<?php

namespace App\Filament\Resources\TypeColis\Pages;

use App\Filament\Resources\TypeColis\TypeColisResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTypeColis extends EditRecord
{
    protected static string $resource = TypeColisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
