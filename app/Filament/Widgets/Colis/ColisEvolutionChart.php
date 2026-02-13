<?php

namespace App\Filament\Widgets\Colis;

use App\Models\Colis;
use Filament\Widgets\ChartWidget;

class ColisEvolutionChart extends ChartWidget
{
    protected ?string $heading = 'Évolution mensuelle des colis';

    protected function getData(): array
    {
        $data = Colis::selectRaw("
                DATE_FORMAT(created_at, '%Y-%m') as mois,
                COUNT(*) as total
            ")
            ->groupBy('mois')
            ->orderBy('mois')
            ->pluck('total', 'mois');

        return [
            'datasets' => [
                [
                    'label' => 'Colis créés',
                    'data' => $data->values(),
                ],
            ],
            'labels' => $data->keys(),
        ];
    }

    protected function getType(): string
    {
        return 'line'; // courbe d’évolution
    }
}
