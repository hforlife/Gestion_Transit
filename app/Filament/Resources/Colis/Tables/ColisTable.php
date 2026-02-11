<?php

namespace App\Filament\Resources\Colis\Tables;

use App\Filament\Resources\Colis\ColisResource;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Text;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class ColisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // Informations de base
                TextColumn::make('numero_bl')
                    ->label('N° BL')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),
                
                TextColumn::make('description')
                    ->label('Description')
                    ->limit(30)
                    ->tooltip(fn($record) => $record->description)
                    ->searchable(),
                
                // TextColumn::make('etat_colis')
                //     ->label('Statut global')
                //     ->badge()
                //     ->colors([
                //         'secondary' => 'BL_ENREGISTRE',
                //         'info' => 'AU_PORT',
                //         'warning' => 'A_LA_DOUANE',
                //         'primary' => 'EN_ROUTE',
                //         'success' => 'LIVRE',
                //         'danger' => 'CLOTURE',
                //     ])
                //     ->sortable(),
                
                TextColumn::make('typeColis.nom')
                    ->label('Type')
                    ->badge()
                    ->color(fn($record) => $record->typeColis?->nom === 'Chassis' ? 'warning' : 'primary')
                    ->sortable()
                    ->searchable(),
                
                // Étape PORT
                TextColumn::make('status_colis_port')
                    ->label('Port')
                    ->badge()
                    ->colors([
                        'secondary' => 'EN_ATTENTE',
                        'warning' => 'ENTRE',
                        'success' => 'SORTI',
                    ])
                    ->toggleable(),
                
                TextColumn::make('date_entree_port')
                    ->label('Arrivée port')
                    ->date('d/m/Y')
                    ->toggleable()
                    ->sortable(),
                
                // Étape DOUANE
                TextColumn::make('status_colis_douane')
                    ->label('Douane')
                    ->badge()
                    ->colors([
                        'secondary' => 'EN_ATTENTE',
                        'warning' => 'ENTRE',
                        'success' => 'SORTI',
                    ])
                    ->toggleable(),
                
                TextColumn::make('num_t1')
                    ->label('N° T1')
                    ->copyable()
                    ->toggleable()
                    ->badge()
                    ->color(fn($state) => $state ? 'success' : 'secondary'),
                
                TextColumn::make('etat_t1')
                    ->label('État T1')
                    ->badge()
                    ->colors([
                        'secondary' => 'FOURNI',
                        'success' => 'PAYE',
                    ])
                    ->toggleable(isToggledHiddenByDefault: true),
                
                // Étape EXPERTISE (spécifique Chassis)
                TextColumn::make('etat_expertise')
                    ->label('Expertise ONT')
                    ->badge()
                    ->colors([
                        'danger' => 'EN_ATTENTE',
                        'success' => 'EFFECTUEE',
                    ])
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('num_pvc')
                    ->label('PVC')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->badge()
                    ->color(fn($state) => $state ? 'info' : 'secondary'),
                
                TextColumn::make('etat_pvc')
                    ->label('État PVC')
                    ->badge()
                    ->colors([
                        'secondary' => 'NON_RECU',
                        'warning' => 'RECU',
                        'success' => 'PAYE',
                    ])
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('etat_ae')
                    ->label('AE')
                    ->badge()
                    ->colors([
                        'danger' => 'NON_VALIDE',
                        'success' => 'VALIDE',
                    ])
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('num_cmc')
                    ->label('CMC')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('etat_cmc')
                    ->label('État CMC')
                    ->badge()
                    ->colors([
                        'secondary' => 'NON_RECU',
                        'success' => 'RECU',
                    ])
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('status')
                    ->label('Clôture')
                    ->badge()
                    ->colors([
                        'warning' => 'EN_COURS',
                        'success' => 'TERMINE',
                    ])
                    ->toggleable(),
                
                // Relations
                TextColumn::make('client.nom')
                    ->label('Client')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                
                TextColumn::make('port.nom')
                    ->label('Port')
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('agent.name')
                    ->label('Agent')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                // Dates système
                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('updated_at')
                    ->label('Mis à jour')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Filtre par statut global
                // SelectFilter::make('etat_colis')
                //     ->label('Statut')
                //     ->options([
                //         'BL_ENREGISTRE' => 'BL enregistré',
                //         'AU_PORT' => 'Au port',
                //         'A_LA_DOUANE' => 'À la douane',
                //         'EN_ROUTE' => 'En route',
                //         'LIVRE' => 'Livré',
                //         'CLOTURE' => 'Clôturé',
                //     ]),
                
                // Filtre par type de colis
                SelectFilter::make('id_type_colis')
                    ->label('Type de colis')
                    ->relationship('typeColis', 'nom'),
                
                // Filtre par client
                SelectFilter::make('client_id')
                    ->label('Client')
                    ->relationship('client', 'nom')
                    ->searchable(),
                
                // Filtre par port
                SelectFilter::make('id_port')
                    ->label('Port')
                    ->relationship('port', 'nom'),
                
                // Filtre par état d'expertise (utile pour les chassis)
                SelectFilter::make('etat_expertise')
                    ->label('Expertise ONT')
                    ->options([
                        'EN_ATTENTE' => 'En attente',
                        'EFFECTUEE' => 'Effectuée',
                    ]),
                
                // Filtre par statut douane
                SelectFilter::make('status_colis_douane')
                    ->label('Statut douane')
                    ->options([
                        'EN_ATTENTE' => 'En attente',
                        'ENTRE' => 'Entré',
                        'SORTI' => 'Sorti',
                    ]),
                
                // Filtre personnalisé pour les chassis avec PVC payé mais AE non validé
                Filter::make('expertise_en_cours')
                    ->label('Expertise en cours')
                    ->query(fn (Builder $query) => $query
                        ->where('etat_pvc', 'PAYE')
                        ->where('etat_ae', 'NON_VALIDE')
                        ->orWhere('etat_ae', 'VALIDE')
                        ->where('etat_cmc', 'NON_RECU'))
                    ->toggle(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                // Action::make('tracking')
                //     ->label('Suivi')
                //     ->icon('heroicon-o-clock')
                //     ->url(fn($record) => ColisResource::getUrl('tracking', ['record' => $record]))
                //     ->openUrlInNewTab()
                //     ->color('info'),
                
                EditAction::make()
                    ->visible(fn($record) => $record->etat_colis !== 'CLOTURE'),
                
                ViewAction::make()
                    ->label('Détails'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn() => auth()->user()?->hasRole('admin')),
                ]),
            ])
            ->poll('10s');
    }
}