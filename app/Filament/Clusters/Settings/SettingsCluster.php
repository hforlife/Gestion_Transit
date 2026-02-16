<?php

namespace App\Filament\Clusters\Settings;

use BackedEnum;
use Filament\Clusters\Cluster;
// use BackedEnum;
use UnitEnum;
use Filament\Support\Icons\Heroicon;

class SettingsCluster extends Cluster
{
    // protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static string | UnitEnum | null $navigationGroup = 'Paramètres';

    // protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?int $navigationSort = 9;

    public static function getNavigationLabel(): string
    {
        return 'Paramètres';
    }
}
