<?php

namespace App\Filament\Widgets;

use App\Models\Colis;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ColisGlobalStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total colis', Colis::count())
                ->description('Tous les colis enregistrés')
                ->icon('heroicon-o-archive-box'),

            Stat::make(
                'En cours',
                Colis::whereNotIn('etat_colis', ['LIVRE'])->count()
            )
                ->description('Non encore livrés')
                ->icon('heroicon-o-clock')
                ->color('warning'),

            Stat::make(
                'Livrés',
                Colis::where('etat_colis', 'LIVRE')->count()
            )
                ->description('Colis terminés')
                ->icon('heroicon-o-check-circle')
                ->color('success'),
        ];
    }
}
 