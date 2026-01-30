<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nom & Prénom')
                    ->required(),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required(),
                TextInput::make('password')
                    ->label('Mot de Passe')
                    ->password()
                    ->visibleOn('create')
                    ->required(),
                TextInput::make('telephone')
                    ->tel(),
                // Champ rôle
                Select::make('roles')
                    ->label('Rôle')
                    ->relationship('roles', 'name') // relation Spatie Role
                    ->required(),
                Toggle::make('is_active')
                    ->label('Actif')
                    ->required(),
            ]);
    }
}
