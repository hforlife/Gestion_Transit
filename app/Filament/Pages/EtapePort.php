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
use Filament\Tables\Enums\FiltersLayout;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Schemas\Components\Tabs\Tab;
use UnitEnum;
use Filament\Notifications\Notification;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Illuminate\Support\Collection;

class EtapePort extends Page implements HasTable
{
    use InteractsWithTable, HasExports, HasPageShield;

    protected static ?string $navigationLabel = 'Port';
    protected static ?string $title = 'Gestion des Unités - Étape Port';
    protected static ?string $slug = 'etape-port';
    protected static string | UnitEnum | null $navigationGroup = 'Colis / BL';
    protected static ?int $navigationSort = 5;
    protected string $view = 'filament.pages.etape-port';

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
                    $query->where('status_port', 'EN_ATTENTE')
                ),

            'au_port' => Tab::make('Au port')  // Changé de 'entre' à 'au_port'
                ->icon('heroicon-o-archive-box')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('status_port', 'AU_PORT')
                ),

            'sorti' => Tab::make('Sortis')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('status_port', 'SORTI')
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
                ColisUnite::query()
                    ->with(['colis.typeColis', 'colis.dossierTransit.client', 'colis.port'])
                    ->whereNotNull('colis_id')
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
                    ->url(fn ($record) => $record->colis ?
                        \App\Filament\Resources\Colis\ColisResource::getUrl('view', ['record' => $record->colis]) : null)
                    ->openUrlInNewTab(),

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
                    ->sortable()
                    ->searchable(),

                TextColumn::make('numero_conteneur')
                    ->label('N° Conteneur')
                    ->placeholder('—')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('numero_chassis')
                    ->label('N° Châssis')
                    ->placeholder('—')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('vin')
                    ->label('VIN')
                    ->placeholder('—')
                    ->copyable()
                    ->limit(10),

                // Client
                TextColumn::make('colis.dossierTransit.client.nom')
                    ->label('Client')
                    ->sortable()
                    ->searchable(),

                // Port
                TextColumn::make('colis.port.nom')
                    ->label('Port')
                    ->sortable()
                    ->searchable(),

                // Informations portuaires - CORRIGÉ
                TextColumn::make('status_port')
                    ->label('Statut Port')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'EN_ATTENTE' => 'En attente',
                        'AU_PORT' => 'Au port',
                        'SORTI' => 'Sorti',
                        default => 'Non défini',
                    })
                    ->colors([
                        'warning' => 'EN_ATTENTE',
                        'primary' => 'AU_PORT',  // Changé de 'ENTRE' à 'AU_PORT'
                        'success' => 'SORTI',
                        'gray' => null,
                    ])
                    ->icon(fn ($state) => match ($state) {
                        'EN_ATTENTE' => 'heroicon-o-clock',
                        'AU_PORT' => 'heroicon-o-archive-box',  // Changé de 'arrow-left-circle' à 'archive-box'
                        'SORTI' => 'heroicon-o-check-circle',
                        default => 'heroicon-o-question-mark-circle',
                    }),

                TextColumn::make('date_entree_port')
                    ->label('Entrée port')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('date_sortie_port')
                    ->label('Sortie port')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('—'),

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

                SelectFilter::make('status_port')
                    ->label('Statut Port')
                    ->options([
                        'EN_ATTENTE' => 'En attente',
                        'AU_PORT' => 'Au port',
                        'SORTI' => 'Sorti',
                    ])
                    ->multiple(),

                SelectFilter::make('port_id')
                    ->label('Port')
                    ->relationship('colis.port', 'nom')
                    ->searchable()
                    ->preload(),

                Filter::make('date_entree_port')
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
                                fn (Builder $query, $date): Builder => $query->whereDate('date_entree_port', '>=', $date),
                            )
                            ->when(
                                $data['entree_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date_entree_port', '<=', $date),
                            );
                    }),

                Filter::make('date_sortie_port')
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
                                fn (Builder $query, $date): Builder => $query->whereDate('date_sortie_port', '>=', $date),
                            )
                            ->when(
                                $data['sortie_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date_sortie_port', '<=', $date),
                            );
                    }),
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                ActionGroup::make([
                    Action::make('voir_colis')
                        ->label('Voir le BL')
                        ->icon('heroicon-o-document-text')
                        ->url(fn (ColisUnite $record): string =>
                            \App\Filament\Resources\Colis\ColisResource::getUrl('view', ['record' => $record->colis])
                        )
                        ->color('info')
                        ->openUrlInNewTab(),

                    Action::make('mettre_a_jour_port')
                        ->label('Mise à jour')
                        ->icon('heroicon-o-pencil-square')
                        ->color('warning')
                        ->action(function (ColisUnite $record, array $data) {
                            // Debug - à retirer après test
                            \Log::info('Mise à jour port', [
                                'id' => $record->id,
                                'ancien_status' => $record->status_port,
                                'nouveau_status' => $data['status_port'] ?? null,
                                'data' => $data
                            ]);

                            $record->update([
                                'status_port' => $data['status_port'],
                                'date_entree_port' => $data['date_entree_port'] ?? $record->date_entree_port,
                                'date_sortie_port' => $data['date_sortie_port'] ?? $record->date_sortie_port,
                            ]);

                            // Recharger le modèle pour vérifier
                            $record->refresh();

                            \Log::info('Après mise à jour', [
                                'id' => $record->id,
                                'nouveau_status' => $record->status_port
                            ]);

                            Notification::make()
                                ->title('Mise à jour effectuée')
                                ->body("Statut changé à : " . match($record->status_port) {
                                    'EN_ATTENTE' => 'En attente',
                                    'AU_PORT' => 'Au port',
                                    'SORTI' => 'Sorti',
                                    default => $record->status_port
                                })
                                ->success()
                                ->send();
                        })
                        ->form([
                            Grid::make(2)->schema([
                                Select::make('status_port')
                                    ->label('Statut port')
                                    ->options([
                                        'EN_ATTENTE' => 'En attente',
                                        'AU_PORT' => 'Au port',
                                        'SORTI' => 'Sorti',
                                    ])
                                    ->required()
                                    ->native(false)
                                    ->live()
                                    ->afterStateUpdated(function ($set, $state) {
                                        if ($state === 'AU_PORT') {
                                            $set('date_entree_port', now()->format('Y-m-d'));
                                        } elseif ($state === 'SORTI') {
                                            $set('date_sortie_port', now()->format('Y-m-d'));
                                        }
                                    }),

                                DatePicker::make('date_entree_port')
                                    ->label('Date entrée')
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->required(fn ($get) => $get('status_port') === 'AU_PORT'),

                                DatePicker::make('date_sortie_port')
                                    ->label('Date sortie')
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->afterOrEqual('date_entree_port')
                                    ->required(fn ($get) => $get('status_port') === 'SORTI'),
                            ]),
                        ])
                        ->modalHeading('Mise à jour des informations portuaires')
                        ->modalButton('Enregistrer')
                        ->modalWidth('lg'),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('marquer_entre')
                        ->label('Marquer comme entré')
                        ->icon('heroicon-o-archive-box')
                        ->color('primary')
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                $record->update([
                                    'status_port' => 'AU_PORT',
                                    'date_entree_port' => now(),
                                ]);
                            }

                            Notification::make()
                                ->title('Opération effectuée')
                                ->body(count($records) . ' unité(s) marquée(s) comme entrée(s) au port')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('marquer_sorti')
                        ->label('Marquer comme sorti')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                $record->update([
                                    'status_port' => 'SORTI',
                                    'date_sortie_port' => now(),
                                ]);
                            }

                            Notification::make()
                                ->title('Opération effectuée')
                                ->body(count($records) . ' unité(s) marquée(s) comme sortie(s) du port')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('exporter_csv')
                        ->label('Exporter sélection (CSV)')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->action(function (Collection $records) {
                            $headers = [
                                'Content-Type' => 'text/csv',
                                'Content-Disposition' => 'attachment; filename="unites-port-' . now()->format('Y-m-d') . '.csv"',
                            ];

                            $callback = function() use ($records) {
                                $file = fopen('php://output', 'w');

                                fputcsv($file, ['N° BL', 'Type', 'N° Conteneur', 'N° Châssis', 'VIN', 'Client', 'Port', 'Statut', 'Entrée', 'Sortie']);

                                foreach ($records as $record) {
                                    fputcsv($file, [
                                        $record->colis?->numero_bl ?? 'N/A',
                                        $record->type ?? 'N/A',
                                        $record->numero_conteneur ?? 'N/A',
                                        $record->numero_chassis ?? 'N/A',
                                        $record->vin ?? 'N/A',
                                        $record->colis?->dossierTransit?->client?->nom ?? 'N/A',
                                        $record->colis?->port?->nom ?? 'N/A',
                                        match($record->status_port) {
                                            'EN_ATTENTE' => 'En attente',
                                            'AU_PORT' => 'Au port',
                                            'SORTI' => 'Sorti',
                                            default => 'Non défini',
                                        },
                                        $record->date_entree_port ? \Carbon\Carbon::parse($record->date_entree_port)->format('d/m/Y') : 'N/A',
                                        $record->date_sortie_port ? \Carbon\Carbon::parse($record->date_sortie_port)->format('d/m/Y') : 'N/A',
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
        return (string) ColisUnite::query()
            ->where('status_port', 'EN_ATTENTE')
            ->count();
    }

    /**
     * Libellé de navigation
     */
    public static function getNavigationLabel(): string
    {
        return '1 - Étape Port';
    }

    /**
     * Couleur du badge de navigation
     */
    public static function getNavigationBadgeColor(): ?string
    {
        $count = ColisUnite::query()
            ->where('status_port', 'EN_ATTENTE')
            ->count();

        return match (true) {
            $count > 50 => 'danger',
            $count > 20 => 'warning',
            $count > 0 => 'info',
            default => 'success',
        };
    }
}
