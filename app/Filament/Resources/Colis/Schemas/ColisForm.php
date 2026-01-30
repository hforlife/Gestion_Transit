<?php

namespace App\Filament\Resources\Colis\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ColisForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('numero_bl')
                    ->label('Numéro BL')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(100),

                TextInput::make('description')
                    ->label('Description')
                    ->columnSpanFull(),

                Select::make('etat_colis')
                    ->label('État du colis')
                    ->options([
                        'BL_ENREGISTRE' => 'BL enregistré',
                        'AU_PORT' => 'Au port',
                        'A_LA_DOUANE' => 'À la douane',
                        'EN_ROUTE' => 'En route',
                        'LIVRE' => 'Livré',
                        'CLOTURE' => 'Clôturé',
                    ])
                    ->default('BL_ENREGISTRE')
                    ->required()
                    ->disabled(fn ($record) => $record?->etat_colis === 'CLOTURE'),

                Select::make('id_type_colis')
                    ->label('Type de colis')
                    ->relationship('typeColis', 'nom')
                    ->required()
                    ->preload()
                    ->searchable()
                    ->disabled(fn ($record) => filled($record)),

                Select::make('client_id')
                    ->label('Client')
                    ->relationship('client', 'nom')
                    ->required()
                    ->searchable()
                    ->preload(),

                Select::make('id_port')
                    ->label('Port d’entrée')
                    ->relationship('port', 'nom')
                    ->required()
                    ->searchable()
                    ->preload(),

                Select::make('user_id')
                    ->label('Agent responsable')
                    ->relationship('agent', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->default(auth()->id()),
            ]);
    }
}
