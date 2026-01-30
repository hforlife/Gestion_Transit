<?php

namespace App\Filament\Widgets;

use App\Models\Colis;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class RapportBlocage extends TableWidget
{
    protected static ?string $heading = 'Colis bloqués';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Colis::query()
                    ->whereIn('etat_colis', ['A_LA_DOUANE', 'AU_PORT'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('numero_bl')
                    ->label('N° BL')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('client.nom')
                    ->label('Client')
                    ->searchable(),

                Tables\Columns\TextColumn::make('etat_colis')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'A_LA_DOUANE' => 'danger',
                        'AU_PORT' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Dernière mise à jour')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc');
    }
}
