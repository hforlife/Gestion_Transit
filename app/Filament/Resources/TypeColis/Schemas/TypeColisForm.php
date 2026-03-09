<?php

namespace App\Filament\Resources\TypeColis\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TypeColisForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nom')
                    ->placeholder('Nom du type de colis')
                    ->required(),
                TextInput::make('description'),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
