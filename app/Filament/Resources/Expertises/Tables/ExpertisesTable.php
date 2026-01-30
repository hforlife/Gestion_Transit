<?php

namespace App\Filament\Resources\Expertises\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Table;

class ExpertisesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // ─────────────────────────────
                // Références documents
                // ─────────────────────────────
                TextColumn::make('num_pvc')
                    ->label('N° PVC')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('num_ae')
                    ->label('N° AE')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('num_cmc')
                    ->label('N° CMC')
                    ->searchable()
                    ->copyable(),

                // ─────────────────────────────
                // États métier modifiables
                // ─────────────────────────────
                SelectColumn::make('etat_expertise')
                    ->label('Expertise')
                    ->options([
                        'EN_ATTENTE' => 'En attente', 
                        'EFFECTUEE' => 'Effectuée'
                    ])
                    ->selectablePlaceholder(false)
                    ->disabled(fn($record) => $record->status === 'TERMINE')
                    ->afterStateUpdated(function ($record, $state) {
                        // Règle métier simple
                        if ($state === 'EFFECTUEE') {
                            $record->updateQuietly([
                                'status' => 'EN_COURS',
                            ]);
                        }
                    }),

                SelectColumn::make('etat_pvc')
                    ->label('PVC')
                    ->options([
                        'NON_RECU' => 'Non réçu', 
                        'RECU' => 'Réçu', 
                        'PAYE' => 'Payé'
                    ])
                    ->disabled(fn($record) => $record->status === 'TERMINE'),

                SelectColumn::make('etat_ae')
                    ->label('AE')
                    ->options([
                        'NON_VALIDE' => 'Non validé', 
                        'VALIDE' => 'Validé'
                    ])
                    ->disabled(fn($record) => $record->status === 'TERMINE'),

                SelectColumn::make('etat_cmc')
                    ->label('CMC')
                    ->options([
                        'NON_RECU' => 'Non réçu', 
                        'RECU' => 'Réçu'
                    ])
                    ->disabled(fn($record) => $record->status === 'TERMINE'),

                SelectColumn::make('status')
                    ->label('Statut dossier')
                    ->options([
                        'EN_COURS' => 'En cours',
                        'TERMINE' => 'Terminé',
                    ])
                    ->selectablePlaceholder(false),

                // ─────────────────────────────
                // Relations & métadonnées
                // ─────────────────────────────
                TextColumn::make('expert_id')
                    ->label('Expert')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('colis_id')
                    ->label('Colis')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Mis à jour')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // futurs filtres métier :
                // - par statut
                // - par expert
                // - par période
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn($record) => $record->status !== 'TERMINE'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
