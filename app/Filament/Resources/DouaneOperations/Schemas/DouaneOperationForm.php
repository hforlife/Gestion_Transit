<?php

namespace App\Filament\Resources\DouaneOperations\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DouaneOperationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('num_t1'),
                Select::make('etat_t1')
                    ->options([
                        'FOURNI' => 'Fourni',
                        'PAYE' => 'Payé'
                    ]),
                TextInput::make('declaration_reference')
                    ->label('Référence Déclaration'),
                DatePicker::make('date_entree_douane')
                    ->label('Date d\'entree à la douane')
                    ->native(false),
                DatePicker::make('date_sortie_douane')
                    ->label('Date de sortie à la douane')
                    ->native(false),
                Select::make('status_colis')
                    ->label('Etat du colis')
                    ->options([
                        'EN_ATTENTE' => 'En attente',
                        'ENTRE' => 'Entrée',
                        'SORTI' => 'Sortie'
                    ])
                    ->required(),
                Select::make('colis_id')
                    ->label('Colis')
                    ->relationship(
                        name: 'colis',
                        titleAttribute: 'numero_bl'
                    )
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('agent_id')
                    ->label('Agent')
                    ->relationship(
                        name: 'agent',
                        titleAttribute: 'name'
                    )
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }
}
