<?php

namespace App\Filament\Resources\ColisUnites\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Action;

class ColisUnitesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                // === INFOS DU COLIS LIÉ ===
                TextColumn::make('colis.numero_bl')
                    ->label('N° BL')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->colis?->typeColis?->nom ?? 'Type inconnu')
                    ->icon('heroicon-o-document-text')
                    ->copyable()
                    ->copyMessage('Numéro BL copié')
                    ->toggleable(),

                TextColumn::make('colis.id_dossier_transit')
                    ->label('Dossier transit')
                    ->formatStateUsing(fn ($record) => $record->colis?->dossierTransit?->reference ?? '—')
                    ->badge()
                    ->color('warning')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('colis.port.nom')
                    ->label('Port d\'entrée')
                    ->placeholder('—')
                    ->icon('heroicon-o-map-pin')
                    ->toggleable(),

                // === INFOS DE L'UNITÉ ===
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
                    ->copyMessage('N° Conteneur copié')
                    ->toggleable(),

                TextColumn::make('numero_chassis')
                    ->label('N° Châssis')
                    ->placeholder('—')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('N° Châssis copié')
                    ->toggleable(),

                TextColumn::make('vin')
                    ->label('VIN')
                    ->placeholder('—')
                    ->copyable()
                    ->copyMessage('VIN copié')
                    ->toggleable(),

                TextColumn::make('etat')
                    ->label('État')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'AU_PORT' => 'warning',
                        'A_LA_DOUANE' => 'info',
                        'EXPERTISE' => 'purple',
                        'EN_ROUTE' => 'primary',
                        'LIVRE' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'AU_PORT' => 'Au port',
                        'A_LA_DOUANE' => 'En douane',
                        'EXPERTISE' => 'Expertise',
                        'EN_ROUTE' => 'En route',
                        'LIVRE' => 'Livré',
                        default => $state,
                    })
                    ->sortable()
                    ->toggleable(),

                // === INFORMATIONS DOUANIÈRES DÉTAILLÉES ===
                TextColumn::make('num_t1')
                    ->label('N° T1')
                    ->placeholder('—')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('N° T1 copié')
                    ->toggleable(),

                TextColumn::make('etat_t1')
                    ->label('État T1')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'FOURNI' => 'warning',
                        'PAYE' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'FOURNI' => 'Fourni',
                        'PAYE' => 'Payé',
                        default => $state,
                    })
                    ->toggleable(),

                TextColumn::make('declaration_reference')
                    ->label('Réf déclaration')
                    ->placeholder('—')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Référence copiée')
                    ->toggleable(),

                TextColumn::make('date_entree_douane')
                    ->label('Entrée douane')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('date_sortie_douane')
                    ->label('Sortie douane')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('status_douane')
                    ->label('Statut douane')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'EN_ATTENTE' => 'warning',
                        'EN_COURS' => 'info',
                        'VALIDE' => 'success',
                        'REJETE' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'EN_ATTENTE' => 'En attente',
                        'EN_COURS' => 'En cours',
                        'VALIDE' => 'Validé',
                        'REJETE' => 'Rejeté',
                        default => $state,
                    })
                    ->toggleable(),

                // === INFORMATIONS D'EXPERTISE DÉTAILLÉES ===
                TextColumn::make('num_pvc')
                    ->label('N° PVC')
                    ->placeholder('—')
                    ->copyable()
                    ->copyMessage('N° PVC copié')
                    ->toggleable(),

                TextColumn::make('etat_pvc')
                    ->label('État PVC')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'EN_ATTENTE' => 'warning',
                        'VALIDE' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'EN_ATTENTE' => 'En attente',
                        'VALIDE' => 'Validé',
                        default => $state,
                    })
                    ->toggleable(),

                TextColumn::make('num_ae')
                    ->label('N° AE')
                    ->placeholder('—')
                    ->copyable()
                    ->copyMessage('N° AE copié')
                    ->toggleable(),

                TextColumn::make('etat_ae')
                    ->label('État AE')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'EN_ATTENTE' => 'warning',
                        'VALIDE' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'EN_ATTENTE' => 'En attente',
                        'VALIDE' => 'Validé',
                        default => $state,
                    })
                    ->toggleable(),

                TextColumn::make('num_cmc')
                    ->label('N° CMC')
                    ->placeholder('—')
                    ->copyable()
                    ->copyMessage('N° CMC copié')
                    ->toggleable(),

                TextColumn::make('etat_cmc')
                    ->label('État CMC')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'EN_ATTENTE' => 'warning',
                        'VALIDE' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'EN_ATTENTE' => 'En attente',
                        'VALIDE' => 'Validé',
                        default => $state,
                    })
                    ->toggleable(),

                // === INFORMATIONS DE LIVRAISON DÉTAILLÉES ===
                TextColumn::make('date_livraison')
                    ->label('Date livraison')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('status_colis_livraison')
                    ->label('Statut livraison')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'EN_ATTENTE' => 'warning',
                        'EN_COURS' => 'info',
                        'LIVRE' => 'success',
                        'ANNULE' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'EN_ATTENTE' => 'En attente',
                        'EN_COURS' => 'En cours',
                        'LIVRE' => 'Livré',
                        'ANNULE' => 'Annulé',
                        default => $state,
                    })
                    ->toggleable(),

                TextColumn::make('livraison_commentaire')
                    ->label('Commentaire livraison')
                    ->placeholder('—')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                // === DATES DE SUIVI ===
                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Mis à jour')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->filters([

                // Filtres par type d'unité
                SelectFilter::make('type')
                    ->label("Type d'unité")
                    ->options([
                        'CONTENEUR' => 'Conteneur',
                        'CHASSIS' => 'Châssis',
                        'CHASSIS_VOITURE' => 'Châssis voiture',
                        'CHASSIS_MACHINE' => 'Châssis machine',
                    ]),

                // Filtres par état
                SelectFilter::make('etat')
                    ->label('État')
                    ->options([
                        'AU_PORT' => 'Au port',
                        'A_LA_DOUANE' => 'En douane',
                        'EXPERTISE' => 'Expertise',
                        'EN_ROUTE' => 'En route',
                        'LIVRE' => 'Livré',
                    ]),

                // Filtres T1
                SelectFilter::make('etat_t1')
                    ->label('État T1')
                    ->options([
                        'FOURNI' => 'Fourni',
                        'PAYE' => 'Payé',
                    ]),

                // Filtres expertise
                Filter::make('avec_pvc')
                    ->label('Avec PVC')
                    ->query(fn (Builder $query) => $query->whereNotNull('num_pvc')),

                Filter::make('avec_ae')
                    ->label('Avec AE')
                    ->query(fn (Builder $query) => $query->whereNotNull('num_ae')),

                Filter::make('avec_cmc')
                    ->label('Avec CMC')
                    ->query(fn (Builder $query) => $query->whereNotNull('num_cmc')),

                Filter::make('expertise_complete')
                    ->label('Expertise complète')
                    ->query(fn (Builder $query) => $query
                        ->whereNotNull('num_pvc')
                        ->whereNotNull('num_ae')
                        ->whereNotNull('num_cmc')),

                // Filtres douane
                Filter::make('en_douane')
                    ->label('En cours de douane')
                    ->query(fn (Builder $query) => $query
                        ->where('status_douane', 'EN_COURS')
                        ->orWhere('etat_t1', 'FOURNI')),

                Filter::make('douane_validee')
                    ->label('Douane validée')
                    ->query(fn (Builder $query) => $query
                        ->where('status_douane', 'VALIDE')
                        ->orWhere('etat_t1', 'PAYE')),

                // Filtres livraison
                Filter::make('livre')
                    ->label('Livrés')
                    ->query(fn (Builder $query) => $query->where('etat', 'LIVRE')),

                Filter::make('non_livre')
                    ->label('Non livrés')
                    ->query(fn (Builder $query) => $query->where('etat', '!=', 'LIVRE')),

                Filter::make('livraison_en_cours')
                    ->label('Livraison en cours')
                    ->query(fn (Builder $query) => $query->where('status_colis_livraison', 'EN_COURS')),
            ])

            ->recordActions([
                // Bouton pour voir le détail du colis parent
                Action::make('voir_colis')
                    ->label('Voir le BL')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->url(fn ($record) => route('filament.admin.resources.colis.view', $record->colis))
                    ->openUrlInNewTab(),

                // Bouton pour voir le détail de l'unité
                ViewAction::make()
                    ->label('Voir détails')
                    ->icon('heroicon-o-document-text')
                    ->color('info'),

                // Bouton pour modifier
                EditAction::make()
                    ->label('Modifier')
                    ->icon('heroicon-o-pencil')
                    ->color('warning'),

                // Bouton pour imprimer/export
                Action::make('exporter')
                    ->label('Exporter')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->action(function ($record) {
                        // Logique d'export
                    })
                    ->visible(false), // À activer si besoin
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Supprimer la sélection')
                        ->icon('heroicon-o-trash')
                        ->requiresConfirmation()
                        ->modalHeading('Supprimer les unités')
                        ->modalDescription('Êtes-vous sûr de vouloir supprimer ces unités ? Cette action est irréversible.'),
                ]),
            ])

            ->emptyStateHeading('Aucune unité')
            ->emptyStateDescription('Ce BL ne contient aucune unité pour le moment.')
            ->emptyStateIcon('heroicon-o-cube')

            ->defaultSort('created_at', 'desc')

            // Polling pour mise à jour en temps réel (optionnel)
            ->poll('60s');
    }
}
