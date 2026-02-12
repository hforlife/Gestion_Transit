<?php

namespace App\Filament\Resources\Documents\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;

use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class DocumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                // 🔹 Dossier lié (relation)
                TextColumn::make('dossierTransit.nom')
                    ->label('Dossier')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                // 🔹 Type de document
                TextColumn::make('type_document')
                    ->badge()
                    ->colors([
                        'primary' => 'PVC',
                        'success' => 'AE',
                        'warning' => 'CMC',
                        'info' => 'Carte grise',
                        'gray' => 'Plaque',
                    ])
                    ->sortable(),

                // 🔹 Fichier cliquable
                TextColumn::make('fichier')
                    ->label('Fichier')
                    ->formatStateUsing(fn ($state) => 'Voir')
                    ->url(fn ($record) => asset('storage/' . $record->fichier))
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary'),

                // 🔹 Validé ou non
                IconColumn::make('valide')
                    ->label('Validé')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                // 🔹 Date création
                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Modifié le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            /*
            |--------------------------------------------------------------------------
            | FILTRES
            |--------------------------------------------------------------------------
            */
            ->filters([

                SelectFilter::make('type_document')
                    ->label('Type document')
                    ->options([
                        'PVC' => 'PVC',
                        'AE' => 'AE',
                        'CMC' => 'CMC',
                        'Carte grise' => 'Carte grise',
                        'Plaque' => 'Plaque',
                    ]),

                SelectFilter::make('valide')
                    ->label('Validation')
                    ->options([
                        1 => 'Validé',
                        0 => 'Non validé',
                    ]),
            ])

            /*
            |--------------------------------------------------------------------------
            | ACTIONS
            |--------------------------------------------------------------------------
            */
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])

            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50]);
    }
}
