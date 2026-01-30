<?php

namespace App\Filament\Resources\PortOperations\Pages;

use App\Filament\Resources\PortOperations\PortOperationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPortOperation extends EditRecord
{
    protected static string $resource = PortOperationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
