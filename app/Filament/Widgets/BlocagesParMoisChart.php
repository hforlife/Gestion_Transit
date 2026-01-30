<?php

namespace App\Filament\Widgets;

use App\Models\Colis;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class BlocagesParMoisChart extends ChartWidget
{
    protected ?string $heading = 'Évolution mensuelle des blocages';

    protected function getData(): array
    {
        $data = Colis::selectRaw("
                DATE_FORMAT(created_at, '%Y-%m') as mois,
                COUNT(*) as total
            ")
            ->whereIn('etat_colis', ['A_LA_DOUANE', 'AU_PORT'])
            ->groupBy('mois')
            ->orderBy('mois')
            ->pluck('total', 'mois');

        return [
            'datasets' => [
                [
                    'label' => 'Colis bloqués',
                    'data' => $data->values(),
                ],
            ],
            'labels' => $data->keys(),
        ];
    }

    protected function getType(): string
    {
        return 'line'; // graphique en courbe
    }
}
