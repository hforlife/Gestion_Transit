<?php

namespace App\Filament\Resources\Colis\Pages;

use App\Filament\Resources\Colis\ColisResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListConteneurs extends ListRecords
{
    protected static string $resource = ColisResource::class;

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->whereHas('typeColis', fn ($q) =>
                $q->where('nom', 'Conteneur')
            );
    }

    protected static ?string $navigationLabel = 'Conteneurs';
}
