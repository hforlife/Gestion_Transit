<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use BackedEnum;
use UnitEnum;
use Filament\Support\Icons\Heroicon;

class RapportColis extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;
    protected static string|UnitEnum|null $navigationGroup = 'Rapports';
    protected static ?int $navigationSort = 1;
    protected string $view = 'filament.pages.rapport-colis';

    protected function getHeaderWidgets(): array
    {
        return [
            //
            \App\Filament\Widgets\ColisGlobalStats::class,
            \App\Filament\Widgets\ColisParStatutTable::class,
            \App\Filament\Widgets\ColisEvolutionChart::class,
        ];
    }
}
