<?php

namespace App\Filament\Widgets;

use App\Models\Colis;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ColisBloques extends ChartWidget
{
    protected ?string $heading = 'Évolution des colis';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = Colis::query()
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total')
            )
            ->where('created_at', '>=', now()->subDays(14))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Colis créés',
                    'data' => $data->pluck('total'),
                ],
            ],
            'labels' => $data->pluck('date')
                ->map(fn ($date) => Carbon::parse($date)->format('d/m'))
                ->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
