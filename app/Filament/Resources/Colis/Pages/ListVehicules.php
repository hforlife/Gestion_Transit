<?php

namespace App\Filament\Resources\Colis\Pages;

use App\Filament\Resources\Colis\ColisResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListVehicules extends ListRecords
{
    protected static string $resource = ColisResource::class;

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->whereHas('typeColis', fn ($q) =>
                $q->where('nom', 'Véhicules')
            );
    }

    protected static ?string $navigationLabel = 'Véhicules';
}
