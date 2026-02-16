<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ColisBloques;
use App\Filament\Widgets\ColisStats;
use App\Filament\Widgets\TransitByMonth;
use App\Filament\Widgets\LastTrackingEvents;
use Filament\Pages\Page;
use BackedEnum;
use UnitEnum;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Section;

class Dashboard extends BaseDashboard
{
    protected string $view = 'filament.pages.dashboard';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    use HasFiltersForm;

    protected function getHeaderWidgets(): array
    {
        return [
            ColisStats::class,
            ColisBloques::class,
            // TransitByMonth::class,
            // LastTrackingEvents::class,
        ];
    }
}
