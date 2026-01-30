<?php

namespace App\Filament\Resources\DouaneOperations\Pages;

use App\Filament\Resources\DouaneOperations\DouaneOperationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDouaneOperation extends EditRecord
{
    protected static string $resource = DouaneOperationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
