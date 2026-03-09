<?php

namespace App\Filament\Pages;

use App\Models\ColisUnite;
use App\Filament\Traits\HasExports;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\BulkAction;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Schemas\Components\Section;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Tabs\Tab;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Illuminate\Support\Collection;

class EtapeExpertise extends Page implements HasTable
{
    use InteractsWithTable, HasExports, HasPageShield;

    protected static ?string $navigationLabel = 'Expertise';
    protected static ?string $title = 'Gestion des Unités - Étape Expertise';
    protected static ?string $slug = 'etape-expertise';
    protected static string | UnitEnum | null $navigationGroup = 'Colis / BL';
    protected static ?int $navigationSort = 7;
    protected string $view = 'filament.pages.etape-expertise';

    public static function canAccess(): bool
    {
        return auth()->user()->can('View:EtapeExpertise');
    }

    /**
     * Configuration des onglets de filtrage
     */
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Toutes les unités')
                ->icon('heroicon-o-rectangle-stack'),

            'conteneur' => Tab::make('Conteneurs')
                ->icon('heroicon-o-cube')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('type', 'CONTENEUR')
                ),

            'chassis' => Tab::make('Châssis')
                ->icon('heroicon-o-truck')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->whereIn('type', ['CHASSIS', 'CHASSIS_VOITURE', 'CHASSIS_MACHINE'])
                ),

            'en_attente' => Tab::make('En attente')
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('etat_expertise', 'EN_ATTENTE')
                ),

            'effectuee' => Tab::make('Effectuée')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('etat_expertise', 'EFFECTUEE')
                ),

            'pvc_manquant' => Tab::make('PVC manquant')
                ->icon('heroicon-o-document-minus')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->whereNull('num_pvc')
                ),

            'ae_manquant' => Tab::make('AE manquant')
                ->icon('heroicon-o-document-minus')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->whereNull('num_ae')
                ),

            'cmc_manquant' => Tab::make('CMC manquant')
                ->icon('heroicon-o-document-minus')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->whereNull('num_cmc')
                ),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ColisUnite::query()
                    ->with(['colis.typeColis', 'colis.dossierTransit.client', 'colis.agent'])
                    ->whereIn('type', ['CHASSIS', 'CHASSIS_VOITURE', 'CHASSIS_MACHINE'])
                    ->where(function ($query) {
                        $query->whereNotNull('num_pvc')
                              ->orWhereNotNull('num_ae')
                              ->orWhereNotNull('num_cmc')
                              ->orWhereNotNull('etat_expertise');
                    })
            )
            ->columns([
                // Informations du colis parent
                TextColumn::make('colis.numero_bl')
                    ->label('N° BL')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold')
                    ->color('primary')
                    ->description(fn ($record) => $record->colis?->description)
                    ->url(fn ($record) => $record->colis ?
                        \App\Filament\Resources\Colis\ColisResource::getUrl('view', ['record' => $record->colis]) : null)
                    ->openUrlInNewTab()
                    ->toggleable(),

                // Informations de l'unité
                TextColumn::make('type')
                    ->label("Type d'unité")
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'CONTENEUR' => 'info',
                        'CHASSIS' => 'warning',
                        'CHASSIS_VOITURE' => 'success',
                        'CHASSIS_MACHINE' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'CONTENEUR' => 'Conteneur',
                        'CHASSIS' => 'Châssis',
                        'CHASSIS_VOITURE' => 'Châssis voiture',
                        'CHASSIS_MACHINE' => 'Châssis machine',
                        default => $state,
                    })
                    ->toggleable(),

                TextColumn::make('numero_chassis')
                    ->label('N° Châssis')
                    ->searchable()
                    ->copyable()
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('vin')
                    ->label('VIN')
                    ->searchable()
                    ->copyable()
                    ->placeholder('—')
                    ->limit(10)
                    ->toggleable(),

                // Client
                TextColumn::make('colis.dossierTransit.client.nom')
                    ->label('Client')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                // Documents d'expertise
                TextColumn::make('num_pvc')
                    ->label('N° PVC')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-document-text')
                    ->badge()
                    ->color(fn ($state) => $state ? 'info' : 'gray')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('etat_pvc')
                    ->label('État PVC')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'NON_RECU' => 'Non reçu',
                        'RECU' => 'Reçu',
                        'PAYE' => 'Payé',
                        default => 'Non défini',
                    })
                    ->colors([
                        'danger' => 'NON_RECU',
                        'warning' => 'RECU',
                        'success' => 'PAYE',
                        'gray' => null,
                    ])
                    ->toggleable(),

                TextColumn::make('num_ae')
                    ->label('N° AE')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-document-text')
                    ->badge()
                    ->color(fn ($state) => $state ? 'info' : 'gray')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('etat_ae')
                    ->label('État AE')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'NON_VALIDE' => 'Non valide',
                        'VALIDE' => 'Valide',
                        default => 'Non défini',
                    })
                    ->colors([
                        'danger' => 'NON_VALIDE',
                        'success' => 'VALIDE',
                    ])
                    ->toggleable(),

                TextColumn::make('num_cmc')
                    ->label('N° CMC')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-document-text')
                    ->badge()
                    ->color(fn ($state) => $state ? 'info' : 'gray')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('etat_cmc')
                    ->label('État CMC')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'NON_RECU' => 'Non reçu',
                        'RECU' => 'Reçu',
                        default => 'Non défini',
                    })
                    ->colors([
                        'danger' => 'NON_RECU',
                        'success' => 'RECU',
                    ])
                    ->toggleable(),

                // Statut global de l'expertise
                TextColumn::make('etat_expertise')
                    ->label('État expertise')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'EN_ATTENTE' => 'En attente',
                        'EFFECTUEE' => 'Effectuée',
                        default => 'Non défini',
                    })
                    ->colors([
                        'warning' => 'EN_ATTENTE',
                        'success' => 'EFFECTUEE',
                    ])
                    ->icon(fn ($state) => match ($state) {
                        'EN_ATTENTE' => 'heroicon-o-clock',
                        'EFFECTUEE' => 'heroicon-o-check-circle',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->toggleable(),

                IconColumn::make('documents_complets')
                    ->label('Complet')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->getStateUsing(fn ($record): bool =>
                        !is_null($record->num_pvc) &&
                        !is_null($record->num_ae) &&
                        !is_null($record->num_cmc) &&
                        $record->etat_pvc === 'PAYE' &&
                        $record->etat_ae === 'VALIDE' &&
                        $record->etat_cmc === 'RECU'
                    )
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label("Type d'unité")
                    ->options([
                        'CONTENEUR' => 'Conteneur',
                        'CHASSIS' => 'Châssis',
                        'CHASSIS_VOITURE' => 'Châssis voiture',
                        'CHASSIS_MACHINE' => 'Châssis machine',
                    ])
                    ->multiple(),

                SelectFilter::make('etat_expertise')
                    ->label('État expertise')
                    ->options([
                        'EN_ATTENTE' => 'En attente',
                        'EFFECTUEE' => 'Effectuée',
                    ])
                    ->multiple(),

                SelectFilter::make('etat_pvc')
                    ->label('État PVC')
                    ->options([
                        'NON_RECU' => 'Non reçu',
                        'RECU' => 'Reçu',
                        'PAYE' => 'Payé',
                    ])
                    ->multiple(),

                SelectFilter::make('etat_ae')
                    ->label('État AE')
                    ->options([
                        'NON_VALIDE' => 'Non valide',
                        'VALIDE' => 'Valide',
                    ])
                    ->multiple(),

                SelectFilter::make('etat_cmc')
                    ->label('État CMC')
                    ->options([
                        'NON_RECU' => 'Non reçu',
                        'RECU' => 'Reçu',
                    ])
                    ->multiple(),

                SelectFilter::make('client_id')
                    ->label('Client')
                    ->relationship('colis.dossierTransit.client', 'nom')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Filter::make('documents_manquants')
                    ->label('Documents manquants')
                    ->query(fn (Builder $query): Builder => $query->where(function ($q) {
                        $q->whereNull('num_pvc')
                          ->orWhereNull('num_ae')
                          ->orWhereNull('num_cmc');
                    }))
                    ->toggle(),

                Filter::make('documents_complets')
                    ->label('Documents complets')
                    ->query(fn (Builder $query): Builder => $query
                        ->whereNotNull('num_pvc')
                        ->whereNotNull('num_ae')
                        ->whereNotNull('num_cmc')
                    )
                    ->toggle(),
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                ActionGroup::make([

                    // Détails complets
                    Action::make('voir_colis')
                        ->label('Voir le BL')
                        ->icon('heroicon-o-document-text')
                        ->url(fn (ColisUnite $record): string =>
                            \App\Filament\Resources\Colis\ColisResource::getUrl('view', ['record' => $record->colis])
                        )
                        ->color('info')
                        ->openUrlInNewTab(),

                    // ✅ IMPRESSION RAPPORT EXPERTISE (via trait)
                    $this->getPrintAction('pdf.etape-expertise', 'Imprimer rapport expertise'),

                    // Gérer expertise
                    Action::make('gerer_expertise')
                        ->label('Gérer expertise')
                        ->icon('heroicon-o-clipboard-document-list')
                        ->color('primary')
                        ->action(function (ColisUnite $record, array $data) {
                            $record->update([
                                'num_pvc' => $data['num_pvc'] ?? $record->num_pvc,
                                'etat_pvc' => $data['etat_pvc'] ?? $record->etat_pvc,
                                'num_ae' => $data['num_ae'] ?? $record->num_ae,
                                'etat_ae' => $data['etat_ae'] ?? $record->etat_ae,
                                'num_cmc' => $data['num_cmc'] ?? $record->num_cmc,
                                'etat_cmc' => $data['etat_cmc'] ?? $record->etat_cmc,
                                'etat_expertise' => $data['etat_expertise'] ?? $record->etat_expertise,
                            ]);

                            Notification::make()
                                ->title('Expertise mise à jour')
                                ->success()
                                ->send();
                        })
                        ->form([
                            Grid::make(3)->schema([
                                // Section PVC
                                Section::make('PVC')
                                    ->compact()
                                    ->schema([
                                        TextInput::make('num_pvc')
                                            ->label('N° PVC')
                                            ->placeholder('Ex: PVC-2024-001')
                                            ->maxLength(50),

                                        Select::make('etat_pvc')
                                            ->label('État')
                                            ->options([
                                                'NON_RECU' => 'Non reçu',
                                                'RECU' => 'Reçu',
                                                'PAYE' => 'Payé',
                                            ])
                                            ->native(false),
                                    ])->columnSpan(1),

                                // Section AE
                                Section::make('AE')
                                    ->compact()
                                    ->schema([
                                        TextInput::make('num_ae')
                                            ->label('N° AE')
                                            ->placeholder('Ex: AE-2024-001')
                                            ->maxLength(50),

                                        Select::make('etat_ae')
                                            ->label('État')
                                            ->options([
                                                'NON_VALIDE' => 'Non valide',
                                                'VALIDE' => 'Valide',
                                            ])
                                            ->native(false),
                                    ])->columnSpan(1),

                                // Section CMC
                                Section::make('CMC')
                                    ->compact()
                                    ->schema([
                                        TextInput::make('num_cmc')
                                            ->label('N° CMC')
                                            ->placeholder('Ex: CMC-2024-001')
                                            ->maxLength(50),

                                        Select::make('etat_cmc')
                                            ->label('État')
                                            ->options([
                                                'NON_RECU' => 'Non reçu',
                                                'RECU' => 'Reçu',
                                            ])
                                            ->native(false),
                                    ])->columnSpan(1),
                            ]),

                            Grid::make(1)->schema([
                                Select::make('etat_expertise')
                                    ->label('État de l\'expertise')
                                    ->options([
                                        'EN_ATTENTE' => 'En attente',
                                        'EFFECTUEE' => 'Effectuée',
                                    ])
                                    ->required()
                                    ->native(false),
                            ]),
                        ])
                        ->modalHeading('Gestion de l\'expertise')
                        ->modalButton('Enregistrer')
                        ->modalWidth('4xl'),

                    // Valider documents
                    Action::make('valider_document')
                        ->label('Valider')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->visible(fn (ColisUnite $record): bool =>
                            $record->num_pvc || $record->num_ae || $record->num_cmc
                        )
                        ->action(function (ColisUnite $record) {
                            if ($record->num_pvc) $record->update(['etat_pvc' => 'PAYE']);
                            if ($record->num_ae) $record->update(['etat_ae' => 'VALIDE']);
                            if ($record->num_cmc) $record->update(['etat_cmc' => 'RECU']);

                            // Vérifier si tous les documents sont validés
                            if ($record->etat_pvc === 'PAYE' &&
                                $record->etat_ae === 'VALIDE' &&
                                $record->etat_cmc === 'RECU') {
                                $record->update(['etat_expertise' => 'EFFECTUEE']);

                                Notification::make()
                                    ->title('Expertise complétée')
                                    ->body('Tous les documents sont validés')
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Documents validés')
                                    ->body('Les documents ont été validés')
                                    ->success()
                                    ->send();
                            }
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Valider les documents')
                        ->modalDescription('Êtes-vous sûr de vouloir valider tous les documents ?')
                        ->modalSubmitActionLabel('Oui, valider'),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('valider_documents')
                        ->label('Valider documents')
                        ->icon('heroicon-o-check-circle')
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                if ($record->num_pvc) $record->update(['etat_pvc' => 'PAYE']);
                                if ($record->num_ae) $record->update(['etat_ae' => 'VALIDE']);
                                if ($record->num_cmc) $record->update(['etat_cmc' => 'RECU']);

                                if ($record->etat_pvc === 'PAYE' &&
                                    $record->etat_ae === 'VALIDE' &&
                                    $record->etat_cmc === 'RECU') {
                                    $record->update(['etat_expertise' => 'EFFECTUEE']);
                                }
                            }

                            Notification::make()
                                ->title('Documents validés')
                                ->body('Les documents sélectionnés ont été validés')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('marquer_termine')
                        ->label('Marquer terminé')
                        ->icon('heroicon-o-check-badge')
                        ->action(fn (Collection $records) => $records->each->update([
                            'etat_expertise' => 'EFFECTUEE',
                        ]))
                        ->requiresConfirmation(),

                    BulkAction::make('exporter_csv')
                        ->label('Exporter sélection (CSV)')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->action(function (Collection $records) {
                            $headers = [
                                'Content-Type' => 'text/csv',
                                'Content-Disposition' => 'attachment; filename="expertise-selection-' . now()->format('Y-m-d') . '.csv"',
                            ];

                            $callback = function() use ($records) {
                                $file = fopen('php://output', 'w');

                                // En-têtes
                                fputcsv($file, [
                                    'N° BL',
                                    'Type',
                                    'N° Châssis',
                                    'VIN',
                                    'Client',
                                    'État expertise',
                                    'N° PVC',
                                    'État PVC',
                                    'N° AE',
                                    'État AE',
                                    'N° CMC',
                                    'État CMC'
                                ]);

                                foreach ($records as $record) {
                                    fputcsv($file, [
                                        $record->colis?->numero_bl ?? 'N/A',
                                        $record->type ?? 'N/A',
                                        $record->numero_chassis ?? 'N/A',
                                        $record->vin ?? 'N/A',
                                        $record->colis?->dossierTransit?->client?->nom ?? 'N/A',
                                        match($record->etat_expertise) {
                                            'EN_ATTENTE' => 'En attente',
                                            'EFFECTUEE' => 'Effectuée',
                                            default => 'Non défini',
                                        },
                                        $record->num_pvc ?? 'N/A',
                                        match($record->etat_pvc) {
                                            'NON_RECU' => 'Non reçu',
                                            'RECU' => 'Reçu',
                                            'PAYE' => 'Payé',
                                            default => 'N/A',
                                        },
                                        $record->num_ae ?? 'N/A',
                                        match($record->etat_ae) {
                                            'NON_VALIDE' => 'Non valide',
                                            'VALIDE' => 'Valide',
                                            default => 'N/A',
                                        },
                                        $record->num_cmc ?? 'N/A',
                                        match($record->etat_cmc) {
                                            'NON_RECU' => 'Non reçu',
                                            'RECU' => 'Reçu',
                                            default => 'N/A',
                                        },
                                    ]);
                                }

                                fclose($file);
                            };

                            return response()->stream($callback, 200, $headers);
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s');
    }

    public static function getNavigationLabel(): string
    {
        return '3 - Étape Expertise';
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) ColisUnite::query()
            ->whereIn('type', ['CHASSIS', 'CHASSIS_VOITURE', 'CHASSIS_MACHINE'])
            ->where('etat_expertise', 'EN_ATTENTE')
            ->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = ColisUnite::query()
            ->whereIn('type', ['CHASSIS', 'CHASSIS_VOITURE', 'CHASSIS_MACHINE'])
            ->where('etat_expertise', 'EN_ATTENTE')
            ->count();

        return match (true) {
            $count > 50 => 'danger',
            $count > 20 => 'warning',
            $count > 0 => 'info',
            default => 'success',
        };
    }
}
