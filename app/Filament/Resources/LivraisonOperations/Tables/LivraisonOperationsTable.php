<?php

namespace App\Filament\Resources\LivraisonOperations\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;

use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class LivraisonOperationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                /*
                |--------------------------------------------------------------------------
                | COLIS
                |--------------------------------------------------------------------------
                */
                TextColumn::make('colis.numero_bl')
                    ->label('N° BL')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => 
                        $record->colis?->client?->nom
                    ),

                /*
                |--------------------------------------------------------------------------
                | AGENT
                |--------------------------------------------------------------------------
                */
                TextColumn::make('agent.name')
                    ->label('Agent')
                    ->searchable()
                    ->sortable(),

                /*
                |--------------------------------------------------------------------------
                | DATE LIVRAISON
                |--------------------------------------------------------------------------
                */
                TextColumn::make('date_livraison')
                    ->label('Date livraison')
                    ->date('d/m/Y')
                    ->sortable(),

                /*
                |--------------------------------------------------------------------------
                | STATUT
                |--------------------------------------------------------------------------
                */
                TextColumn::make('statut')
                    ->badge()
                    ->colors([
                        'warning' => 'EN_ATTENTE',
                        'info' => 'EN_COURS',
                        'success' => 'LIVREE',
                        'danger' => 'ANNULEE',
                    ])
                    ->sortable(),

                /*
                |--------------------------------------------------------------------------
                | INDICATEUR VISUEL LIVRÉ
                |--------------------------------------------------------------------------
                */
                IconColumn::make('statut')
                    ->label('Livré')
                    ->boolean(fn ($record) => $record->statut === 'LIVREE')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning'),

                /*
                |--------------------------------------------------------------------------
                | DATES SYSTÈME
                |--------------------------------------------------------------------------
                */
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

                SelectFilter::make('statut')
                    ->options([
                        'EN_ATTENTE' => 'En attente',
                        'EN_COURS' => 'En cours',
                        'LIVREE' => 'Livrée',
                        'ANNULEE' => 'Annulée',
                    ]),

                SelectFilter::make('agent_id')
                    ->relationship('agent', 'name')
                    ->label('Agent'),
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

            ->defaultSort('date_livraison', 'desc')
            ->striped()
            ->paginated([10, 25, 50]);
    }
}
