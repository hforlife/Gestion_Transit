<?php

namespace App\Filament\Resources\PortOperations\Pages;

use App\Filament\Resources\PortOperations\PortOperationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPortOperations extends ListRecords
{
    protected static string $resource = PortOperationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
