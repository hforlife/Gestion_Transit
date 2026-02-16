<?php

namespace App\Filament\Resources\Colis\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class ColisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->with(['typeColis', 'client', 'port', 'agent'])
            )

            ->columns([

                /* ===============================
                 |  INFOS PRINCIPALES
                 ===============================*/

                TextColumn::make('numero_bl')
                    ->label('N° BL')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->description)
                    ->limit(20),

                TextColumn::make('typeColis.nom')
                    ->label('Type')
                    ->badge()
                    ->color(fn ($state) =>
                        $state === 'Chassis' ? 'warning' : 'primary'
                    )
                    ->sortable(),

                TextColumn::make('client.nom')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('port.nom')
                    ->label('Port')
                    ->sortable()
                    ->toggleable(),

                /* ===============================
                 |  PORT
                 ===============================*/

                TextColumn::make('status_colis_port')
                    ->label('Port')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'EN_ATTENTE' => 'secondary',
                        'ENTRE' => 'warning',
                        'SORTI' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('date_entree_port')
                    ->label('Arrivée')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),

                /* ===============================
                 |  DOUANE
                 ===============================*/

                TextColumn::make('status_colis_douane')
                    ->label('Douane')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'EN_ATTENTE' => 'secondary',
                        'ENTRE' => 'warning',
                        'SORTI' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('num_t1')
                    ->label('T1')
                    ->badge()
                    ->copyable()
                    ->color(fn ($state) =>
                        filled($state) ? 'success' : 'secondary'
                    )
                    ->toggleable(),

                TextColumn::make('etat_t1')
                    ->label('État T1')
                    ->badge()
                    ->color(fn ($state) =>
                        $state === 'PAYE' ? 'success' : 'secondary'
                    )
                    ->toggleable(isToggledHiddenByDefault: true),

                /* ===============================
                 |  EXPERTISE
                 ===============================*/

                TextColumn::make('etat_expertise')
                    ->label('Expertise')
                    ->badge()
                    ->color(fn ($state) =>
                        $state === 'EFFECTUEE' ? 'success' : 'danger'
                    )
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('etat_pvc')
                    ->label('PVC')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'NON_RECU' => 'secondary',
                        'RECU' => 'warning',
                        'PAYE' => 'success',
                        default => 'gray',
                    })
                    ->toggleable(),

                TextColumn::make('etat_ae')
                    ->label('AE')
                    ->badge()
                    ->color(fn ($state) =>
                        $state === 'VALIDE' ? 'success' : 'danger'
                    )
                    ->toggleable(),

                TextColumn::make('etat_cmc')
                    ->label('CMC')
                    ->badge()
                    ->color(fn ($state) =>
                        $state === 'RECU' ? 'success' : 'secondary'
                    )
                    ->toggleable(isToggledHiddenByDefault: true),

                /* ===============================
                 |  CLOTURE
                 ===============================*/

                TextColumn::make('status')
                    ->label('Clôture')
                    ->badge()
                    ->color(fn ($state) =>
                        $state === 'TERMINE' ? 'success' : 'warning'
                    ),

                IconColumn::make('is_late')
                    ->label('Retard')
                    ->boolean()
                    ->getStateUsing(fn ($record) =>
                        $record->status !== 'TERMINE'
                        && $record->created_at->diffInDays(now()) > 15
                    )
                    ->color('danger'),

                /* ===============================
                 |  SYSTEME
                 ===============================*/

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])

            ->filters([

                SelectFilter::make('id_type_colis')
                    ->label('Type')
                    ->relationship('typeColis', 'nom'),

                SelectFilter::make('client_id')
                    ->relationship('client', 'nom')
                    ->searchable(),

                SelectFilter::make('status')
                    ->options([
                        'EN_COURS' => 'En cours',
                        'TERMINE' => 'Terminé',
                    ]),

                Filter::make('expertise_en_cours')
                    ->label('Expertise en cours')
                    ->query(fn (Builder $query) =>
                        $query->where('etat_pvc', 'PAYE')
                              ->where(function ($q) {
                                  $q->where('etat_ae', 'NON_VALIDE')
                                    ->orWhere('etat_cmc', 'NON_RECU');
                              })
                    )
                    ->toggle(),

            ])

            ->defaultSort('created_at', 'desc')

            ->recordActions([

                /* ===============================
                 |  VIEW ACTION
                 ===============================*/
                ViewAction::make('view'),

                /* ===============================
                 |  EDIT ACTION
                 ===============================*/
                EditAction::make()
                    ->visible(fn ($record) =>
                        $record->status !== 'TERMINE'
                    ),

            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () =>
                            auth()->user()?->hasRole('super_admin')
                        ),
                ]),
            ])

            ->poll('15s');
    }
}
