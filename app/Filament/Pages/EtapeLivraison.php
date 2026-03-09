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
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\BulkAction;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Notifications\Notification;
use Carbon\Carbon;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Illuminate\Support\Collection;

class EtapeLivraison extends Page implements HasTable
{
    use InteractsWithTable, HasExports, HasPageShield;

    protected static ?string $navigationLabel = 'Livraison';
    protected static ?string $title = 'Gestion des Unités - Étape Livraison';
    protected static ?string $slug = 'etape-livraison';
    protected static string | UnitEnum | null $navigationGroup = 'Colis / BL';
    protected static ?int $navigationSort = 8;
    protected string $view = 'filament.pages.etape-livraison';

    public static function canAccess(): bool
    {
        return auth()->user()->can('View:EtapeLivraison');
    }

    /**
     * Définition des onglets de filtrage
     */
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Toutes les unités')
                ->icon('heroicon-o-rectangle-stack'),

            'au_port' => Tab::make('Au port')
                // ->icon('heroicon-o-anchor')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('etat', 'AU_PORT')
                ),

            'a_la_douane' => Tab::make('En douane')
                ->icon('heroicon-o-building-library')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('etat', 'A_LA_DOUANE')
                ),

            'expertise' => Tab::make('Expertise')
                ->icon('heroicon-o-clipboard-document-check')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('etat', 'EXPERTISE')
                ),

            'en_route' => Tab::make('En route')
                ->icon('heroicon-o-truck')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('etat', 'EN_ROUTE')
                ),

            'livre' => Tab::make('Livrées')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('etat', 'LIVRE')
                ),

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
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ColisUnite::query()
                    ->with(['colis.typeColis', 'colis.dossierTransit.client', 'colis.agent', 'colis.port'])
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

                // Informations de livraison - utilisant la colonne 'etat'
                TextColumn::make('etat')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'AU_PORT' => 'Au port',
                        'A_LA_DOUANE' => 'En douane',
                        'EXPERTISE' => 'Expertise',
                        'EN_ROUTE' => 'En route',
                        'LIVRE' => 'Livré',
                        default => 'Non défini',
                    })
                    ->colors([
                        'primary' => 'AU_PORT',
                        'purple' => 'A_LA_DOUANE',
                        'orange' => 'EXPERTISE',
                        'warning' => 'EN_ROUTE',
                        'success' => 'LIVRE',
                    ])
                    ->icon(fn ($state) => match ($state) {
                        // 'AU_PORT' => 'heroicon-o-anchor',
                        'A_LA_DOUANE' => 'heroicon-o-building-library',
                        'EXPERTISE' => 'heroicon-o-clipboard-document-check',
                        'EN_ROUTE' => 'heroicon-o-truck',
                        'LIVRE' => 'heroicon-o-check-circle',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->toggleable(),

                TextColumn::make('date_livraison')
                    ->label('Date livraison')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->icon('heroicon-o-calendar')
                    ->color('success')
                    ->placeholder('—')
                    ->toggleable(),

                // Délai de livraison (depuis la création de l'unité)
                TextColumn::make('delai_livraison')
                    ->label('Délai')
                    ->getStateUsing(function ($record) {
                        if (!$record->created_at || !$record->date_livraison) {
                            return 'N/A';
                        }

                        $created = $record->created_at instanceof Carbon
                            ? $record->created_at
                            : Carbon::parse($record->created_at);

                        $livree = $record->date_livraison instanceof Carbon
                            ? $record->date_livraison
                            : Carbon::parse($record->date_livraison);

                        $jours = (int) $created->diffInDays($livree);
                        return $jours . ' jour' . ($jours > 1 ? 's' : '');
                    })
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state === 'N/A' => 'gray',
                        (int) filter_var($state, FILTER_SANITIZE_NUMBER_INT) > 7 => 'danger',
                        (int) filter_var($state, FILTER_SANITIZE_NUMBER_INT) > 3 => 'warning',
                        default => 'success',
                    })
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

                SelectFilter::make('etat')
                    ->label('Statut')
                    ->options([
                        'AU_PORT' => 'Au port',
                        'A_LA_DOUANE' => 'En douane',
                        'EXPERTISE' => 'Expertise',
                        'EN_ROUTE' => 'En route',
                        'LIVRE' => 'Livré',
                    ])
                    ->multiple(),

                Filter::make('date_livraison')
                    ->form([
                        Grid::make(2)->schema([
                            DatePicker::make('livraison_from')
                                ->label('Du')
                                ->native(false)
                                ->displayFormat('d/m/Y'),
                            DatePicker::make('livraison_until')
                                ->label('Au')
                                ->native(false)
                                ->displayFormat('d/m/Y'),
                        ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['livraison_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date_livraison', '>=', $date),
                            )
                            ->when(
                                $data['livraison_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date_livraison', '<=', $date),
                            );
                    }),

                Filter::make('avec_date_livraison')
                    ->label('Avec date de livraison')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('date_livraison'))
                    ->toggle(),

                Filter::make('sans_date')
                    ->label('Sans date de livraison')
                    ->query(fn (Builder $query): Builder =>
                        $query->whereNull('date_livraison')
                              ->where('etat', '!=', 'LIVRE')
                    )
                    ->toggle(),

                SelectFilter::make('client_id')
                    ->label('Client')
                    ->relationship('colis.dossierTransit.client', 'nom')
                    ->searchable()
                    ->preload()
                    ->multiple(),
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                ActionGroup::make([

                    // Détails complets du colis parent
                    Action::make('voir_colis')
                        ->label('Voir le BL')
                        ->icon('heroicon-o-document-text')
                        ->url(fn (ColisUnite $record): string =>
                            \App\Filament\Resources\Colis\ColisResource::getUrl('view', ['record' => $record->colis])
                        )
                        ->color('info')
                        ->openUrlInNewTab(),

                    // Gérer livraison
                    Action::make('gerer_livraison')
                        ->label('Gérer livraison')
                        ->icon('heroicon-o-truck')
                        ->color('primary')
                        ->action(function (ColisUnite $record, array $data) {
                            $updateData = [
                                'etat' => $data['etat'],
                                'date_livraison' => $data['date_livraison'] ?? null,
                            ];

                            $record->update($updateData);

                            Notification::make()
                                ->title('Livraison mise à jour')
                                ->success()
                                ->send();
                        })
                        ->form([
                            Grid::make(2)->schema([
                                Select::make('etat')
                                    ->label('Statut')
                                    ->options([
                                        'AU_PORT' => 'Au port',
                                        'A_LA_DOUANE' => 'En douane',
                                        'EXPERTISE' => 'Expertise',
                                        'EN_ROUTE' => 'En route',
                                        'LIVRE' => 'Livré',
                                    ])
                                    ->required()
                                    ->native(false)
                                    ->live()
                                    ->afterStateUpdated(fn ($set, $state) =>
                                        $state === 'LIVRE' ? $set('date_livraison', now()) : null
                                    ),

                                DatePicker::make('date_livraison')
                                    ->label('Date livraison')
                                    ->native(false)
                                    ->displayFormat('d/m/Y H:i')
                                    ->seconds(false)
                                    ->required(fn ($get) => $get('etat') === 'LIVRE'),
                            ]),
                        ])
                        ->modalHeading('Gestion de la livraison')
                        ->modalButton('Enregistrer')
                        ->modalWidth('lg'),

                    // Marquer livré
                    Action::make('marquer_livre')
                        ->label('Marquer livré')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn (ColisUnite $record): bool =>
                            $record->etat !== 'LIVRE'
                        )
                        ->action(function (ColisUnite $record) {
                            $record->update([
                                'etat' => 'LIVRE',
                                'date_livraison' => now(),
                            ]);

                            Notification::make()
                                ->title('Unité marquée comme livrée')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Confirmer la livraison')
                        ->modalDescription('Êtes-vous sûr de vouloir marquer cette unité comme livrée ?')
                        ->modalSubmitActionLabel('Oui, marquer livré'),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('marquer_livres')
                        ->label('Marquer livrées')
                        ->icon('heroicon-o-check-circle')
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                $record->update([
                                    'etat' => 'LIVRE',
                                    'date_livraison' => now(),
                                ]);
                            }

                            Notification::make()
                                ->title('Opération effectuée')
                                ->body(count($records) . ' unité(s) marquée(s) comme livrée(s)')
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
                                'Content-Disposition' => 'attachment; filename="livraison-selection-' . now()->format('Y-m-d') . '.csv"',
                            ];

                            $callback = function() use ($records) {
                                $file = fopen('php://output', 'w');

                                fputcsv($file, [
                                    'N° BL',
                                    'Type',
                                    'N° Conteneur',
                                    'N° Châssis',
                                    'VIN',
                                    'Client',
                                    'Statut',
                                    'Date livraison'
                                ]);

                                foreach ($records as $record) {
                                    fputcsv($file, [
                                        $record->colis?->numero_bl ?? 'N/A',
                                        $record->type ?? 'N/A',
                                        $record->numero_conteneur ?? 'N/A',
                                        $record->numero_chassis ?? 'N/A',
                                        $record->vin ?? 'N/A',
                                        $record->colis?->dossierTransit?->client?->nom ?? 'N/A',
                                        match($record->etat) {
                                            'AU_PORT' => 'Au port',
                                            'A_LA_DOUANE' => 'En douane',
                                            'EXPERTISE' => 'Expertise',
                                            'EN_ROUTE' => 'En route',
                                            'LIVRE' => 'Livré',
                                            default => 'Non défini',
                                        },
                                        $record->date_livraison ? Carbon::parse($record->date_livraison)->format('d/m/Y H:i') : 'N/A',
                                    ]);
                                }

                                fclose($file);
                            };

                            return response()->stream($callback, 200, $headers);
                        }),
                ]),
            ])
            ->defaultSort('date_livraison', 'desc')
            ->poll('30s');
    }

    public static function getNavigationLabel(): string
    {
        return '5 - Étape Livraison';
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) ColisUnite::query()
            ->whereIn('etat', ['AU_PORT', 'A_LA_DOUANE', 'EXPERTISE', 'EN_ROUTE'])
            ->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = ColisUnite::query()
            ->whereIn('etat', ['AU_PORT', 'A_LA_DOUANE', 'EXPERTISE', 'EN_ROUTE'])
            ->count();

        return match (true) {
            $count > 50 => 'danger',
            $count > 20 => 'warning',
            $count > 0 => 'info',
            default => 'success',
        };
    }
}
