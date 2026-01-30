<?php

namespace App\Filament\Resources\Expertises\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ExpertiseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('num_pvc')
                    ->label('Numéro PVC'),
                TextInput::make('num_ae')
                    ->label('Numéro Affaire Economique(AE)'),
                TextInput::make('num_cmc')
                    ->label('Numéro CMC'),
                Select::make('etat_expertise')
                    ->label('Etat d\'avancement de l\'expertise')
                    ->options([
                        'EN_ATTENTE' => 'En attente', 
                        'EFFECTUEE' => 'Effectuée'
                        ])
                    ->required(),
                Select::make('etat_pvc')
                    ->label('Etat PVC')
                    ->options([
                        'NON_RECU' => 'Non réçu', 
                        'RECU' => 'Réçu', 
                        'PAYE' => 'Payé'
                        ])
                    ->required(),
                Select::make('etat_ae')
                    ->label('Etat Affaire Economique(AE)')
                    ->options([
                        'NON_VALIDE' => 'Non validé', 
                        'VALIDE' => 'Validé'
                        ])
                    ->required(),
                Select::make('etat_cmc')
                    ->label('Etat CMC')
                    ->options([
                        'NON_RECU' => 'Non réçu', 
                        'RECU' => 'Réçu'
                        ])
                    ->required(),
                Select::make('status')
                    ->options([
                        'EN_COURS' => 'En cours', 
                        'TERMINE' => 'Terminé'
                        ])
                    ->required(),
                Select::make('expert_id')
                    ->label('Expert')
                    ->relationship(
                        'expert',
                        'name'
                    )
                    ->searchable()
                    ->preload()
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
            ]);
    }
}
