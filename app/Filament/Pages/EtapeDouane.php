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

class EtapeDouane extends Page implements HasTable
{
    use InteractsWithTable, HasExports, HasPageShield;

    protected static ?string $navigationLabel = 'Douane';
    protected static ?string $title = 'Gestion des Unités - Étape Douane';
    protected static ?string $slug = 'etape-douane';
    protected static string | UnitEnum | null $navigationGroup = 'Colis / BL';
    protected static ?int $navigationSort = 6;
    protected string $view = 'filament.pages.etape-douane';

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
                    $query->where('type', 'CHASSIS')
                ),

            'chassis_voiture' => Tab::make('Châssis Voitures')
                ->icon('heroicon-o-truck')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('type', 'CHASSIS_VOITURE')
                ),

            'chassis_machine' => Tab::make('Châssis Machines')
                ->icon('heroicon-o-truck')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('type', 'CHASSIS_MACHINE')
                ),

            'en_attente' => Tab::make('En attente')
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('status_douane', 'EN_ATTENTE')
                ),

            'entre' => Tab::make('Entrés')
                ->icon('heroicon-o-arrow-left-circle')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('status_douane', 'ENTRE')
                ),

            'sorti' => Tab::make('Sortis')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('status_douane', 'SORTI')
                ),

            't1_fourni' => Tab::make('T1 Fourni')
                ->icon('heroicon-o-document-plus')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('etat_t1', 'FOURNI')
                ),

            't1_paye' => Tab::make('T1 Payé')
                ->icon('heroicon-o-check-badge')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('etat_t1', 'PAYE')
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
                    ->with(['colis.typeColis', 'colis.dossierTransit.client', 'colis.agent', 'colis.port'])
                    ->where(function ($query) {
                        $query->whereNotNull('num_t1')
                              ->orWhereNotNull('declaration_reference')
                              ->orWhereNotNull('status_douane');
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
                    ->openUrlInNewTab(),

                TextColumn::make('colis.typeColis.nom')
                    ->label('Type BL')
                    ->sortable()
                    ->searchable()
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
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('numero_conteneur')
                    ->label('N° Conteneur')
                    ->placeholder('—')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),

                TextColumn::make('numero_chassis')
                    ->label('N° Châssis')
                    ->placeholder('—')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),

                TextColumn::make('vin')
                    ->label('VIN')
                    ->placeholder('—')
                    ->copyable()
                    ->limit(10)
                    ->toggleable(),

                // Client
                TextColumn::make('colis.dossierTransit.client.nom')
                    ->label('Client')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                // Informations douanières
                TextColumn::make('num_t1')
                    ->label('N° T1')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-document-text')
                    ->badge()
                    ->color('info')
                    ->toggleable(),

                TextColumn::make('etat_t1')
                    ->label('État T1')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'FOURNI' => 'Fourni',
                        'PAYE' => 'Payé',
                        default => 'Non défini',
                    })
                    ->colors([
                        'warning' => 'FOURNI',
                        'success' => 'PAYE',
                        'gray' => null,
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

                TextColumn::make('status_douane')
                    ->label('Statut Douane')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'EN_ATTENTE' => 'En attente',
                        'ENTRE' => 'Entré',
                        'SORTI' => 'Sorti',
                        'BLOQUE' => 'Bloqué',
                        default => 'Non défini',
                    })
                    ->colors([
                        'warning' => 'EN_ATTENTE',
                        'primary' => 'ENTRE',
                        'success' => 'SORTI',
                        'danger' => 'BLOQUE',
                        'gray' => null,
                    ])
                    ->icon(fn ($state) => match ($state) {
                        'EN_ATTENTE' => 'heroicon-o-clock',
                        'ENTRE' => 'heroicon-o-arrow-left-circle',
                        'SORTI' => 'heroicon-o-check-circle',
                        'BLOQUE' => 'heroicon-o-exclamation-triangle',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->toggleable(),

                TextColumn::make('date_entree_douane')
                    ->label('Entrée douane')
                    ->date('d/m/Y')
                    ->sortable()
                    ->icon('heroicon-o-arrow-left-circle')
                    ->color('info')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('date_sortie_douane')
                    ->label('Sortie douane')
                    ->date('d/m/Y')
                    ->sortable()
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('success')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('colis.port.nom')
                    ->label('Port')
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
                SelectFilter::make('type')
                    ->label("Type d'unité")
                    ->options([
                        'CONTENEUR' => 'Conteneur',
                        'CHASSIS' => 'Châssis',
                        'CHASSIS_VOITURE' => 'Châssis voiture',
                        'CHASSIS_MACHINE' => 'Châssis machine',
                    ])
                    ->multiple(),

                SelectFilter::make('status_douane')
                    ->label('Statut Douane')
                    ->options([
                        'EN_ATTENTE' => 'En attente',
                        'ENTRE' => 'Entré',
                        'SORTI' => 'Sorti',
                        'BLOQUE' => 'Bloqué',
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

                Filter::make('has_t1')
                    ->label('Avec T1')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('num_t1'))
                    ->toggle(),

                Filter::make('sans_t1')
                    ->label('Sans T1')
                    ->query(fn (Builder $query): Builder => $query->whereNull('num_t1'))
                    ->toggle(),

                Filter::make('has_declaration')
                    ->label('Avec déclaration')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('declaration_reference'))
                    ->toggle(),
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

                    Action::make('mettre_a_jour_douane')
                        ->label('Mise à jour')
                        ->icon('heroicon-o-pencil-square')
                        ->color('warning')
                        ->action(function (ColisUnite $record, array $data) {
                            $record->update([
                                'num_t1' => $data['num_t1'] ?? $record->num_t1,
                                'etat_t1' => $data['etat_t1'] ?? $record->etat_t1,
                                'declaration_reference' => $data['declaration_reference'] ?? $record->declaration_reference,
                                'status_douane' => $data['status_douane'] ?? $record->status_douane,
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
                                    ->columnSpanFull(),

                                Select::make('status_douane')
                                    ->label('Statut douane')
                                    ->options([
                                        'EN_ATTENTE' => 'En attente',
                                        'ENTRE' => 'Entré',
                                        'SORTI' => 'Sorti',
                                        'BLOQUE' => 'Bloqué',
                                    ])
                                    ->columnSpanFull(),

                                DatePicker::make('date_entree_douane')
                                    ->label('Date entrée')
                                    ->native(false)
                                    ->displayFormat('d/m/Y'),

                                DatePicker::make('date_sortie_douane')
                                    ->label('Date sortie')
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
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
                        ->label('Marquer T1 comme payé')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn (Collection $records) => $records->each->update(['etat_t1' => 'PAYE']))
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('fournir_t1')
                        ->label('Marquer T1 comme fourni')
                        ->icon('heroicon-o-document-plus')
                        ->color('warning')
                        ->action(fn (Collection $records) => $records->each->update(['etat_t1' => 'FOURNI']))
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('exporter_csv')
                        ->label('Exporter sélection (CSV)')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->action(function (Collection $records) {
                            $headers = [
                                'Content-Type' => 'text/csv',
                                'Content-Disposition' => 'attachment; filename="unites-douane-' . now()->format('Y-m-d') . '.csv"',
                            ];

                            $callback = function() use ($records) {
                                $file = fopen('php://output', 'w');

                                fputcsv($file, ['N° BL', 'Type', 'N° Conteneur', 'N° Châssis', 'VIN', 'Client', 'N° T1', 'État T1', 'Déclaration', 'Statut', 'Entrée', 'Sortie']);

                                foreach ($records as $record) {
                                    fputcsv($file, [
                                        $record->colis?->numero_bl ?? 'N/A',
                                        $record->type ?? 'N/A',
                                        $record->numero_conteneur ?? 'N/A',
                                        $record->numero_chassis ?? 'N/A',
                                        $record->vin ?? 'N/A',
                                        $record->colis?->dossierTransit?->client?->nom ?? 'N/A',
                                        $record->num_t1 ?? 'N/A',
                                        match($record->etat_t1) {
                                            'FOURNI' => 'Fourni',
                                            'PAYE' => 'Payé',
                                            default => 'Non défini',
                                        },
                                        $record->declaration_reference ?? 'N/A',
                                        match($record->status_douane) {
                                            'EN_ATTENTE' => 'En attente',
                                            'ENTRE' => 'Entré',
                                            'SORTI' => 'Sorti',
                                            'BLOQUE' => 'Bloqué',
                                            default => 'Non défini',
                                        },
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
        return (string) ColisUnite::query()
            ->where(function ($query) {
                $query->where('status_douane', 'EN_ATTENTE')
                      ->orWhere('status_douane', 'ENTRE');
            })
            ->count();
    }

    /**
     * Libellé de navigation
     */
    public static function getNavigationLabel(): string
    {
        return '2 - Étape Douane';
    }

    /**
     * Couleur du badge de navigation
     */
    public static function getNavigationBadgeColor(): ?string
    {
        $count = ColisUnite::query()
            ->where(function ($query) {
                $query->where('status_douane', 'EN_ATTENTE')
                      ->orWhere('status_douane', 'ENTRE');
            })
            ->count();

        return match (true) {
            $count > 50 => 'danger',
            $count > 20 => 'warning',
            $count > 0 => 'info',
            default => 'success',
        };
    }
}
