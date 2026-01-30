<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use BackedEnum;
use UnitEnum;
use Filament\Support\Icons\Heroicon;

class RapportClients extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::Users;
    protected static string | UnitEnum | null $navigationGroup = 'Rapports';
    protected static ?int $navigationSort = 2;
    
    protected string $view = 'filament.pages.rapport-clients';
}
