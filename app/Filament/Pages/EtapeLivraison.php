<?php

namespace App\Filament\Pages;

use App\Models\Colis;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\Builder;
use BackedEnum;
use UnitEnum;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\BulkAction;

class EtapeLivraison extends Page implements HasTable
{
    use InteractsWithTable;

    // protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-truck';
    
    protected static ?string $navigationLabel = 'Livraison';
    
    protected static ?string $title = 'Gestion des Colis - Étape Livraison';
    
    protected static ?string $slug = 'etape-livraison';
    
    protected static string | UnitEnum | null $navigationGroup = 'Colis';
    
    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.pages.etape-livraison';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Colis::query()
                    ->with(['typeColis', 'client', 'agent', 'port'])
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

                TextColumn::make('client.nom')
                    ->label('Client')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                // Informations de livraison
                BadgeColumn::make('status_colis_livraison')
                    ->label('Statut livraison')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'EN_ATTENTE' => 'En attente',
                        'LIVRE' => 'Livré',
                        default => $state ?? 'Non défini',
                    })
                    ->colors([
                        'gray' => 'EN_ATTENTE',
                        'success' => 'LIVRE',
                    ])
                    ->icon(fn ($state) => match ($state) {
                        'EN_ATTENTE' => 'heroicon-o-cube',
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
                // TextColumn::make('delai_livraison')
                //     ->label('Délai')
                //     ->formatStateUsing(function ($record) {
                //         if (!$record->created_at || !$record->date_livraison) {
                //             return 'N/A';
                //         }
                        
                //         $jours = $record->created_at->diffInDays($record->date_livraison);
                //         return $jours . ' jour' . ($jours > 1 ? 's' : '');
                //     })
                //     ->badge()
                //     ->color(fn ($record) => 
                //         $record->created_at && $record->date_livraison
                //             ? ($record->created_at->diffInDays($record->date_livraison) > 7 ? 'danger' : 'success')
                //             : 'gray'
                //     )
                //     ->toggleable(),

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
                    ->relationship('client', 'nom')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Filter::make('avec_commentaires')
                    ->label('Avec commentaires')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('commentaires_cloture'))
                    ->toggle(),
            ])
            ->actions([
                Action::make('voir')
                    ->label('Détails')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Colis $record): string => \App\Filament\Resources\Colis\ColisResource::getUrl('view', ['record' => $record]))
                    ->color('info'),

                Action::make('gerer_livraison')
                    ->label('Gérer livraison')
                    ->icon('heroicon-o-truck')
                    ->color('primary')
                    ->action(function (Colis $record, array $data) {
                        $record->update([
                            'status_colis_livraison' => $data['status_colis_livraison'],
                            'date_livraison' => $data['date_livraison'],
                            'commentaires_cloture' => $data['commentaires_cloture'],
                        ]);

                        // Si livré, mettre à jour l'état du colis
                        if ($data['status_colis_livraison'] === 'LIVRE') {
                            $record->update(['etat_colis' => 'livre']);
                        }

                        // Log de l'action
                        activity()
                            ->performedOn($record)
                            ->causedBy(auth()->user())
                            ->withProperties(['livraison_data' => $data])
                            ->log('Mise à jour livraison');
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
                                ->reactive()
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
                            'etat_colis' => 'livre',
                        ]);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Confirmer la livraison')
                    ->modalDescription('Êtes-vous sûr de vouloir marquer ce colis comme livré ?')
                    ->modalSubmitActionLabel('Oui, marquer livré'),

                // Action::make('signature')
                //     ->label('Signature')
                //     ->icon('heroicon-o-pencil')
                //     ->color('gray')
                //     ->visible(fn (Colis $record): bool => $record->status_colis_livraison === 'LIVRE')
                //     ->action(function (Colis $record, array $data) {
                //         // Logique pour enregistrer la signature
                //         $record->update(['signature' => $data['signature']]);
                //     })
                //     ->form([
                //         Textarea::make('signature')
                //             ->label('Signature du destinataire')
                //             ->placeholder('Nom et signature du destinataire...')
                //             ->required()
                //             ->rows(3),
                //     ])
                //     ->modalHeading('Signature de livraison')
                //     ->modalButton('Enregistrer'),

                // Action::make('documents')
                //     ->label('Documents')
                //     ->icon('heroicon-o-document-duplicate')
                //     ->color('gray')
                //     ->url(fn (Colis $record): string => route('filament.admin.resources.documents.index', [
                //         'colis_id' => $record->id,
                //         'type' => 'livraison'
                //     ]))
                //     ->openUrlInNewTab(),

                // Action::make('bon_livraison')
                //     ->label('Bon de livraison')
                //     ->icon('heroicon-o-document-arrow-down')
                //     ->color('gray')
                //     ->action(function (Colis $record) {
                //         // Générer PDF du bon de livraison
                //         return redirect()->route('colis.bon-livraison', $record);
                //     }),
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
                                    'etat_colis' => 'livre',
                                ]);
                            }
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('exporter_livraisons')
                        ->label('Exporter')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function ($records) {
                            // Logique d'export CSV des livraisons
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
        return '4 - Etape Livraison';
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