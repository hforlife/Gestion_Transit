<?php

namespace App\Filament\Resources\DossierTransits\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DossierTransitsTable
{
    public static function configure(Table $table): Table
    {
        return $table

            // ✅ Optimisation requête
            ->modifyQueryUsing(
                fn(Builder $query) =>
                $query->withCount('documents')
            )

            ->columns([

                /* ===============================
                 |  INFORMATIONS PRINCIPALES
                 ===============================*/

                TextColumn::make('reference')
                    ->label('Référence')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                TextColumn::make('nom')
                    ->label('Nom du dossier')
                    ->searchable()
                    ->sortable()
                    ->limit(25)
                    ->tooltip(fn($record) => $record->nom),
                
                TextColumn::make('colis.client.nom')
                    ->label('Client')
                    ->searchable()
                    ->sortable()
                    ->limit(25)
                    ->tooltip(fn($record) => $record->nom),

                TextColumn::make('colis.numero_bl')
                    ->label('N° BL')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type_dossier.nom')
                    ->label('Type')
                    ->badge()
                    ->color('secondary')
                    ->sortable(),

                /* ===============================
                 |  DATE
                 ===============================*/

                TextColumn::make('date_depot')
                    ->label('Date dépôt')
                    ->date('d/m/Y')
                    ->sortable(),

                /* ===============================
                 |  STATUT
                 ===============================*/

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'OUVERT' => 'warning',
                        'EN_COURS' => 'info',
                        'CLOTURE' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),

                IconColumn::make('is_closed')
                    ->label('Clôturé')
                    ->boolean()
                    ->getStateUsing(
                        fn($record) =>
                        $record->status === 'CLOTURE'
                    )
                    ->color('success'),

                TextColumn::make('documents_count')
                    ->label('Nb Documents')
                    ->badge()
                    ->color(
                        fn($state) =>
                        $state > 0 ? 'primary' : 'secondary'
                    )
                    ->sortable(),

                /* ===============================
                 |  SYSTEME
                 ===============================*/

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Mis à jour')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->filters([

                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'OUVERT' => 'Ouvert',
                        'EN_COURS' => 'En cours',
                        'CLOTURE' => 'Clôturé',
                    ]),

                SelectFilter::make('type_dossier_id')
                    ->label('Type de dossier')
                    ->relationship('type_dossier', 'nom')
                    ->searchable(),
            ])

            ->defaultSort('created_at', 'desc')

            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(
                        fn($record) =>
                        $record->status !== 'CLOTURE'
                    ),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
