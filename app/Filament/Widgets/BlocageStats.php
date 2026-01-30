<?php

namespace App\Filament\Widgets;

use App\Models\Colis;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BlocageStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make(
                'Bloqués à la douane',
                Colis::where('etat_colis', 'A_LA_DOUANE')->count()
            )
                ->description('En attente de dédouanement')
                ->icon('heroicon-o-scale')
                ->color('danger'),

            Stat::make(
                'Bloqués au port',
                Colis::where('etat_colis', 'AU_PORT')->count()
            )
                ->description('Arrivés mais non sortis du port')
                ->icon('heroicon-o-building-office-2')
                ->color('warning'),

            Stat::make(
                'Total blocages',
                Colis::whereIn('etat_colis', ['A_LA_DOUANE', 'AU_PORT'])->count()
            )
                ->description('Tous les colis en attente')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('primary'),
        ];
    }
}
