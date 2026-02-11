<?php

namespace App\Filament\Widgets;

use App\Models\Colis;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Collection;

class ColisParStatutTable extends TableWidget
{
    // protected ?string $heading = 'Répartition des colis par statut';
    // protected static ?int $sort = 2;

    // protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                // Récupérer les données d'abord
                $data = Colis::query()
                    ->select('etat_colis')
                    ->selectRaw('COUNT(*) as total')
                    ->groupBy('etat_colis')
                    ->orderByDesc('total')
                    ->get();

                // Retourner une requête vide mais avec les données
                return Colis::whereIn('id', [])->withCasts(['etat_colis' => 'string']);
            })
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('etat_colis')
                    ->label('Statut')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'BL_ENREGISTRE' => 'BL enregistré',
                        'AU_PORT' => 'Arrivé au port',
                        'A_LA_DOUANE' => 'À la douane',
                        'EN_ROUTE' => 'En route',
                        'LIVRE' => 'Livré',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'BL_ENREGISTRE' => 'gray',
                        'AU_PORT' => 'warning',
                        'A_LA_DOUANE' => 'danger',
                        'EN_ROUTE' => 'info',
                        'LIVRE' => 'success',
                        default => 'secondary',
                    }),

                Tables\Columns\TextColumn::make('total')
                    ->label('Nombre de colis')
                    ->numeric()
                    ->badge()
                    ->color('primary')
                    ->alignCenter(),
            ]);
    }

    // Méthode pour récupérer les données
    // protected function getTableRecords(): Collection
    // {
    //     return Colis::query()
    //         ->select('etat_colis')
    //         ->selectRaw('COUNT(*) as total')
    //         ->groupBy('etat_colis')
    //         ->orderByDesc('total')
    //         ->get()
    //         ->map(function ($item) {
    //             return (object) [
    //                 'etat_colis' => $item->etat_colis,
    //                 'total' => $item->total,
    //             ];
    //         });
    // }
}
