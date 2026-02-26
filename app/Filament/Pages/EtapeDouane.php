<?php

namespace App\Filament\Pages;

use App\Models\Colis;
use App\Filament\Traits\HasExports;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;
use Illuminate\Database\Eloquent\Builder;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Schemas\Components\Tabs\Tab;
use UnitEnum;
use Filament\Notifications\Notification;

class EtapeDouane extends Page implements HasTable
{
    use InteractsWithTable, HasExports;

    protected static ?string $navigationLabel = 'Douane';
    protected static ?string $title = 'Gestion des Colis - Étape Douane';
    protected static ?string $slug = 'etape-douane';
    protected static string | UnitEnum | null $navigationGroup = 'Colis';
    protected static ?int $navigationSort = 5;
    protected string $view = 'filament.pages.etape-douane';

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
                    $query->where('status_colis_douane', 'EN_ATTENTE')
                ),

            'entre' => Tab::make('Entrés')
                ->icon('heroicon-o-arrow-left-circle')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('status_colis_douane', 'ENTRE')
                ),

            'sorti' => Tab::make('Sortis')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('status_colis_douane', 'SORTI')
                ),
        ];
    }

    /**
     * Configuration de la table
     */
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Colis::query()
                    ->with(['typeColis', 'dossierTransit.client', 'agent', 'port'])
                    ->where(function ($query) {
                        $query->whereNotNull('num_t1')
                              ->orWhereNotNull('declaration_reference')
                              ->orWhereNotNull('status_colis_douane');
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
                    ->description(fn ($record) => $record->description),

                TextColumn::make('typeColis.nom')
                    ->label('Type')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('dossierTransit.client.nom')
                    ->label('Client')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('agent.name')
                    ->label('Agent')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('port.nom')
                    ->label('Port')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                // Colonnes spécifiques à la douane
                TextColumn::make('num_t1')
                    ->label('N° T1')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-document-text')
                    ->color('gray')
                    ->badge()
                    ->toggleable(),

                TextColumn::make('etat_t1')
                    ->label('État T1')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'FOURNI' => 'Fourni',
                        'PAYE' => 'Payé',
                        default => $state ?? 'Non défini',
                    })
                    ->colors([
                        'warning' => 'FOURNI',
                        'success' => 'PAYE',
                    ])
                    ->toggleable(),

                TextColumn::make('declaration_reference')
                    ->label('Déclaration')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-document')
                    ->limit(15)
                    ->tooltip(fn ($record) => $record->declaration_reference)
                    ->toggleable(),

                TextColumn::make('status_colis_douane')
                    ->label('Statut Douane')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'EN_ATTENTE' => 'En attente',
                        'ENTRE' => 'Entré',
                        'SORTI' => 'Sorti',
                        default => $state ?? 'Non défini',
                    })
                    ->colors([
                        'warning' => 'EN_ATTENTE',
                        'primary' => 'ENTRE',
                        'success' => 'SORTI',
                    ])
                    ->icon(fn ($state) => match ($state) {
                        'EN_ATTENTE' => 'heroicon-o-clock',
                        'ENTRE' => 'heroicon-o-arrow-left-circle',
                        'SORTI' => 'heroicon-o-check-circle',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->toggleable(),

                TextColumn::make('date_entree_douane')
                    ->label('Entrée douane')
                    ->date('d/m/Y')
                    ->sortable()
                    ->icon('heroicon-o-arrow-left-circle')
                    ->color('info')
                    ->toggleable(),

                TextColumn::make('date_sortie_douane')
                    ->label('Sortie douane')
                    ->date('d/m/Y')
                    ->sortable()
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('success')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status_colis_douane')
                    ->label('Statut Douane')
                    ->options([
                        'EN_ATTENTE' => 'En attente',
                        'ENTRE' => 'Entré',
                        'SORTI' => 'Sorti',
                    ])
                    ->multiple(),

                SelectFilter::make('etat_t1')
                    ->label('État T1')
                    ->options([
                        'FOURNI' => 'Fourni',
                        'PAYE' => 'Payé',
                    ])
                    ->multiple(),

                Filter::make('date_entree_douane')
                    ->form([
                        Grid::make(2)->schema([
                            DatePicker::make('entree_from')
                                ->label('Entrée du')
                                ->native(false)
                                ->displayFormat('d/m/Y'),
                            DatePicker::make('entree_until')
                                ->label('Entrée au')
                                ->native(false)
                                ->displayFormat('d/m/Y'),
                        ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['entree_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date_entree_douane', '>=', $date),
                            )
                            ->when(
                                $data['entree_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date_entree_douane', '<=', $date),
                            );
                    }),

                Filter::make('date_sortie_douane')
                    ->form([
                        Grid::make(2)->schema([
                            DatePicker::make('sortie_from')
                                ->label('Sortie du')
                                ->native(false)
                                ->displayFormat('d/m/Y'),
                            DatePicker::make('sortie_until')
                                ->label('Sortie au')
                                ->native(false)
                                ->displayFormat('d/m/Y'),
                        ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['sortie_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date_sortie_douane', '>=', $date),
                            )
                            ->when(
                                $data['sortie_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date_sortie_douane', '<=', $date),
                            );
                    }),

                SelectFilter::make('client_id')
                    ->label('Client')
                    ->relationship('dossierTransit.client', 'nom')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Filter::make('has_t1')
                    ->label('Avec T1')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('num_t1'))
                    ->toggle(),

                Filter::make('has_declaration')
                    ->label('Avec déclaration')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('declaration_reference'))
                    ->toggle(),
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                // Actions individuelles groupées
                ActionGroup::make([

                    // Détails complets
                    Action::make('voir')
                        ->label('Détails complets')
                        ->icon('heroicon-o-eye')
                        ->url(fn (Colis $record): string => 
                            \App\Filament\Resources\Colis\ColisResource::getUrl('view', ['record' => $record])
                        )
                        ->color('info'),

                    // ✅ IMPRESSION FICHE DOUANE (via trait)
                    $this->getPrintAction('pdf.etape-douane', 'Imprimer fiche douane'),

                    // Mise à jour des informations douanières
                    Action::make('mettre_a_jour_douane')
                        ->label('Mise à jour')
                        ->icon('heroicon-o-pencil-square')
                        ->color('warning')
                        ->action(function (Colis $record, array $data) {
                            $record->update([
                                'num_t1' => $data['num_t1'] ?? $record->num_t1,
                                'etat_t1' => $data['etat_t1'] ?? $record->etat_t1,
                                'declaration_reference' => $data['declaration_reference'] ?? $record->declaration_reference,
                                'status_colis_douane' => $data['status_colis_douane'] ?? $record->status_colis_douane,
                                'date_entree_douane' => $data['date_entree_douane'] ?? $record->date_entree_douane,
                                'date_sortie_douane' => $data['date_sortie_douane'] ?? $record->date_sortie_douane,
                            ]);

                            Notification::make()
                                ->title('Mise à jour effectuée')
                                ->success()
                                ->send();
                        })
                        ->form([
                            Grid::make(2)->schema([
                                TextInput::make('num_t1')
                                    ->label('N° T1')
                                    ->placeholder('Ex: T1-2024-001')
                                    ->prefix('T1')
                                    ->maxLength(50),

                                Select::make('etat_t1')
                                    ->label('État T1')
                                    ->options([
                                        'FOURNI' => 'Fourni',
                                        'PAYE' => 'Payé',
                                    ]),

                                TextInput::make('declaration_reference')
                                    ->label('Référence déclaration')
                                    ->placeholder('Ex: DEC-2024-001234')
                                    ->prefix('DEC')
                                    ->columnSpanFull(),

                                Select::make('status_colis_douane')
                                    ->label('Statut douane')
                                    ->options([
                                        'EN_ATTENTE' => 'En attente',
                                        'ENTRE' => 'Entré',
                                        'SORTI' => 'Sorti',
                                    ])
                                    ->required()
                                    ->columnSpanFull(),

                                DatePicker::make('date_entree_douane')
                                    ->label('Date entrée')
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->closeOnDateSelection(),

                                DatePicker::make('date_sortie_douane')
                                    ->label('Date sortie')
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->closeOnDateSelection()
                                    ->afterOrEqual('date_entree_douane'),
                            ]),
                        ])
                        ->modalHeading('Mise à jour des informations douanières')
                        ->modalButton('Enregistrer')
                        ->modalWidth('xl'),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('valider_t1')
                        ->label('Valider T1')
                        ->icon('heroicon-o-check')
                        ->action(fn ($records) => $records->each->update(['etat_t1' => 'PAYE']))
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('export_csv_bulk')
                        ->label('Exporter sélection (CSV)')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->action(function ($records) {
                            $headers = [
                                'Content-Type' => 'text/csv',
                                'Content-Disposition' => 'attachment; filename="Colis-selection-' . now()->format('Y-m-d') . '.csv"',
                            ];

                            $callback = function() use ($records) {
                                $file = fopen('php://output', 'w');
                                
                                // En-têtes
                                fputcsv($file, ['N° BL', 'Client', 'Type', 'Statut', 'N° T1', 'Déclaration', 'Entrée', 'Sortie']);
                                
                                foreach ($records as $record) {
                                    fputcsv($file, [
                                        $record->numero_bl,
                                        $record->dossierTransit?->client?->nom ?? 'N/A',
                                        $record->typeColis?->nom ?? 'N/A',
                                        $record->status_colis_douane ?? 'N/A',
                                        $record->num_t1 ?? 'N/A',
                                        $record->declaration_reference ?? 'N/A',
                                        $record->date_entree_douane ? \Carbon\Carbon::parse($record->date_entree_douane)->format('d/m/Y') : 'N/A',
                                        $record->date_sortie_douane ? \Carbon\Carbon::parse($record->date_sortie_douane)->format('d/m/Y') : 'N/A',
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

    /**
     * Badge de navigation
     */
    public static function getNavigationBadge(): ?string
    {
        return (string) Colis::query()
            ->where(function ($query) {
                $query->where('status_colis_douane', 'EN_ATTENTE')
                      ->orWhere('status_colis_douane', 'ENTRE');
            })
            ->count();
    }

    /**
     * Libellé de navigation
     */
    public static function getNavigationLabel(): string
    {
        return '2 - Etape Douane';
    }

    /**
     * Couleur du badge de navigation
     */
    public static function getNavigationBadgeColor(): ?string
    {
        $count = Colis::query()
            ->where(function ($query) {
                $query->where('status_colis_douane', 'EN_ATTENTE')
                      ->orWhere('status_colis_douane', 'ENTRE');
            })
            ->count();

        return match (true) {
            $count > 20 => 'danger',
            $count > 10 => 'warning',
            default => 'success',
        };
    }
}