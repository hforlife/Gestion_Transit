<?php

namespace App\Filament\Resources\Colis\Pages;

use App\Filament\Resources\Colis\ColisResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListColis extends ListRecords
{
    protected static string $resource = ColisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
