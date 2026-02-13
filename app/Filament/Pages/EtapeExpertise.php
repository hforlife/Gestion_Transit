<?php

namespace App\Filament\Pages;

use App\Models\Colis;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Builder;
use BackedEnum;
use UnitEnum;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\BulkAction;

class EtapeExpertise extends Page implements HasTable
{
    use InteractsWithTable;

    // protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';
    
    protected static ?string $navigationLabel = 'Expertise';
    
    protected static ?string $title = 'Gestion des Colis - Étape Expertise';
    
    protected static ?string $slug = 'etape-expertise';
    
    protected static string | UnitEnum | null $navigationGroup = 'Colis';
    
    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.etape-expertise';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Colis::query()
                    ->with(['typeColis', 'client', 'agent', 'port'])
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

                TextColumn::make('etat_colis')
                    ->label('État')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'en_attente' => 'En attente',
                        'en_transit' => 'En transit',
                        'livre' => 'Livré',
                        'retenu' => 'Retenu',
                        'perdu' => 'Perdu',
                        default => $state,
                    })
                    ->colors([
                        'warning' => 'en_attente',
                        'info' => 'en_transit',
                        'success' => 'livre',
                        'danger' => 'retenu',
                        'gray' => 'perdu',
                    ])
                    ->toggleable(),

                TextColumn::make('client.nom')
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
                        'NON_FOURNI' => 'Non fourni',
                        'FOURNI' => 'Fourni',
                        'VALIDE' => 'Validé',
                        'REJETE' => 'Rejeté',
                        default => $state ?? 'Non défini',
                    })
                    ->colors([
                        'danger' => 'NON_FOURNI',
                        'warning' => 'FOURNI',
                        'success' => 'VALIDE',
                        'danger' => 'REJETE',
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
                        'NON_FOURNI' => 'Non fourni',
                        'FOURNI' => 'Fourni',
                        'VALIDE' => 'Validé',
                        'REJETE' => 'Rejeté',
                        default => $state ?? 'Non défini',
                    })
                    ->colors([
                        'danger' => 'NON_FOURNI',
                        'warning' => 'FOURNI',
                        'success' => 'VALIDE',
                        'danger' => 'REJETE',
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
                        'NON_FOURNI' => 'Non fourni',
                        'FOURNI' => 'Fourni',
                        'VALIDE' => 'Validé',
                        'REJETE' => 'Rejeté',
                        default => $state ?? 'Non défini',
                    })
                    ->colors([
                        'danger' => 'NON_FOURNI',
                        'warning' => 'FOURNI',
                        'success' => 'VALIDE',
                        'danger' => 'REJETE',
                    ])
                    ->toggleable(),

                // Statut global de l'expertise
                TextColumn::make('etat_expertise')
                    ->label('État expertise')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'NON_COMMENCE' => 'Non commencé',
                        'EN_COURS' => 'En cours',
                        'EN_ATTENTE_DOCUMENTS' => 'En attente docs',
                        'EN_ATTENTE_VALIDATION' => 'En attente validation',
                        'TERMINE' => 'Terminé',
                        'ANNULE' => 'Annulé',
                        default => $state ?? 'Non défini',
                    })
                    ->colors([
                        'gray' => 'NON_COMMENCE',
                        'warning' => 'EN_COURS',
                        'warning' => 'EN_ATTENTE_DOCUMENTS',
                        'info' => 'EN_ATTENTE_VALIDATION',
                        'success' => 'TERMINE',
                        'danger' => 'ANNULE',
                    ])
                    ->icon(fn ($state) => match ($state) {
                        'NON_COMMENCE' => 'heroicon-o-x-circle',
                        'EN_COURS' => 'heroicon-o-arrow-path',
                        'EN_ATTENTE_DOCUMENTS' => 'heroicon-o-document',
                        'EN_ATTENTE_VALIDATION' => 'heroicon-o-clock',
                        'TERMINE' => 'heroicon-o-check-circle',
                        'ANNULE' => 'heroicon-o-no-symbol',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Statut final')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'EN_ATTENTE' => 'En attente',
                        'APPROUVE' => 'Approuvé',
                        'REJETE' => 'Rejeté',
                        'EN_ATTENTE_CORRECTIONS' => 'En attente corrections',
                        default => $state ?? 'Non défini',
                    })
                    ->colors([
                        'warning' => 'EN_ATTENTE',
                        'success' => 'APPROUVE',
                        'danger' => 'REJETE',
                        'info' => 'EN_ATTENTE_CORRECTIONS',
                    ])
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
                        $record->etat_pvc === 'VALIDE' && 
                        $record->etat_ae === 'VALIDE' && 
                        $record->etat_cmc === 'VALIDE'
                    )
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
                        'NON_COMMENCE' => 'Non commencé',
                        'EN_COURS' => 'En cours',
                        'EN_ATTENTE_DOCUMENTS' => 'En attente documents',
                        'EN_ATTENTE_VALIDATION' => 'En attente validation',
                        'TERMINE' => 'Terminé',
                        'ANNULE' => 'Annulé',
                    ])
                    ->multiple(),

                SelectFilter::make('status')
                    ->label('Statut final')
                    ->options([
                        'EN_ATTENTE' => 'En attente',
                        'APPROUVE' => 'Approuvé',
                        'REJETE' => 'Rejeté',
                        'EN_ATTENTE_CORRECTIONS' => 'En attente corrections',
                    ])
                    ->multiple(),

                Filter::make('documents_manquants')
                    ->label('Documents manquants')
                    ->query(fn (Builder $query): Builder => $query->where(function ($q) {
                        $q->whereNull('num_pvc')
                          ->orWhereNull('num_ae')
                          ->orWhereNull('num_cmc');
                    }))
                    ->toggle(),

                Filter::make('documents_incomplets')
                    ->label('Documents incomplets')
                    ->query(fn (Builder $query): Builder => $query->where(function ($q) {
                        $q->where('etat_pvc', '!=', 'VALIDE')
                          ->orWhere('etat_ae', '!=', 'VALIDE')
                          ->orWhere('etat_cmc', '!=', 'VALIDE');
                    }))
                    ->toggle(),

                SelectFilter::make('client_id')
                    ->label('Client')
                    ->relationship('client', 'nom')
                    ->searchable()
                    ->preload()
                    ->multiple(),
            ])
            ->actions([
                Action::make('voir')
                    ->label('Détails')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Colis $record): string => route('filament.admin.resources.colis.edit', $record))
                    ->color('info'),

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

                        // Log de l'action
                        activity()
                            ->performedOn($record)
                            ->causedBy(auth()->user())
                            ->withProperties(['expertise_data' => $data])
                            ->log('Mise à jour expertise');
                    })
                    ->form([
                        Grid::make(3)->schema([
                            // Section PVC
                            Grid::make(1)->schema([
                                TextInput::make('num_pvc')
                                    ->label('N° PVC')
                                    ->placeholder('Ex: PVC-2024-001')
                                    ->prefix('PVC')
                                    ->maxLength(50)
                                    ->columnSpanFull(),

                                Select::make('etat_pvc')
                                    ->label('État PVC')
                                    ->options([
                                        'NON_FOURNI' => 'Non fourni',
                                        'FOURNI' => 'Fourni',
                                        'VALIDE' => 'Validé',
                                        'REJETE' => 'Rejeté',
                                    ])
                                    ->native(false)
                                    ->columnSpanFull(),
                            ])->columnSpan(1),

                            // Section AE
                            Grid::make(1)->schema([
                                TextInput::make('num_ae')
                                    ->label('N° AE')
                                    ->placeholder('Ex: AE-2024-001')
                                    ->prefix('AE')
                                    ->maxLength(50)
                                    ->columnSpanFull(),

                                Select::make('etat_ae')
                                    ->label('État AE')
                                    ->options([
                                        'NON_FOURNI' => 'Non fourni',
                                        'FOURNI' => 'Fourni',
                                        'VALIDE' => 'Validé',
                                        'REJETE' => 'Rejeté',
                                    ])
                                    ->native(false)
                                    ->columnSpanFull(),
                            ])->columnSpan(1),

                            // Section CMC
                            Grid::make(1)->schema([
                                TextInput::make('num_cmc')
                                    ->label('N° CMC')
                                    ->placeholder('Ex: CMC-2024-001')
                                    ->prefix('CMC')
                                    ->maxLength(50)
                                    ->columnSpanFull(),

                                Select::make('etat_cmc')
                                    ->label('État CMC')
                                    ->options([
                                        'NON_FOURNI' => 'Non fourni',
                                        'FOURNI' => 'Fourni',
                                        'VALIDE' => 'Validé',
                                        'REJETE' => 'Rejeté',
                                    ])
                                    ->native(false)
                                    ->columnSpanFull(),
                            ])->columnSpan(1),
                        ]),

                        Grid::make(2)->schema([
                            Select::make('etat_expertise')
                                ->label('État de l\'expertise')
                                ->options([
                                    'NON_COMMENCE' => 'Non commencé',
                                    'EN_COURS' => 'En cours',
                                    'EN_ATTENTE_DOCUMENTS' => 'En attente documents',
                                    'EN_ATTENTE_VALIDATION' => 'En attente validation',
                                    'TERMINE' => 'Terminé',
                                    'ANNULE' => 'Annulé',
                                ])
                                ->required()
                                ->native(false),

                            Select::make('status')
                                ->label('Statut final')
                                ->options([
                                    'EN_ATTENTE' => 'En attente',
                                    'APPROUVE' => 'Approuvé',
                                    'REJETE' => 'Rejeté',
                                    'EN_ATTENTE_CORRECTIONS' => 'En attente corrections',
                                ])
                                ->native(false),
                        ]),

                        Textarea::make('commentaire_expertise')
                            ->label('Commentaire')
                            ->placeholder('Ajouter des observations sur l\'expertise...')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->modalHeading('Gestion de l\'expertise')
                    ->modalButton('Enregistrer')
                    ->modalWidth('4xl'),

                Action::make('valider_document')
                    ->label('Valider')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (Colis $record): bool => 
                        $record->num_pvc || $record->num_ae || $record->num_cmc
                    )
                    ->action(function (Colis $record) {
                        if ($record->num_pvc) $record->update(['etat_pvc' => 'VALIDE']);
                        if ($record->num_ae) $record->update(['etat_ae' => 'VALIDE']);
                        if ($record->num_cmc) $record->update(['etat_cmc' => 'VALIDE']);
                        
                        // Vérifier si tous les documents sont validés
                        if ($record->etat_pvc === 'VALIDE' && 
                            $record->etat_ae === 'VALIDE' && 
                            $record->etat_cmc === 'VALIDE') {
                            $record->update(['etat_expertise' => 'TERMINE']);
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Valider les documents')
                    ->modalDescription('Êtes-vous sûr de vouloir valider tous les documents ?')
                    ->modalSubmitActionLabel('Oui, valider'),

                Action::make('documents')
                    ->label('Documents')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->url(fn (Colis $record): string => route('filament.admin.resources.documents.index', [
                        'colis_id' => $record->id,
                        'type' => 'expertise'
                    ]))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('valider_documents')
                        ->label('Valider documents')
                        ->icon('heroicon-o-check-circle')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                if ($record->num_pvc) $record->update(['etat_pvc' => 'VALIDE']);
                                if ($record->num_ae) $record->update(['etat_ae' => 'VALIDE']);
                                if ($record->num_cmc) $record->update(['etat_cmc' => 'VALIDE']);
                            }
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('marquer_termine')
                        ->label('Marquer terminé')
                        ->icon('heroicon-o-check-badge')
                        ->action(fn ($records) => $records->each->update(['etat_expertise' => 'TERMINE']))
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s');
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) Colis::query()
            ->where(function ($query) {
                $query->where('etat_expertise', 'EN_COURS')
                      ->orWhere('etat_expertise', 'EN_ATTENTE_DOCUMENTS')
                      ->orWhere('etat_expertise', 'EN_ATTENTE_VALIDATION');
            })
            ->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = Colis::query()
            ->where('etat_expertise', 'EN_COURS')
            ->count();

        return match (true) {
            $count > 20 => 'danger',
            $count > 10 => 'warning',
            default => 'success',
        };
    }
}