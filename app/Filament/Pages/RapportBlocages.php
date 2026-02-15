<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\BlocagesParMoisChart;
use App\Filament\Widgets\BlocageStats;
use Filament\Pages\Page;
use BackedEnum;
use UnitEnum;
use Filament\Support\Icons\Heroicon;

use App\Filament\Widgets\RapportBlocage;

class RapportBlocages extends Page
{
    // protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldExclamation;
    protected static string|UnitEnum|null $navigationGroup = 'Rapports';
    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.rapport-blocages';

    protected function getHeaderWidgets(): array
    {
        return [
                //
            BlocageStats::class,
            RapportBlocage::class,
            BlocagesParMoisChart::class,
        ];
    }
}
