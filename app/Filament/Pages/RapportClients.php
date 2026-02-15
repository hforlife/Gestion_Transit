<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ClientsParColisTable;
use App\Filament\Widgets\ClientStats;
use App\Filament\Widgets\ColisParClientChart;
use Filament\Pages\Page;
use BackedEnum;
use UnitEnum;
use Filament\Support\Icons\Heroicon;

class RapportClients extends Page
{
//     protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string | UnitEnum | null $navigationGroup = 'Rapports';
    
    protected static ?int $navigationSort = 2;
    
    protected string $view = 'filament.pages.rapport-clients';

    protected function getHeaderWidgets(): array
    {
        return [
                //
            ClientStats::class,
            ClientsParColisTable::class,
            ColisParClientChart::class,
        ];
    }
}
