<?php

namespace App\Filament\Resources\TypeColis\Pages;

use App\Filament\Resources\TypeColis\TypeColisResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTypeColis extends ListRecords
{
    protected static string $resource = TypeColisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
