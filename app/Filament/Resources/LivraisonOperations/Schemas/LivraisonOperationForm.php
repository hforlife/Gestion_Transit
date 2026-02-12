<?php

namespace App\Filament\Resources\LivraisonOperations\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LivraisonOperationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('colis_id')
                    ->label('Colis')
                    ->relationship(
                        name: 'colis',
                        titleAttribute: 'numero_bl')
                    ->required(),
                Select::make('agent_id')
                    ->label('Agent de livraison')
                    ->relationship(
                        name: 'agent',
                        titleAttribute: 'name')
                    ->required(),
                DatePicker::make('date_livraison')
                    ->native(false),
                Select::make('statut')
                    ->options([
                        'EN_ROUTE' => 'En Route',
                        'LIVREE' => 'Livrée'
                    ])
                    ->required()
                    ->default('EN_ROUTE'),
            ]);
    }
}
