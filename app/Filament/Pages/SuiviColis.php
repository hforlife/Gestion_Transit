<?php

namespace App\Filament\Pages;

use App\Models\Colis;
use Filament\Pages\Page;
use BackedEnum;
use UnitEnum;
use Filament\Support\Icons\Heroicon;

class SuiviColis extends Page
{
    protected static ?string $navigationLabel = 'Suivi d’un colis';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::ChartBar;
    protected static string|UnitEnum|null $navigationGroup = 'Rapports';
    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.pages.suivi-colis';

    public ?Colis $colis = null;

    public function mount(): void
    {
        // Par défaut on prend le dernier colis (à adapter plus tard avec un select/recherche)
        $this->colis = Colis::latest()->first();
    }

    public function getSteps(): array
    {
        return [
            'BL_ENREGISTRE',
            'AU_PORT',
            'A_LA_DOUANE',
            'EN_ROUTE',
            'LIVRE',
        ];
    }

    public function getCurrentStepIndex(): int
    {
        if (! $this->colis) {
            return 0;
        }

        return array_search($this->colis->etat_colis, $this->getSteps());
    }
}
