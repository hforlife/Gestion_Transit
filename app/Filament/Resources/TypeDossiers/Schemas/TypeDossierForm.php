<?php

namespace App\Filament\Resources\TypeDossiers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TypeDossierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nom')
                    ->required(),
                TextInput::make('description'),
            ]);
    }
}
