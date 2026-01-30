<?php

namespace App\Filament\Resources\DossierTransits\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DossierTransitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('reference')
                    ->label('Référence')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nom')
                    ->label('Nom du dossier')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('colis.numero_bl')
                    ->label('N° BL')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type_dossier.nom')
                    ->label('Type de dossier')
                    ->sortable(),

                TextColumn::make('date_depot')
                    ->label('Date de dépôt')
                    ->date()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->colors([
                        'warning' => 'OUVERT',
                        'info' => 'EN_COURS',
                        'success' => 'CLOTURE',
                    ])
                    ->sortable(),

                TextColumn::make('repertoire')
                    ->label('Répertoire')
                    ->formatStateUsing(fn($state) => $state ? '📄 Voir le PDF' : '—')
                    ->url(
                        fn($record) =>
                        $record->document_path
                        ? asset('storage/' . $record->document_path)
                        : null
                    )
                    ->openUrlInNewTab()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Mis à jour le')
                    ->dateTime()
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

                SelectFilter::make('type_dossier')
                    ->label('Type de dossier')
                    ->relationship('type_dossier', 'nom'),

            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
