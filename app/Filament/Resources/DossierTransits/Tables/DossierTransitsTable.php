<?php

namespace App\Filament\Resources\DossierTransits\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DossierTransitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn (Builder $query) =>
                $query->with(['client', 'colis', 'type_dossier'])
                    ->withCount('documents')
            )

            ->columns([

                TextColumn::make('reference')
                    ->label('Référence')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                TextColumn::make('nom')
                    ->label('Nom du dossier')
                    ->searchable()
                    ->sortable()
                    ->limit(25)
                    ->tooltip(fn ($record) => $record->nom),

                TextColumn::make('client.nom')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('colis.numero_bl')
                    ->label('N° BL')
                    ->badge()
                    ->color('primary')
                    ->placeholder('Aucun colis'),

                TextColumn::make('type_dossier.nom')
                    ->label('Type')
                    ->badge()
                    ->color('secondary')
                    ->sortable(),

                TextColumn::make('date_depot')
                    ->label('Date dépôt')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'OUVERT' => 'warning',
                        'EN_COURS' => 'info',
                        'CLOTURE' => 'success',
                        default => 'gray',
                    }),

                IconColumn::make('is_closed')
                    ->label('Clôturé')
                    ->boolean()
                    ->getStateUsing(
                        fn ($record) =>
                        $record->status === 'CLOTURE'
                    )
                    ->color('success'),

                TextColumn::make('documents_count')
                    ->label('Nb Documents')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'primary' : 'secondary'),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->filters([

                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'OUVERT' => 'Ouvert',
                        'EN_COURS' => 'En cours',
                        'CLOTURE' => 'Clôturé',
                    ]),

                SelectFilter::make('id_type_dossier')
                    ->label('Type de dossier')
                    ->relationship('type_dossier', 'nom')
                    ->searchable(),
            ])

            ->defaultSort('created_at', 'desc')

            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn ($record) => $record->status !== 'CLOTURE'),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

