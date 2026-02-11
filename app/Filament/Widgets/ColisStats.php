<?php

namespace App\Filament\Widgets;

use App\Models\Colis;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ColisStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
             Stat::make('Total colis', Colis::count())
                ->description('Tous les colis enregistrés')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->icon('heroicon-o-archive-box'),

            // Stat::make('Au port', Colis::where('etat_colis', 'AU_PORT')->count())
            //     ->description('Colis arrivés au port')
            //     ->icon('heroicon-o-building-office-2')
            //     ->chart([7, 2, 10, 3, 15, 4, 17])
            //     ->color('warning'),

            // Stat::make('À la douane', Colis::where('etat_colis', 'A_LA_DOUANE')->count())
            //     ->description('En cours de dédouanement')
            //     ->icon('heroicon-o-scale')
            //     ->chart([7, 2, 10, 3, 15, 4, 17])
            //     ->color('danger'),

            // Stat::make('En route', Colis::where('etat_colis', 'EN_ROUTE')->count())
            //     ->description('En livraison')
            //     ->icon('heroicon-o-truck')
            //     ->chart([7, 2, 10, 3, 15, 4, 17])
            //     ->color('info'),

            // Stat::make('Livrés', Colis::where('etat_colis', 'LIVRE')->count())
            //     ->description('Colis livrés')
            //     ->icon('heroicon-o-check-circle')
            //     ->chart([7, 2, 10, 3, 15, 4, 17])
            //     ->color('success'),
        ];
    }
}
