<?php

namespace App\Filament\Resources\LivraisonOperations\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LivraisonOperationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('colis_id')
                    ->required()
                    ->numeric(),
                TextInput::make('agent_id')
                    ->required()
                    ->numeric(),
                DatePicker::make('date_livraison'),
                TextInput::make('statut')
                    ->required()
                    ->default('EN_ROUTE'),
            ]);
    }
}
