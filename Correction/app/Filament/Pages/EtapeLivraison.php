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

class EtapeLivraison extends Page implements HasTable
{
    use InteractsWithTable, HasExports;

    protected static ?string $navigationLabel = 'Livraison';
    protected static ?string $title = 'Gestion des Colis - Étape Livraison';
    protected static ?string $slug = 'etape-livraison';
    protected static string | UnitEnum | null $navigationGroup = 'Colis';
    protected static ?int $navigationSort = 7;
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
            'all' => Tab::make('Tous les colis')
                ->icon('heroicon-o-rectangle-stack'),

            'en_attente' => Tab::make('En attente')
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('status_colis_livraison', 'EN_ATTENTE')
                ),

            'livre' => Tab::make('Livrés')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('status_colis_livraison', 'LIVRE')
                ),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Colis::query()
                    ->with(['typeColis', 'dossierTransit.client', 'agent', 'port'])
                    ->whereNotNull('status_colis_livraison')
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

                TextColumn::make('dossierTransit.client.nom')
                    ->label('Client')
                    ->sortable()
                    ->searchable()
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

                // Informations de livraison
                TextColumn::make('status_colis_livraison')
                    ->label('Statut livraison')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'EN_ATTENTE' => 'En attente',
                        'LIVRE' => 'Livré',
                        default => $state ?? 'Non défini',
                    })
                    ->colors([
                        'warning' => 'EN_ATTENTE',
                        'success' => 'LIVRE',
                    ])
                    ->icon(fn ($state) => match ($state) {
                        'EN_ATTENTE' => 'heroicon-o-clock',
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
                    ->toggleable(),

                TextColumn::make('commentaires_cloture')
                    ->label('Commentaires')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->commentaires_cloture)
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->toggleable(),

                // Indicateurs de complétion
                IconColumn::make('livraison_complete')
                    ->label('Complété')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->getStateUsing(fn ($record): bool => 
                        $record->status_colis_livraison === 'LIVRE' && $record->date_livraison
                    )
                    ->toggleable(),

                // Délai de livraison
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
                    ->color(fn ($state) => 
                        $state !== 'N/A' && (int) filter_var($state, FILTER_SANITIZE_NUMBER_INT) > 7 
                            ? 'danger' 
                            : 'success'
                    )
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status_colis_livraison')
                    ->label('Statut livraison')
                    ->options([
                        'EN_ATTENTE' => 'En attente',
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

                Filter::make('en_retard')
                    ->label('En retard (>7 jours)')
                    ->query(fn (Builder $query): Builder => 
                        $query->whereNotNull('date_livraison')
                              ->whereRaw('DATEDIFF(date_livraison, created_at) > 7')
                    )
                    ->toggle(),

                SelectFilter::make('client_id')
                    ->label('Client')
                    ->relationship('dossierTransit.client', 'nom')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Filter::make('avec_commentaires')
                    ->label('Avec commentaires')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('commentaires_cloture'))
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

                    // ✅ IMPRESSION BON DE LIVRAISON (via trait)
                    $this->getPrintAction('pdf.etape-livraison', 'Imprimer bon de livraison'),

                    // Gérer livraison
                    Action::make('gerer_livraison')
                        ->label('Gérer livraison')
                        ->icon('heroicon-o-truck')
                        ->color('primary')
                        ->action(function (Colis $record, array $data) {
                            $updateData = [
                                'status_colis_livraison' => $data['status_colis_livraison'],
                                'date_livraison' => $data['date_livraison'] ?? null,
                                'commentaires_cloture' => $data['commentaires_cloture'] ?? null,
                            ];

                            if ($data['status_colis_livraison'] === 'LIVRE') {
                                $updateData['etat_colis'] = 'LIVRE';
                            }

                            $record->update($updateData);

                            Notification::make()
                                ->title('Livraison mise à jour')
                                ->success()
                                ->send();
                        })
                        ->form([
                            Grid::make(2)->schema([
                                Select::make('status_colis_livraison')
                                    ->label('Statut livraison')
                                    ->options([
                                        'EN_ATTENTE' => 'En préparation',
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
                                    ->required(fn ($get) => $get('status_colis_livraison') === 'LIVRE'),

                                Textarea::make('commentaires_cloture')
                                    ->label('Commentaires')
                                    ->placeholder('Ajouter des commentaires sur la livraison...')
                                    ->rows(3)
                                    ->columnSpanFull(),
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
                        ->visible(fn (Colis $record): bool => 
                            $record->status_colis_livraison !== 'LIVRE'
                        )
                        ->action(function (Colis $record) {
                            $record->update([
                                'status_colis_livraison' => 'LIVRE',
                                'date_livraison' => now(),
                                'etat_colis' => 'LIVRE',
                            ]);

                            Notification::make()
                                ->title('Colis marqué comme livré')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Confirmer la livraison')
                        ->modalDescription('Êtes-vous sûr de vouloir marquer ce colis comme livré ?')
                        ->modalSubmitActionLabel('Oui, marquer livré'),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('marquer_livres')
                        ->label('Marquer livrés')
                        ->icon('heroicon-o-check-circle')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update([
                                    'status_colis_livraison' => 'LIVRE',
                                    'date_livraison' => now(),
                                    'etat_colis' => 'LIVRE',
                                ]);
                            }

                            Notification::make()
                                ->title('Opération effectuée')
                                ->body(count($records) . ' colis marqués comme livrés')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('exporter_csv_bulk')
                        ->label('Exporter sélection (CSV)')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->action(function ($records) {
                            $headers = [
                                'Content-Type' => 'text/csv',
                                'Content-Disposition' => 'attachment; filename="Livraison-selection-' . now()->format('Y-m-d') . '.csv"',
                            ];

                            $callback = function() use ($records) {
                                $file = fopen('php://output', 'w');
                                
                                fputcsv($file, ['N° BL', 'Client', 'Type', 'Statut', 'Date livraison', 'Commentaires']);
                                
                                foreach ($records as $record) {
                                    fputcsv($file, [
                                        $record->numero_bl,
                                        $record->dossierTransit?->client?->nom ?? 'N/A',
                                        $record->typeColis?->nom ?? 'N/A',
                                        $record->status_colis_livraison ?? 'N/A',
                                        $record->date_livraison ? Carbon::parse($record->date_livraison)->format('d/m/Y H:i') : 'N/A',
                                        $record->commentaires_cloture ?? 'N/A',
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

    public static function getNavigationBadge(): ?string
    {
        return (string) Colis::query()
            ->where('status_colis_livraison', 'EN_ATTENTE')
            ->count();
    }

    public static function getNavigationLabel(): string
    {
        return '5 - Etape Livraison';
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = Colis::query()
            ->where('status_colis_livraison', 'EN_ATTENTE')
            ->count();

        return match (true) {
            $count > 10 => 'danger',
            $count > 5 => 'warning',
            default => 'success',
        };
    }
}