<?php

namespace App\Filament\Resources\LivraisonOperations\Pages;

use App\Filament\Resources\LivraisonOperations\LivraisonOperationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLivraisonOperation extends EditRecord
{
    protected static string $resource = LivraisonOperationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
