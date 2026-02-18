<?php

namespace App\Filament\Resources\DossierTransits\Schemas;

use App\Models\Colis;
use App\Models\TypeDossier;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rule;
use Filament\Actions\Action;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DossierTransitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            
            Select::make('colis_id')
                ->label('Colis associé')
                ->relationship(
                    name: 'colis',
                    titleAttribute: 'numero_bl'
                )
                ->searchable()
                ->preload()
                ->required()
                ->live(),

            TextInput::make('reference')
                ->label('Référence du dossier')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255)
                ->readOnly()
                ->suffixAction(
                    Action::make('generateReference')
                        ->icon('heroicon-o-arrow-path')
                        ->tooltip('Générer automatiquement')
                        ->action(function ($get, $set) {

                            $colisId = $get('colis_id');

                            if (! $colisId) {
                                return;
                            }

                            $colis = Colis::with('typeColis')->find($colisId);

                            if (! $colis || ! $colis->typeColis) {
                                return;
                            }

                            $typeNom = strtolower($colis->typeColis->nom);

                            // Déterminer la lettre
                            $prefix = match (true) {
                                str_contains($typeNom, 'véhicule') => 'V',
                                str_contains($typeNom, 'conteneur') => 'C',
                                default => 'D',
                            };

                            $year = Carbon::now()->year;

                            // Compter combien de dossiers cette année avec ce prefix
                            $count = \App\Models\DossierTransit::where('reference', 'like', "{$prefix}{$year}-%")
                                ->count();

                            $nextNumber = str_pad($count + 1, 4, '0', STR_PAD_LEFT);

                            $reference = "{$prefix}{$year}-{$nextNumber}";

                            $set('reference', $reference);
                        })
                ),


            TextInput::make('nom')
                ->label('Nom du dossier')
                ->required()
                ->maxLength(255),

            DatePicker::make('date_depot')
                ->label('Date de dépôt')
                ->native(false)
                ->maxDate(now()),

            Select::make('status')
                ->label('Statut du dossier')
                ->options([
                    'OUVERT' => 'Ouvert',
                    'EN_COURS' => 'En cours',
                    'CLOTURE' => 'Clôturé',
                ])
                ->default('OUVERT')
                ->required(),


            Select::make('id_type_dossier')
                ->label('Type de dossier')
                ->relationship(
                    name: 'type_dossier',
                    titleAttribute: 'nom'
                )
                ->searchable()
                ->preload()
                ->required(),

        ]);
    }
}
