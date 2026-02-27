<?php

namespace App\Filament\Pages;

use App\Models\Colis;
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

class EtapeExpertise extends Page implements HasTable
{
    use InteractsWithTable, HasExports;

    protected static ?string $navigationLabel = 'Expertise';
    protected static ?string $title = 'Gestion des Colis - Étape Expertise';
    protected static ?string $slug = 'etape-expertise';
    protected static string | UnitEnum | null $navigationGroup = 'Colis';
    protected static ?int $navigationSort = 6;
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
            'all' => Tab::make('Tous les colis')
                ->icon('heroicon-o-rectangle-stack'),

            'conteneur' => Tab::make('Conteneurs')
                ->icon('heroicon-o-cube')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->whereHas('typeColis', fn ($q) =>
                        $q->where('nom', 'Conteneur')
                    )
                ),

            'vehicule' => Tab::make('Véhicules')
                ->icon('heroicon-o-truck')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->whereHas('typeColis', fn ($q) =>
                        $q->where('nom', 'Véhicules')
                    )
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
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Colis::query()
                    ->with(['typeColis', 'dossierTransit.client', 'agent', 'port'])
                    ->where(function ($query) {
                        $query->whereNotNull('num_pvc')
                              ->orWhereNotNull('num_ae')
                              ->orWhereNotNull('num_cmc')
                              ->orWhereNotNull('etat_expertise');
                    })
            )
            ->columns([
                // Informations principales du colis
                TextColumn::make('numero_bl')
                    ->label('N° BL')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold')
                    ->color('primary')
                    ->description(fn ($record) => $record->description)
                    ->toggleable(),

                TextColumn::make('typeColis.nom')
                    ->label('Type')
                    ->badge()
                    ->color(fn ($record) => 
                        str_contains(strtolower($record->typeColis?->nom ?? ''), 'véhicules') 
                            ? 'warning' 
                            : 'primary'
                    )
                    ->toggleable(),

                TextColumn::make('dossierTransit.client.nom')
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
                    ->color('gray')
                    ->badge()
                    ->toggleable(),

                TextColumn::make('etat_pvc')
                    ->label('État PVC')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'NON_RECU' => 'Non reçu',
                        'RECU' => 'Reçu',
                        'PAYE' => 'Payé',
                        default => $state ?? 'Non défini',
                    })
                    ->colors([
                        'danger' => 'NON_RECU',
                        'warning' => 'RECU',
                        'success' => 'PAYE',
                    ])
                    ->toggleable(),

                TextColumn::make('num_ae')
                    ->label('N° AE')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-document-text')
                    ->color('gray')
                    ->badge()
                    ->toggleable(),

                TextColumn::make('etat_ae')
                    ->label('État AE')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'NON_VALIDE' => 'Non valide',
                        'VALIDE' => 'Valide',
                        default => $state ?? 'Non défini',
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
                    ->color('gray')
                    ->badge()
                    ->toggleable(),

                TextColumn::make('etat_cmc')
                    ->label('État CMC')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'NON_RECU' => 'Non reçu',
                        'RECU' => 'Reçu',
                        default => $state ?? 'Non défini',
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
                        default => $state ?? 'Non défini',
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
                        $record->num_pvc && $record->num_ae && $record->num_cmc &&
                        $record->etat_pvc === 'PAYE' && 
                        $record->etat_ae === 'VALIDE' && 
                        $record->etat_cmc === 'RECU'
                    )
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Statut dossier')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'EN_COURS' => 'En cours',
                        'TERMINE' => 'Terminé',
                        default => $state ?? 'Non défini',
                    })
                    ->colors([
                        'warning' => 'EN_COURS',
                        'success' => 'TERMINE',
                    ])
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
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
                    ->relationship('dossierTransit.client', 'nom')
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
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                ActionGroup::make([

                    // Détails complets
                    Action::make('voir')
                        ->label('Détails complets')
                        ->icon('heroicon-o-eye')
                        ->url(fn (Colis $record): string => 
                            \App\Filament\Resources\Colis\ColisResource::getUrl('view', ['record' => $record])
                        )
                        ->color('info')
                        ->openUrlInNewTab(false),

                    // ✅ IMPRESSION RAPPORT EXPERTISE (via trait)
                    $this->getPrintAction('pdf.etape-expertise', 'Imprimer rapport expertise'),

                    // Gérer expertise
                    Action::make('gerer_expertise')
                        ->label('Gérer expertise')
                        ->icon('heroicon-o-clipboard-document-list')
                        ->color('primary')
                        ->action(function (Colis $record, array $data) {
                            $record->update([
                                'num_pvc' => $data['num_pvc'] ?? $record->num_pvc,
                                'etat_pvc' => $data['etat_pvc'] ?? $record->etat_pvc,
                                'num_ae' => $data['num_ae'] ?? $record->num_ae,
                                'etat_ae' => $data['etat_ae'] ?? $record->etat_ae,
                                'num_cmc' => $data['num_cmc'] ?? $record->num_cmc,
                                'etat_cmc' => $data['etat_cmc'] ?? $record->etat_cmc,
                                'etat_expertise' => $data['etat_expertise'] ?? $record->etat_expertise,
                                'status' => $data['status'] ?? $record->status,
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
                                            ->prefix('PVC')
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
                                            ->prefix('AE')
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
                                            ->prefix('CMC')
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

                            Grid::make(2)->schema([
                                Select::make('etat_expertise')
                                    ->label('État de l\'expertise')
                                    ->options([
                                        'EN_ATTENTE' => 'En attente',
                                        'EFFECTUEE' => 'Effectuée',
                                    ])
                                    ->required()
                                    ->native(false),

                                Select::make('status')
                                    ->label('Statut dossier')
                                    ->options([
                                        'EN_COURS' => 'En cours',
                                        'TERMINE' => 'Terminé',
                                    ])
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
                        ->visible(fn (Colis $record): bool => 
                            $record->num_pvc || $record->num_ae || $record->num_cmc
                        )
                        ->action(function (Colis $record) {
                            if ($record->num_pvc) $record->update(['etat_pvc' => 'PAYE']);
                            if ($record->num_ae) $record->update(['etat_ae' => 'VALIDE']);
                            if ($record->num_cmc) $record->update(['etat_cmc' => 'RECU']);
                            
                            // Vérifier si tous les documents sont validés
                            if ($record->etat_pvc === 'PAYE' && 
                                $record->etat_ae === 'VALIDE' && 
                                $record->etat_cmc === 'RECU') {
                                $record->update([
                                    'etat_expertise' => 'EFFECTUEE',
                                    'status' => 'TERMINE'
                                ]);

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
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                if ($record->num_pvc) $record->update(['etat_pvc' => 'PAYE']);
                                if ($record->num_ae) $record->update(['etat_ae' => 'VALIDE']);
                                if ($record->num_cmc) $record->update(['etat_cmc' => 'RECU']);
                                
                                if ($record->etat_pvc === 'PAYE' && 
                                    $record->etat_ae === 'VALIDE' && 
                                    $record->etat_cmc === 'RECU') {
                                    $record->update([
                                        'etat_expertise' => 'EFFECTUEE',
                                        'status' => 'TERMINE'
                                    ]);
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
                        ->action(fn ($records) => $records->each->update([
                            'etat_expertise' => 'EFFECTUEE',
                            'status' => 'TERMINE'
                        ]))
                        ->requiresConfirmation(),

                    BulkAction::make('export_csv_bulk')
                        ->label('Exporter sélection (CSV)')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->action(function ($records) {
                            $headers = [
                                'Content-Type' => 'text/csv',
                                'Content-Disposition' => 'attachment; filename="Expertise-selection-' . now()->format('Y-m-d') . '.csv"',
                            ];

                            $callback = function() use ($records) {
                                $file = fopen('php://output', 'w');
                                
                                // En-têtes
                                fputcsv($file, ['N° BL', 'Client', 'Type', 'État expertise', 'PVC', 'État PVC', 'AE', 'État AE', 'CMC', 'État CMC']);
                                
                                foreach ($records as $record) {
                                    fputcsv($file, [
                                        $record->numero_bl,
                                        $record->dossierTransit?->client?->nom ?? 'N/A',
                                        $record->typeColis?->nom ?? 'N/A',
                                        $record->etat_expertise ?? 'N/A',
                                        $record->num_pvc ?? 'N/A',
                                        $record->etat_pvc ?? 'N/A',
                                        $record->num_ae ?? 'N/A',
                                        $record->etat_ae ?? 'N/A',
                                        $record->num_cmc ?? 'N/A',
                                        $record->etat_cmc ?? 'N/A',
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

    public static function getNavigationBadge(): ?string
    {
        return (string) Colis::query()
            ->where('etat_expertise', 'EN_ATTENTE')
            ->count();
    }

    public static function getNavigationLabel(): string
    {
        return '3 - Etape Expertise';
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = Colis::query()
            ->where('etat_expertise', 'EN_ATTENTE')
            ->count();

        return match (true) {
            $count > 20 => 'danger',
            $count > 10 => 'warning',
            default => 'success',
        };
    }
}