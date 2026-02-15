<?php

namespace App\Filament\Resources\Colis\Pages;

use App\Filament\Resources\Colis\ColisResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListColis extends ListRecords
{
    protected static string $resource = ColisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return ColisResource::getWidgets();
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tous'),

            'conteneur' => Tab::make('Conteneurs')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->whereHas('typeColis', fn ($q) =>
                        $q->where('nom', 'Conteneur')
                    )
                ),

            'vehicule' => Tab::make('Véhicules')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->whereHas('typeColis', fn ($q) =>
                        $q->where('nom', 'Véhicules')
                    )
                ),
        ];
    }
}
