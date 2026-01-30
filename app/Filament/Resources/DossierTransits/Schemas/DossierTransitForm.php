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

class DossierTransitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            TextInput::make('reference')
                ->label('Référence du dossier')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),

            TextInput::make('nom')
                ->label('Nom du dossier')
                ->required()
                ->maxLength(255),

            FileUpload::make('repertoire')
                ->label('Répertoire de stockage')
                ->helperText('Fichier PDF/Image du document concerné.')
                ->disk('public')
                ->directory(
                    fn($record) =>
                    $record
                    ? "dossiers-transit/{$record->id}"
                    : 'dossiers-transit/temp'
                )
                ->acceptedFileTypes(['application/pdf'])
                ->maxSize(10240) // 10 MB
                ->preserveFilenames()
                ->openable()
                ->downloadable()
                ->previewable(false)
                ->columnSpanFull(),

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

            Select::make('colis_id')
                ->label('Colis associé')
                ->relationship(
                    name: 'colis',
                    titleAttribute: 'numero_bl'
                )
                ->searchable()
                ->preload()
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
