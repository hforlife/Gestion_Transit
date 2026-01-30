<?php

namespace App\Filament\Resources\PortOperations\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PortOperationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('date_entree_port')
                    ->label('Date d\'entrée au port')
                    ->native(false),
                DatePicker::make('date_sortie_port')
                    ->label('Date de sortie au port')
                    ->native(false),
                Select::make('status_colis')
                    ->label('Status du colis')
                    ->options([
                        'EN_ATTENTE' => 'En attente', 
                        'ENTRE' => 'Entrée', 
                        'SORTI' => 'Sortie'])
                    ->required(),
                Select::make('colis_id')
                    ->relationship(
                    name: 'colis',
                    titleAttribute: 'numero_bl'
                    )
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('agent_id')
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
