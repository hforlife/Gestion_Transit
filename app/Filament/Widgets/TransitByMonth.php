<?php

namespace App\Filament\Widgets;

use App\Models\DossierTransit;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TransitByMonth extends ChartWidget
{
    protected ?string $heading = 'Transits effectués par mois';

    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $data = DossierTransit::query()
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('created_at', now()->year)
            ->groupBy('year', 'month')
            ->orderBy('month')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Nombre de transits',
                    'data' => $data->pluck('total')->toArray(),
                ],
            ],
            'labels' => $data->map(function ($item) {
                return now()
                    ->setMonth($item->month)
                    ->translatedFormat('F');
            })->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
