<?php

namespace App\Filament\Resources\LivraisonOperations\Pages;

use App\Filament\Resources\LivraisonOperations\LivraisonOperationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLivraisonOperations extends ListRecords
{
    protected static string $resource = LivraisonOperationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
