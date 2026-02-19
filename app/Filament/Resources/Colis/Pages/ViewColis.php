<?php

namespace App\Filament\Resources\Colis\Pages;

use App\Filament\Resources\Colis\ColisResource;
use App\Models\Colis;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ViewColis extends ViewRecord
{
    protected static string $resource = ColisResource::class;

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\EditAction::make()
    //             ->visible(fn ($record) => $record->etat_colis !== 'CLOTURE'),
    //         Actions\Action::make('tracking')
    //             ->label('Suivi complet')
    //             ->icon('heroicon-o-clock')
    //             ->url(fn ($record) => ColisResource::getUrl('tracking', ['record' => $record])),
    //     ];
    // }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Détails du colis')
                    ->tabs([
                        // ===== INFORMATIONS GÉNÉRALES =====
                        Tab::make('Informations générales')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make('Identification du colis')
                                    ->schema([
                                        Grid::make(3)->schema([
                                            TextEntry::make('numero_bl')
                                                ->label('Numéro BL')
                                                ->copyable()
                                                ->weight('bold')
                                                ->color('primary')
                                                ->size('lg'),
                                                
                                            TextEntry::make('typeColis.nom')
                                                ->label('Type de colis')
                                                ->badge()
                                                ->color(fn ($record) => 
                                                    str_contains(strtolower($record->typeColis?->nom ?? ''), 'chassis') 
                                                        ? 'warning' 
                                                        : 'primary'
                                                ),
                                                
                                            TextEntry::make('etat_colis')
                                                ->label('Statut global')
                                                ->badge()
                                                ->formatStateUsing(fn ($state) => match ($state) {
                                                    'BL_ENREGISTRE' => 'BL enregistré',
                                                    'AU_PORT' => 'Au port',
                                                    'A_LA_DOUANE' => 'À la douane',
                                                    'EXPERTISE' => 'En expertise',
                                                    'EN_ROUTE' => 'En route',
                                                    'LIVRE' => 'Livré',
                                                    'CLOTURE' => 'Clôturé',
                                                    default => $state,
                                                })
                                                ->color(fn ($state) => match ($state) {
                                                    'BL_ENREGISTRE' => 'gray',
                                                    'AU_PORT' => 'info',
                                                    'A_LA_DOUANE' => 'warning',
                                                    'EXPERTISE' => 'purple', // ou 'primary' selon vos préférences
                                                    'EN_ROUTE' => 'primary',
                                                    'LIVRE' => 'success',
                                                    'CLOTURE' => 'danger',
                                                    default => 'secondary',
                                                }),
                                        ]),
                                        
                                        Grid::make(3)->schema([
                                            TextEntry::make('dossierTransit.client.nom')
                                                ->label('Client')
                                                ->icon('heroicon-o-user')
                                                // ->url(fn ($record) => \App\Filament\Resources\Clients\ClientResource::getUrl('edit', ['record' => $record->client_id]))
                                                ->openUrlInNewTab(),
                                                
                                            TextEntry::make('port.nom')
                                                ->label('Port d\'entrée'),
                                                
                                            TextEntry::make('agent.name')
                                                ->label('Agent responsable')
                                                ->icon('heroicon-o-user-circle'),
                                        ]),
                                        
                                        TextEntry::make('description')
                                            ->label('Description')
                                            ->columnSpanFull()
                                            ->markdown()
                                            ->visible(fn ($record) => !empty($record->description)),
                                    ]),
                                    
                                Section::make('Dates clés')
                                    ->schema([
                                        Grid::make(3)->schema([
                                            TextEntry::make('created_at')
                                                ->label('Créé le')
                                                ->dateTime('d/m/Y H:i')
                                                ->icon('heroicon-o-calendar'),
                                                
                                            TextEntry::make('updated_at')
                                                ->label('Dernière modification')
                                                ->dateTime('d/m/Y H:i')
                                                ->icon('heroicon-o-clock'),
                                        ]),
                                    ]),
                            ]),
                        
                        // ===== ÉTAPE PORT =====
                        Tab::make('Port')
                            ->icon('heroicon-o-archive-box')
                            ->schema([
                                Section::make('Opérations portuaires')
                                    ->schema([
                                        Grid::make(3)->schema([
                                            TextEntry::make('status_colis_port')
                                                ->label('Statut au port')
                                                ->badge()
                                                ->formatStateUsing(fn ($state) => match ($state) {
                                                    'EN_ATTENTE' => 'En attente',
                                                    'ENTRE' => 'Entré au port',
                                                    'SORTI' => 'Sorti du port',
                                                    default => $state ?? 'Non défini',
                                                })
                                                ->color(fn ($state) => match ($state) {
                                                    'EN_ATTENTE' => 'gray',
                                                    'ENTRE' => 'info',
                                                    'SORTI' => 'success',
                                                    default => 'secondary',
                                                }),
                                                
                                            TextEntry::make('date_entree_port')
                                                ->label('Date d\'entrée')
                                                ->date('d/m/Y')
                                                ->icon('heroicon-o-arrow-left-circle')
                                                ->placeholder('Non renseignée'),
                                                
                                            TextEntry::make('date_sortie_port')
                                                ->label('Date de sortie')
                                                ->date('d/m/Y')
                                                ->icon('heroicon-o-arrow-right-circle')
                                                ->placeholder('Non renseignée'),
                                        ]),
                                    ]),
                            ]),
                        
                        // ===== ÉTAPE DOUANE =====
                        Tab::make('Douane')
                            ->icon('heroicon-o-document-magnifying-glass')
                            ->schema([
                                Section::make('Formalités douanières')
                                    ->schema([
                                        Grid::make(3)->schema([
                                            TextEntry::make('num_t1')
                                                ->label('Numéro T1')
                                                ->copyable()
                                                ->badge()
                                                ->color('info')
                                                ->placeholder('Non renseigné'),
                                                
                                            TextEntry::make('etat_t1')
                                                ->label('État T1')
                                                ->badge()
                                                ->formatStateUsing(fn ($state) => match ($state) {
                                                    'FOURNI' => 'Fourni',
                                                    'PAYE' => 'Payé',
                                                    default => $state ?? 'Non défini',
                                                })
                                                ->color(fn ($state) => match ($state) {
                                                    'FOURNI' => 'warning',
                                                    'PAYE' => 'success',
                                                    default => 'gray',
                                                }),
                                                
                                            TextEntry::make('declaration_reference')
                                                ->label('Référence déclaration')
                                                ->copyable()
                                                ->badge()
                                                ->placeholder('Non renseignée'),
                                        ]),
                                        
                                        Grid::make(3)->schema([
                                            TextEntry::make('status_colis_douane')
                                                ->label('Statut en douane')
                                                ->badge()
                                                ->formatStateUsing(fn ($state) => match ($state) {
                                                    'EN_ATTENTE' => 'En attente',
                                                    'ENTRE' => 'Entré en douane',
                                                    'SORTI' => 'Sorti de douane',
                                                    default => $state ?? 'Non défini',
                                                })
                                                ->color(fn ($state) => match ($state) {
                                                    'EN_ATTENTE' => 'gray',
                                                    'ENTRE' => 'info',
                                                    'SORTI' => 'success',
                                                    default => 'secondary',
                                                }),
                                                
                                            TextEntry::make('date_entree_douane')
                                                ->label('Date d\'entrée')
                                                ->date('d/m/Y')
                                                ->icon('heroicon-o-arrow-left-circle')
                                                ->placeholder('Non renseignée'),
                                                
                                            TextEntry::make('date_sortie_douane')
                                                ->label('Date de sortie')
                                                ->date('d/m/Y')
                                                ->icon('heroicon-o-arrow-right-circle')
                                                ->placeholder('Non renseignée'),
                                        ]),
                                    ]),
                            ]),
                        
                        // ===== ÉTAPE EXPERTISE =====
                        Tab::make('Expertise')
                            ->icon('heroicon-o-clipboard-document-check')
                            ->visible(fn ($record) => 
                                $record->typeColis->nom === 'Véhicules'
                            )
                            ->schema([
                                Section::make('Expertise ONT - Véhicules')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextEntry::make('etat_expertise')
                                                ->label('État expertise')
                                                ->badge()
                                                ->formatStateUsing(fn ($state) => match ($state) {
                                                    'EN_ATTENTE' => 'En attente',
                                                    'EFFECTUEE' => 'Effectuée',
                                                    default => $state ?? 'Non défini',
                                                })
                                                ->color(fn ($state) => match ($state) {
                                                    'EN_ATTENTE' => 'warning',
                                                    'EFFECTUEE' => 'success',
                                                    default => 'gray',
                                                }),
                                        ]),
                                        
                                        // PVC Section
                                        Section::make('Procès-Verbal de Contrôle (PVC)')
                                            ->compact()
                                            ->schema([
                                                Grid::make(2)->schema([
                                                    TextEntry::make('num_pvc')
                                                        ->label('Numéro PVC')
                                                        ->copyable()
                                                        ->badge()
                                                        ->color('info')
                                                        ->placeholder('Non renseigné'),
                                                        
                                                    TextEntry::make('etat_pvc')
                                                        ->label('État PVC')
                                                        ->badge()
                                                        ->formatStateUsing(fn ($state) => match ($state) {
                                                            'NON_RECU' => 'Non reçu',
                                                            'RECU' => 'Reçu',
                                                            'PAYE' => 'Payé',
                                                            default => $state ?? 'Non défini',
                                                        })
                                                        ->color(fn ($state) => match ($state) {
                                                            'NON_RECU' => 'danger',
                                                            'RECU' => 'info',
                                                            'PAYE' => 'success',
                                                            default => 'gray',
                                                        }),
                                                ]),
                                            ]),
                                        
                                        // AE Section
                                        Section::make('Autorisation d\'Enlèvement (AE)')
                                            ->compact()
                                            ->schema([
                                                Grid::make(2)->schema([
                                                    TextEntry::make('num_ae')
                                                        ->label('Numéro AE')
                                                        ->copyable()
                                                        ->badge()
                                                        ->color('info')
                                                        ->placeholder('Non renseigné'),
                                                        
                                                    TextEntry::make('etat_ae')
                                                        ->label('État AE')
                                                        ->badge()
                                                        ->formatStateUsing(fn ($state) => match ($state) {
                                                            'NON_VALIDE' => 'Non valide',
                                                            'VALIDE' => 'Valide',
                                                            default => $state ?? 'Non défini',
                                                        })
                                                        ->color(fn ($state) => match ($state) {
                                                            'NON_VALIDE' => 'danger',
                                                            'VALIDE' => 'success',
                                                            default => 'gray',
                                                }),
                                                ]),
                                            ]),
                                        
                                        // CMC Section
                                        Section::make('Certificat de Mise en Conformité (CMC)')
                                            ->compact()
                                            ->schema([
                                                Grid::make(2)->schema([
                                                    TextEntry::make('num_cmc')
                                                        ->label('Numéro CMC')
                                                        ->copyable()
                                                        ->badge()
                                                        ->color('info')
                                                        ->placeholder('Non renseigné'),
                                                        
                                                    TextEntry::make('etat_cmc')
                                                        ->label('État CMC')
                                                        ->badge()
                                                        ->formatStateUsing(fn ($state) => match ($state) {
                                                            'NON_RECU' => 'Non reçu',
                                                            'RECU' => 'Reçu',
                                                            default => $state ?? 'Non défini',
                                                        })
                                                        ->color(fn ($state) => match ($state) {
                                                            'NON_RECU' => 'danger',
                                                            'RECU' => 'success',
                                                            default => 'gray',
                                                        }),
                                                ]),
                                            ]),
                                    ]),
                            ]),
                        
                        // ===== FINALISATION =====
                        Tab::make('Finalisation')
                            ->icon('heroicon-o-check-badge')
                            ->schema([
                                Section::make('Clôture du dossier')
                                    ->schema([
                                        Grid::make(3)->schema([
                                            TextEntry::make('status')
                                                ->label('État dossier')
                                                ->badge()
                                                ->formatStateUsing(fn ($state) => match ($state) {
                                                    'EN_COURS' => 'En cours',
                                                    'TERMINE' => 'Terminé',
                                                    default => $state ?? 'Non défini',
                                                })
                                                ->color(fn ($state) => match ($state) {
                                                    'EN_COURS' => 'warning',
                                                    'TERMINE' => 'success',
                                                    default => 'gray',
                                                }),
                                                
                                            TextEntry::make('date_livraison')
                                                ->label('Date de livraison')
                                                ->date('d/m/Y')
                                                ->icon('heroicon-o-truck')
                                                ->placeholder('Non renseignée'),
                                        ]),
                                        
                                        TextEntry::make('commentaires_cloture')
                                            ->label('Commentaires')
                                            ->markdown()
                                            ->columnSpanFull()
                                            ->visible(fn ($record) => !empty($record->commentaires_cloture)),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}