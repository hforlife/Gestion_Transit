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
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Notifications\Notification;
use Carbon\Carbon;

class EtapePort extends Page implements HasTable
{
    use InteractsWithTable, HasExports;

    protected static ?string $navigationLabel = 'Port';
    protected static ?string $title = 'Gestion des Colis - Étape Port';
    protected static ?string $slug = 'etape-port';
    protected static string | UnitEnum | null $navigationGroup = 'Colis';
    protected static ?int $navigationSort = 4;
    protected string $view = 'filament.pages.etape-port';

    public static function canAccess(): bool
    {
        return auth()->user()->can('View:EtapePort');
    }

    /**
     * Définition des onglets de filtrage
     */
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tous les colis')
                ->icon('heroicon-o-rectangle-stack'),

            'en_attente' => Tab::make('En attente')
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('status_colis_port', 'EN_ATTENTE')
                ),

            'entre' => Tab::make('Entrés')
                ->icon('heroicon-o-arrow-left-circle')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('status_colis_port', 'ENTRE')
                ),

            'sorti' => Tab::make('Sortis')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('status_colis_port', 'SORTI')
                ),

            'sans_dates' => Tab::make('Sans dates')
                ->icon('heroicon-o-calendar')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where(function ($q) {
                        $q->whereNull('date_entree_port')
                          ->orWhereNull('date_sortie_port');
                    })
                ),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Colis::query()
                    ->with(['typeColis', 'dossierTransit.client', 'agent', 'port'])
                    ->whereHas('port')
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
                    ->badge()
                    ->color(fn ($record) => 
                        str_contains(strtolower($record->typeColis?->nom ?? ''), 'chassis') 
                            ? 'warning' 
                            : 'primary'
                    )
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('dossierTransit.client.nom')
                    ->label('Client')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('port.nom')
                    ->label('Port')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                // Colonnes spécifiques au port
                TextColumn::make('status_colis_port')
                    ->label('Statut Port')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'EN_ATTENTE' => 'En attente',
                        'ENTRE' => 'Entré',
                        'SORTI' => 'Sorti',
                        default => $state ?? 'Non défini',
                    })
                    ->colors([
                        'warning' => 'EN_ATTENTE',
                        'info' => 'ENTRE',
                        'success' => 'SORTI',
                    ])
                    ->icon(fn ($state) => match ($state) {
                        'EN_ATTENTE' => 'heroicon-o-clock',
                        'ENTRE' => 'heroicon-o-arrow-left-circle',
                        'SORTI' => 'heroicon-o-check-circle',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->toggleable(),

                TextColumn::make('date_entree_port')
                    ->label('Entrée port')
                    ->date('d/m/Y')
                    ->sortable()
                    ->icon('heroicon-o-arrow-left-circle')
                    ->color('info')
                    ->placeholder('Non renseignée')
                    ->toggleable(),

                TextColumn::make('date_sortie_port')
                    ->label('Sortie port')
                    ->date('d/m/Y')
                    ->sortable()
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('success')
                    ->placeholder('Non renseignée')
                    ->toggleable(),

                // Durée de séjour au port
                TextColumn::make('duree_sejour')
                    ->label('Durée séjour')
                    ->getStateUsing(function ($record) {
                        if (!$record->date_entree_port) {
                            return null;
                        }
                        
                        $dateEntree = $record->date_entree_port instanceof Carbon 
                            ? $record->date_entree_port 
                            : Carbon::parse($record->date_entree_port);
                        
                        $dateSortie = $record->date_sortie_port 
                            ? ($record->date_sortie_port instanceof Carbon 
                                ? $record->date_sortie_port 
                                : Carbon::parse($record->date_sortie_port))
                            : now();
                        
                        $jours = (int) $dateEntree->diffInDays($dateSortie);
                        
                        if ($jours === 0) {
                            return '< 1 jour';
                        }
                        
                        return $jours . ' jour' . ($jours > 1 ? 's' : '');
                    })
                    ->badge()
                    ->color(function ($state) {
                        if (!$state) return 'gray';
                        
                        if (str_contains($state, '<')) return 'warning';
                        
                        $jours = (int) filter_var($state, FILTER_SANITIZE_NUMBER_INT);
                        return $jours > 7 ? 'danger' : ($jours > 3 ? 'warning' : 'success');
                    })
                    ->toggleable(),

                TextColumn::make('agent.name')
                    ->label('Agent')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('port_id')
                    ->label('Port')
                    ->relationship('port', 'nom')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                SelectFilter::make('status_colis_port')
                    ->label('Statut au port')
                    ->options([
                        'EN_ATTENTE' => 'En attente',
                        'ENTRE' => 'Entré',
                        'SORTI' => 'Sorti',
                    ])
                    ->multiple(),

                SelectFilter::make('client_id')
                    ->label('Client')
                    ->relationship('dossierTransit.client', 'nom')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Filter::make('date_entree_port')
                    ->form([
                        \Filament\Schemas\Components\Grid::make(2)->schema([
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
                                fn (Builder $query, $date): Builder => $query->whereDate('date_entree_port', '>=', $date),
                            )
                            ->when(
                                $data['entree_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date_entree_port', '<=', $date),
                            );
                    }),

                Filter::make('date_sortie_port')
                    ->form([
                        \Filament\Schemas\Components\Grid::make(2)->schema([
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
                                fn (Builder $query, $date): Builder => $query->whereDate('date_sortie_port', '>=', $date),
                            )
                            ->when(
                                $data['sortie_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date_sortie_port', '<=', $date),
                            );
                    }),

                Filter::make('sans_date_entree')
                    ->label('Sans date entrée')
                    ->query(fn (Builder $query): Builder => $query->whereNull('date_entree_port'))
                    ->toggle(),

                Filter::make('sans_date_sortie')
                    ->label('Sans date sortie')
                    ->query(fn (Builder $query): Builder => $query->whereNull('date_sortie_port'))
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

                    // ✅ IMPRESSION FICHE PORT (via trait)
                    $this->getPrintAction('pdf.etape-port', 'Imprimer fiche port'),

                    // Mise à jour port
                    Action::make('mettre_a_jour_port')
                        ->label('Mettre à jour')
                        ->icon('heroicon-o-pencil')
                        ->color('warning')
                        ->action(function (Colis $record, array $data) {
                            $updateData = [
                                'status_colis_port' => $data['status_colis_port'],
                            ];

                            if (isset($data['date_entree_port'])) {
                                $updateData['date_entree_port'] = $data['date_entree_port'];
                            }

                            if (isset($data['date_sortie_port'])) {
                                $updateData['date_sortie_port'] = $data['date_sortie_port'];
                            }

                            if ($data['status_colis_port'] === 'SORTI') {
                                $updateData['etat_colis'] = 'A_LA_DOUANE';
                            }

                            $record->update($updateData);

                            Notification::make()
                                ->title('Mise à jour effectuée')
                                ->success()
                                ->send();
                        })
                        ->form([
                            \Filament\Schemas\Components\Grid::make(2)->schema([
                                \Filament\Forms\Components\Select::make('status_colis_port')
                                    ->label('Statut au port')
                                    ->options([
                                        'EN_ATTENTE' => 'En attente',
                                        'ENTRE' => 'Entré',
                                        'SORTI' => 'Sorti',
                                    ])
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state === 'ENTRE') {
                                            $set('date_entree_port', now());
                                        }
                                    }),

                                DatePicker::make('date_entree_port')
                                    ->label('Date entrée')
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->required(fn ($get) => $get('status_colis_port') === 'ENTRE' || $get('status_colis_port') === 'SORTI'),

                                DatePicker::make('date_sortie_port')
                                    ->label('Date sortie')
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->afterOrEqual('date_entree_port')
                                    ->required(fn ($get) => $get('status_colis_port') === 'SORTI'),
                            ]),
                        ])
                        ->modalHeading('Mettre à jour les informations port')
                        ->modalButton('Enregistrer')
                        ->modalWidth('lg'),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('marquer_entree')
                        ->label('Marquer entrée')
                        ->icon('heroicon-o-arrow-left-circle')
                        ->color('info')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update([
                                    'status_colis_port' => 'ENTRE',
                                    'date_entree_port' => now(),
                                ]);
                            }

                            Notification::make()
                                ->title('Opération effectuée')
                                ->body(count($records) . ' colis marqués comme entrés au port')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('marquer_sortie')
                        ->label('Marquer sortie')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update([
                                    'status_colis_port' => 'SORTI',
                                    'date_sortie_port' => now(),
                                    'etat_colis' => 'A_LA_DOUANE',
                                ]);
                            }

                            Notification::make()
                                ->title('Opération effectuée')
                                ->body(count($records) . ' colis marqués comme sortis du port')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('export_csv_bulk')
                        ->label('Exporter sélection (CSV)')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->action(function ($records) {
                            $headers = [
                                'Content-Type' => 'text/csv',
                                'Content-Disposition' => 'attachment; filename="Port-selection-' . now()->format('Y-m-d') . '.csv"',
                            ];

                            $callback = function() use ($records) {
                                $file = fopen('php://output', 'w');
                                
                                fputcsv($file, ['N° BL', 'Client', 'Type', 'Port', 'Statut', 'Entrée', 'Sortie', 'Durée']);
                                
                                foreach ($records as $record) {
                                    $dateEntree = $record->date_entree_port ? Carbon::parse($record->date_entree_port) : null;
                                    $dateSortie = $record->date_sortie_port ? Carbon::parse($record->date_sortie_port) : null;
                                    
                                    $duree = $dateEntree && $dateSortie 
                                        ? $dateEntree->diffInDays($dateSortie) . ' jours'
                                        : ($dateEntree ? 'En cours' : 'N/A');
                                    
                                    fputcsv($file, [
                                        $record->numero_bl,
                                        $record->dossierTransit?->client?->nom ?? 'N/A',
                                        $record->typeColis?->nom ?? 'N/A',
                                        $record->port?->nom ?? 'N/A',
                                        $record->status_colis_port ?? 'N/A',
                                        $dateEntree?->format('d/m/Y') ?? 'N/A',
                                        $dateSortie?->format('d/m/Y') ?? 'N/A',
                                        $duree,
                                    ]);
                                }
                                
                                fclose($file);
                            };

                            return response()->stream($callback, 200, $headers);
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('60s');
    }

    public static function getNavigationLabel(): string
    {
        return '1 - Etape Port';
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) Colis::query()
            ->whereHas('port')
            ->where('status_colis_port', '!=', 'SORTI')
            ->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}