<?php

namespace App\Filament\Resources\Documents\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id_dossier_transit')
                    ->label('Dossier associé')
                    ->relationship(
                        name: 'dossierTransit',
                        titleAttribute: 'nom'
                    )
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('type_document')
                    ->options([
                        'PVC' => 'PVC',
                        'AE' => 'AE',
                        'CMC' => 'CMC',
                        'Carte grise' => 'Carte grise',
                        'Plaque' => 'Plaque',
                    ])
                    ->required(),
                FileUpload::make('fichier')
                    ->label('Fichier du document')
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
                Toggle::make('valide')
                    ->required(),
            ]);
    }
}
