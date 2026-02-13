<?php

namespace App\Filament\Resources\Colis\Pages;

use App\Filament\Resources\Colis\ColisResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Enums\TabsPosition;
use Filament\Tables\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListColis extends ListRecords
{
    protected static string $resource = ColisResource::class;

public function getTabs(): array
    {
        return [
            'conteneurs' => Tab::make('Conteneurs')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->whereHas('typeColis', fn ($q) =>
                        $q->where('nom', 'Conteneur')
                    )
                ),

            'vehicules' => Tab::make('Véhicules')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->whereHas('typeColis', fn ($q) =>
                        $q->where('nom', 'Véhicules')
                    )
                ),
        ];
    }
}
