<?php

namespace App\Filament\Resources\Clients\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table

            // ✅ Optimisation si relation colis existe
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->withCount('colis')
            )

            ->columns([

                /* ===============================
                 |  IDENTITÉ
                 ===============================*/

                TextColumn::make('nom')
                    ->label('Nom')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-m-envelope')
                    ->url(fn ($record) => "mailto:{$record->email}")
                    ->openUrlInNewTab(),

                TextColumn::make('telephone')
                    ->label('Téléphone')
                    ->copyable()
                    ->icon('heroicon-m-phone')
                    ->url(fn ($record) => "tel:{$record->telephone}")
                    ->openUrlInNewTab(),

                TextColumn::make('adresse')
                    ->label('Adresse')
                    ->limit(25)
                    ->tooltip(fn ($record) => $record->adresse)
                    ->toggleable(),

                /* ===============================
                 |  ACTIVITÉ
                 ===============================*/

                TextColumn::make('colis_count')
                    ->label('Nb Colis')
                    ->badge()
                    ->color(fn ($state) =>
                        $state > 0 ? 'primary' : 'secondary'
                    ),

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

            ->defaultSort('created_at', 'desc')

            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
