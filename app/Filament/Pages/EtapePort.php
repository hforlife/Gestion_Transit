<?php

namespace App\Filament\Pages;

use App\Models\Colis;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use BackedEnum;
use UnitEnum;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class EtapePort extends Page implements HasTable
{
    use InteractsWithTable;

    // static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBox;
    
    protected static ?string $navigationLabel = 'Port';
    
    protected static ?string $title = 'Gestion des Colis - Étape Port';
    
    protected static ?string $slug = 'etape-port';
    
    protected static string | UnitEnum | null $navigationGroup = 'Colis';
    
    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.etape-port';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Colis::query()
                    ->with(['typeColis', 'client', 'agent', 'port'])
                    ->whereHas('port') // Uniquement les colis avec un port assigné
            )
            ->columns([
                // Informations principales du colis
                TextColumn::make('numero_bl')
                    ->label('N° BL')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold')
                    ->color('primary'),

                TextColumn::make('description')
                    ->label('Description')
                    ->searchable()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->description),

                TextColumn::make('typeColis.nom')
                    ->label('Type')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('client.nom')
                    ->label('Client')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('agent.name')
                    ->label('Agent')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('port.nom')
                    ->label('Port')
                    ->sortable()
                    ->searchable(),

                // Colonnes spécifiques au port
                TextColumn::make('date_entree_port')
                    ->label('Date entrée port')
                    ->date('d/m/Y')
                    ->sortable()
                    ->icon('heroicon-o-arrow-left-circle')
                    ->color('info')
                    ->toggleable(),

                TextColumn::make('date_sortie_port')
                    ->label('Date sortie port')
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
                SelectFilter::make('port')
                    ->relationship('port', 'nom')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('status_colis_port')
                    ->label('Statut au port')
                    ->options([
                        'EN_ATTENTE' => 'En attente',
                        'ENTRE' => 'Entrée',
                        'SORTI' => 'Sorti',
                    ]),

                SelectFilter::make('client_id')
                    ->label('Client')
                    ->relationship('client', 'nom')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Action::make('voir')
                    ->label('Voir détails')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Colis $record): string => \App\Filament\Resources\Colis\ColisResource::getUrl('view', ['record' => $record]))
                    ->color('info'),

                Action::make('mettre_a_jour_port')
                    ->label('Mettre à jour')
                    ->icon('heroicon-o-pencil')
                    ->color('warning')
                    ->action(function (Colis $record, array $data) {
                        $record->update([
                            'status_colis_port' => $data['status_colis_port'],
                            'date_entree_port' => $data['date_entree_port'] ?? $record->date_entree_port,
                            'date_sortie_port' => $data['date_sortie_port'] ?? $record->date_sortie_port,
                        ]);
                    })
                    ->form([
                        \Filament\Forms\Components\Select::make('status_colis_port')
                            ->label('Statut au port')
                            ->options([
                                'EN_ATTENTE' => 'En attente',
                                'ENTRE' => 'Entrée',
                                'SORTI' => 'Sorti',
                            ])
                            ->required(),
                        DatePicker::make('date_entree_port')
                            ->label('Date entrée')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        DatePicker::make('date_sortie_port')
                            ->label('Date sortie')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->afterOrEqual('date_entree_port'),
                    ])
                    ->modalHeading('Mettre à jour les informations port')
                    ->modalButton('Enregistrer')
                    ->modalWidth('lg'),

            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('60s'); // Rafraîchissement automatique toutes les 60 secondes
    }

    public static function getNavigationLabel(): string
    {
        return '1 - Etape Port';
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) Colis::query()
            ->whereHas('port')
            ->whereNull('date_sortie_port')
            ->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}