<?php

namespace App\Filament\Resources\DouaneOperations\Pages;

use App\Filament\Resources\DouaneOperations\DouaneOperationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDouaneOperations extends ListRecords
{
    protected static string $resource = DouaneOperationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
