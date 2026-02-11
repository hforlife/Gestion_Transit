<?php

namespace App\Filament\Widgets;

use App\Models\Client;
use App\Models\Colis;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClientStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total clients', Client::count())
                ->description('Clients enregistrés')
                ->icon('heroicon-o-users'),

            Stat::make('Clients actifs', Client::whereHas('colis')->count())
                ->description('Ayant au moins un colis')
                ->icon('heroicon-o-user-group')
                ->color('success'),

            Stat::make('Moyenne colis / client',
                round(Colis::count() / max(Client::count(), 1), 2)
            )
                ->description('Volume moyen par client')
                ->icon('heroicon-o-calculator')
                ->color('info'),
        ];
    }
}
