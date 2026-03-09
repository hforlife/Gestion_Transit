<?php

namespace App\Filament\Resources\Colis\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class ColisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn(Builder $query) =>
                $query->with([
                    'typeColis',
                    'dossierTransit.client',
                    'port',
                    'agent',
                    'unites' // 👈 Chargement des unités
                ])
                    ->withCount('unites') // 👈 Comptage des unités
            )


            ->columns([

                /* ===============================
                 |  INFOS PRINCIPALES
                 ===============================*/

                TextColumn::make('numero_bl')
                    ->label('N° BL')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold')
                    ->description(fn($record) => $record->description)
                    ->limit(20),

                TextColumn::make('typeColis.nom')
                    ->label('Type')
                    ->badge()
                    ->color(function ($state) {
                        return match ($state) {
                            'Conteneur' => 'primary',
                            'Chassis' => 'warning',
                            'Chassis Voiture' => 'success',
                            'Chassis Machine' => 'danger',
                            default => 'gray',
                        };
                    })
                    ->sortable(),

                /* ===============================
                |  NOUVEAU : COMPTEUR D'UNITÉS
                ===============================*/
                TextColumn::make('unites_count')
                    ->label('Unités')
                    ->counts('unites')
                    ->badge()
                    ->color(fn($state) => $state > 0 ? 'primary' : 'gray')
                    ->formatStateUsing(fn($state) => $state . ' unité' . ($state > 1 ? 's' : ''))
                    ->sortable(),

                TextColumn::make('dossierTransit.client.nom')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('port.nom')
                    ->label('Port')
                    ->sortable()
                    ->toggleable(),

                /* ===============================
                 |  PORT
                 ===============================*/

                TextColumn::make('status_colis_port')
                    ->label('Etat Port')
                    ->sortable()
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'EN_ATTENTE' => 'secondary',
                        'PORT' => 'warning',
                        'SORTI' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('date_entree_port')
                    ->label('Arrivée')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),

                /* ===============================
                |  NOUVEAU : STATISTIQUES DES UNITÉS
                   ===============================*/

                TextColumn::make('unites_au_port')
                    ->label('Au port')
                    ->getStateUsing(
                        fn($record) =>
                        $record->unites->where('etat', 'AU_PORT')->count()
                    )
                    ->badge()
                    ->color('warning')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('unites_en_douane')
                    ->label('En douane')
                    ->getStateUsing(
                        fn($record) =>
                        $record->unites->where('etat', 'A_LA_DOUANE')->count()
                    )
                    ->badge()
                    ->color('info')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('unites_en_expertise')
                    ->label('Expertise')
                    ->getStateUsing(
                        fn($record) =>
                        $record->unites->where('etat', 'EXPERTISE')->count()
                    )
                    ->badge()
                    ->color('purple')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('unites_en_route')
                    ->label('En route')
                    ->getStateUsing(
                        fn($record) =>
                        $record->unites->where('etat', 'EN_ROUTE')->count()
                    )
                    ->badge()
                    ->color('primary')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('unites_livrees')
                    ->label('Livrés')
                    ->getStateUsing(
                        fn($record) =>
                        $record->unites->where('etat', 'LIVRE')->count()
                    )
                    ->badge()
                    ->color('success')
                    ->toggleable(isToggledHiddenByDefault: true),

                /* ===============================
                |  PROGRESSION
                ===============================*/
                IconColumn::make('progression')
                    ->label('Progression')
                    ->getStateUsing(function ($record) {
                        $total = $record->unites->count();

                        if ($total === 0) {
                            return 0;
                        }

                        $livrees = $record->unites->where('etat', 'LIVRE')->count();

                        return round(($livrees / $total) * 100);
                    })
                    ->icon(fn($state) => match (true) {
                        $state >= 100 => 'heroicon-o-check-circle',
                        $state >= 50 => 'heroicon-o-arrow-path',
                        default => 'heroicon-o-clock',
                    })
                    ->color(fn($state) => match (true) {
                        $state >= 100 => 'success',
                        $state >= 50 => 'warning',
                        default => 'gray',
                    })
                    ->toggleable(),

                /* ===============================
                 |  CLOTURE
                 ===============================*/

                TextColumn::make('status')
                    ->label('Clôture')
                    ->badge()
                    ->color(
                        fn($state) =>
                        $state === 'TERMINE' ? 'success' : 'warning'
                    ),

                IconColumn::make('is_late')
                    ->label('Retard')
                    ->boolean()
                    ->getStateUsing(
                        fn($record) =>
                        $record->status !== 'TERMINE'
                        && $record->created_at->diffInDays(now()) > 15
                    )
                    ->color('danger'),

                /* ===============================
                 |  SYSTEME
                 ===============================*/

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])

            ->filters([

                SelectFilter::make('id_type_colis')
                    ->label('Type')
                    ->relationship('typeColis', 'nom'),

                SelectFilter::make('client_id')
                    ->relationship('client', 'nom')
                    ->searchable(),

                SelectFilter::make('status')
                    ->options([
                        'EN_COURS' => 'En cours',
                        'TERMINE' => 'Terminé',
                    ]),

                Filter::make('expertise_en_cours')
                    ->label('Expertise en cours')
                    ->query(
                        fn(Builder $query) =>
                        $query->where('etat_pvc', 'PAYE')
                            ->where(function ($q) {
                                $q->where('etat_ae', 'NON_VALIDE')
                                    ->orWhere('etat_cmc', 'NON_RECU');
                            })
                    )
                    ->toggle(),

            ])

            ->defaultSort('created_at', 'desc')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(
                            fn() =>
                            auth()->user()?->hasRole('super_admin')
                        ),
                ]),
            ])

            ->poll('15s');
    }
}
