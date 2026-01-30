<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use BackedEnum;
use UnitEnum;
use Filament\Support\Icons\Heroicon;

class RapportColis extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::ChartBar;
    protected static string | UnitEnum | null $navigationGroup = 'Rapports';
    protected static ?int $navigationSort = 1;
    protected string $view = 'filament.pages.rapport-colis';

    public function getWidgetData(): array
{
    return [
        'stats' => [
            'total' => 100,
        ],
    ];
}
}
