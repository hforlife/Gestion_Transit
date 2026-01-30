<?php

namespace App\Filament\Resources\DouaneOperations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DouaneOperationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('num_t1')
                    ->searchable(),
                TextColumn::make('etat_t1')
                    ->badge(),
                TextColumn::make('declaration_reference')
                    ->searchable(),
                TextColumn::make('date_entree_douane')
                    ->date()
                    ->sortable(),
                TextColumn::make('date_sortie_douane')
                    ->date()
                    ->sortable(),
                TextColumn::make('status_colis')
                    ->badge(),
                TextColumn::make('colis_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('agent_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
