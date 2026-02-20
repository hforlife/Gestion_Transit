<?php

namespace App\Filament\Resources\DossierTransits\Schemas;

use App\Models\Colis;
use App\Models\DossierTransit;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DossierTransitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Select::make('client_id')
                ->label('Client associé')
                ->relationship(
                    name: 'client',
                    titleAttribute: 'nom'
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
                        ->action(function ($get, $set, $livewire) {

                            $record = $livewire->record;

                            $prefix = 'C'; // défaut

                            /*
                |--------------------------------------------------
                | Si on modifie un dossier existant
                |--------------------------------------------------
                */
                            if ($record && $record->colis) {

                                $typeNom = strtolower($record->colis->typeColis->nom ?? '');

                                $prefix = match (true) {
                                    str_contains($typeNom, 'véhicule') => 'V',
                                    str_contains($typeNom, 'conteneur') => 'C',
                                    default => 'C',
                                };
                            }

                            /*
                |--------------------------------------------------
                | Si on est en création
                |--------------------------------------------------
                */
                            if (! $record) {

                                $clientId = $get('client_id');

                                $colis = Colis::with('typeColis')
                                    ->whereHas('dossierTransit', function ($query) use ($clientId) {
                                        $query->where('client_id', $clientId);
                                    })
                                    ->latest()
                                    ->first();

                                if ($colis) {
                                    $typeNom = strtolower($colis->typeColis->nom ?? '');

                                    $prefix = match (true) {
                                        str_contains($typeNom, 'véhicule') => 'V',
                                        str_contains($typeNom, 'conteneur') => 'C',
                                        default => 'C',
                                    };
                                }
                            }

                            $year = Carbon::now()->year;

                            $count = DossierTransit::where('reference', 'like', "{$prefix}{$year}-%")
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
