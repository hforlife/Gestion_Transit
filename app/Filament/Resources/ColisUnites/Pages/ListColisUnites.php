<?php

namespace App\Filament\Resources\ColisUnites\Pages;

use App\Filament\Resources\ColisUnites\ColisUniteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListColisUnites extends ListRecords
{
    protected static string $resource = ColisUniteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
