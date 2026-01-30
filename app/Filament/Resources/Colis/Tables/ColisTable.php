<?php

namespace App\Filament\Resources\Colis\Tables;

use App\Filament\Resources\Colis\ColisResource;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Text;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ColisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('numero_bl')
                    ->label('BL')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('description')
                    ->limit(30)
                    ->tooltip(fn($record) => $record->description)
                    ->searchable(),

                TextColumn::make('etat_colis')
                    ->label('Statut')
                    ->badge()
                    ->colors([
                        'secondary' => 'BL_ENREGISTRE',
                        'info' => 'AU_PORT',
                        'warning' => 'A_LA_DOUANE',
                        'primary' => 'EN_ROUTE',
                        'success' => 'LIVRE',
                        'danger' => 'CLOTURE',
                    ])
                    ->sortable(),

                TextColumn::make('typeColis.nom')
                    ->label('Type de colis')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('client.nom')
                    ->label('Client')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('port.nom')
                    ->label('Port')
                    ->sortable(),

                TextColumn::make('agent.name')
                    ->label('Agent')
                    ->sortable(),

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
                // à venir : par statut, port, client
            ])
            ->recordActions([
                // Action::make('tracking')
                //     ->label('Tracking')
                //     ->icon('heroicon-o-clock')
                //     ->url(fn($record) => ColisResource::getUrl('tracking', ['record' => $record]))
                //     ->openUrlInNewTab(),

                EditAction::make()
                    ->visible(fn($record) => $record->etat_colis !== 'CLOTURE'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn() => auth()->user()->hasRole('admin')),
                ]),
            ]);
    }
}
