<?php

namespace App\Filament\Widgets;

use App\Models\Client;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class ClientsParColisTable extends TableWidget
{
    protected static ?string $heading = 'Classement des clients par nombre de colis';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Client::query()
                    ->withCount('colis')
                    ->orderByDesc('colis_count')
            )
            ->columns([
                Tables\Columns\TextColumn::make('nom')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('colis_count')
                    ->label('Nombre de colis')
                    ->badge()
                    ->color('primary')
                    ->sortable(),
            ])
            ->defaultSort('colis_count', 'desc');
    }
}
