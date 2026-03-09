<?php

namespace App\Filament\Resources\Colis\Pages;

use App\Filament\Resources\Colis\ColisResource;
use App\Filament\Traits\HasExports;
use App\Models\Colis;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Illuminate\Support\HtmlString;

class ViewColis extends ViewRecord
{
    use HasExports;

    protected static string $resource = ColisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn($record) => $record->etat_colis !== 'CLOTURE'),

            Actions\Action::make('gerer_unites')
                ->label('Gérer les unités')
                ->icon('heroicon-o-cube')
                ->color('warning')
                // ->url(fn($record) => ColisResource::getUrl('unites', ['record' => $record]))
                ->visible(fn($record) => $record->unites->count() > 0),

            Actions\Action::make('print_recap')
                ->label('Imprimer')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->action(function () {
                    $record = $this->getRecord();
                    $pdf = $this->generateColisPDF($record, 'pdf.recap-complet');
                    return response()->streamDownload(
                        fn() => print ($pdf->output()),
                        $this->getFilename($record, 'pdf.recap-complet') . '.pdf'
                    );
                }),
        ];
    }

    /**
     * Détermine si le colis est de type châssis/véhicule
     */
    protected function isVehiculeType($record): bool
    {
        if (!$record->typeColis)
            return false;

        $typesVehicule = [
            'CHASSIS',
            'CHASSIS_VOITURE',
            'CHASSIS_MACHINE',
            'VÉHICULE',
            'VÉHICULES',
        ];

        $typeUpper = strtoupper($record->typeColis->nom);
        return in_array($typeUpper, array_map('strtoupper', $typesVehicule));
    }

    /**
     * Récupère les statistiques des unités
     */
    protected function getUnitesStats($record): array
    {
        $unites = $record->unites;

        return [
            'total' => $unites->count(),
            'au_port' => $unites->where('etat', 'AU_PORT')->count(),
            'a_la_douane' => $unites->where('etat', 'A_LA_DOUANE')->count(),
            'expertise' => $unites->where('etat', 'EXPERTISE')->count(),
            'en_route' => $unites->where('etat', 'EN_ROUTE')->count(),
            'livre' => $unites->where('etat', 'LIVRE')->count(),

            // Statistiques douane
            't1_fourni' => $unites->where('etat_t1', 'FOURNI')->count(),
            't1_paye' => $unites->where('etat_t1', 'PAYE')->count(),

            // Statistiques expertise
            'avec_pvc' => $unites->whereNotNull('num_pvc')->count(),
            'avec_ae' => $unites->whereNotNull('num_ae')->count(),
            'avec_cmc' => $unites->whereNotNull('num_cmc')->count(),
            'expertise_complete' => $unites->filter(function($unite) {
                return !is_null($unite->num_pvc) &&
                       !is_null($unite->num_ae) &&
                       !is_null($unite->num_cmc);
            })->count(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Détails du colis')
                    ->tabs([
                        // ===== INFORMATIONS GÉNÉRALES =====
                        Tab::make('Informations générales')
                            ->icon('heroicon-o-information-circle')
                            ->badge(fn($record) => $record->unites->count())
                            ->badgeColor('primary')
                            ->schema([
                                Section::make('Identification du colis')
                                    ->schema([
                                        Grid::make(3)->schema([
                                            TextEntry::make('numero_bl')
                                                ->label('Numéro BL')
                                                ->copyable()
                                                ->weight(FontWeight::Bold)
                                                ->color('primary')
                                                ->size('lg')
                                                ->icon('heroicon-o-document-text'),

                                            TextEntry::make('typeColis.nom')
                                                ->label('Type de colis')
                                                ->badge()
                                                ->color(
                                                    fn($record) =>
                                                    $this->isVehiculeType($record) ? 'warning' : 'primary'
                                                ),

                                            TextEntry::make('etat_colis')
                                                ->label('Statut global')
                                                ->badge()
                                                ->formatStateUsing(fn($state) => match ($state) {
                                                    'BL_ENREGISTRE' => 'BL enregistré',
                                                    'AU_PORT' => 'Au port',
                                                    'A_LA_DOUANE' => 'À la douane',
                                                    'EXPERTISE' => 'En expertise',
                                                    'EN_ROUTE' => 'En route',
                                                    'LIVRE' => 'Livré',
                                                    'CLOTURE' => 'Clôturé',
                                                    default => $state,
                                                })
                                                ->color(fn($state) => match ($state) {
                                                    'BL_ENREGISTRE' => 'gray',
                                                    'AU_PORT' => 'info',
                                                    'A_LA_DOUANE' => 'warning',
                                                    'EXPERTISE' => 'purple',
                                                    'EN_ROUTE' => 'primary',
                                                    'LIVRE' => 'success',
                                                    'CLOTURE' => 'danger',
                                                    default => 'secondary',
                                                }),
                                        ]),

                                        Grid::make(3)->schema([
                                            TextEntry::make('dossierTransit.reference')
                                                ->label('Dossier transit')
                                                ->icon('heroicon-o-folder')
                                                ->badge()
                                                ->color('warning')
                                                // ->url(fn($record) => $record->dossierTransit ?
                                                //     route('filament.admin.resources.dossier-transits.view', $record->dossierTransit) : null)
                                                ->openUrlInNewTab(),

                                            TextEntry::make('dossierTransit.client.nom')
                                                ->label('Client')
                                                ->icon('heroicon-o-user'),

                                            TextEntry::make('port.nom')
                                                ->label('Port d\'entrée')
                                                ->icon('heroicon-o-map-pin'),

                                            TextEntry::make('agent.name')
                                                ->label('Agent responsable')
                                                ->icon('heroicon-o-user-circle')
                                                ->default('Non assigné'),
                                        ]),

                                        TextEntry::make('description')
                                            ->label('Description')
                                            ->columnSpanFull()
                                            ->markdown()
                                            ->visible(fn($record) => !empty($record->description)),
                                    ]),

                                // ✅ Récapitulatif des unités
                                Section::make('Récapitulatif des unités')
                                    ->schema(function ($record) {
                                        $stats = $this->getUnitesStats($record);

                                        return [
                                            Grid::make(6)->schema([
                                                TextEntry::make('total_unites')
                                                    ->label('Total unités')
                                                    ->default($stats['total'])
                                                    ->badge()
                                                    ->color('primary'),

                                                TextEntry::make('au_port')
                                                    ->label('Au port')
                                                    ->default($stats['au_port'])
                                                    ->badge()
                                                    ->color('warning')
                                                    ->visible($stats['au_port'] > 0),

                                                TextEntry::make('a_la_douane')
                                                    ->label('En douane')
                                                    ->default($stats['a_la_douane'])
                                                    ->badge()
                                                    ->color('info')
                                                    ->visible($stats['a_la_douane'] > 0),

                                                TextEntry::make('expertise')
                                                    ->label('En expertise')
                                                    ->default($stats['expertise'])
                                                    ->badge()
                                                    ->color('purple')
                                                    ->visible($stats['expertise'] > 0),

                                                TextEntry::make('en_route')
                                                    ->label('En route')
                                                    ->default($stats['en_route'])
                                                    ->badge()
                                                    ->color('primary')
                                                    ->visible($stats['en_route'] > 0),

                                                TextEntry::make('livre')
                                                    ->label('Livré')
                                                    ->default($stats['livre'])
                                                    ->badge()
                                                    ->color('success')
                                                    ->visible($stats['livre'] > 0),
                                            ]),

                                            // Barre de progression
                                            TextEntry::make('progression')
                                                ->label('Progression globale')
                                                ->getStateUsing(function() use ($stats) {
                                                    if ($stats['total'] === 0) return '0%';
                                                    $pourcentage = round(($stats['livre'] / $stats['total']) * 100);
                                                    return $pourcentage . '%';
                                                })
                                                ->badge()
                                                ->color(function() use ($stats) {
                                                    if ($stats['total'] === 0) return 'gray';
                                                    $pourcentage = round(($stats['livre'] / $stats['total']) * 100);
                                                    return match(true) {
                                                        $pourcentage >= 100 => 'success',
                                                        $pourcentage >= 50 => 'warning',
                                                        default => 'gray',
                                                    };
                                                })
                                                ->suffix(function() use ($stats) {
                                                    return " ({$stats['livre']}/{$stats['total']})";
                                                }),
                                        ];
                                    })
                                    ->visible(fn($record) => $record->unites->count() > 0),

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

                        // ===== LISTE COMPLÈTE DES UNITÉS =====
                        Tab::make('Unités')
                            ->icon('heroicon-o-cube')
                            ->badge(fn($record) => $record->unites->count())
                            ->badgeColor('warning')
                            ->visible(fn($record) => $record->unites->count() > 0)
                            ->schema([
                                Section::make('Toutes les unités du BL')
                                    ->description('Liste détaillée des unités avec toutes leurs informations')
                                    ->schema([
                                        RepeatableEntry::make('unites')
                                            ->label('')
                                            ->schema([
                                                // En-tête de l'unité avec numéro
                                                TextEntry::make('header')
                                                    ->label('')
                                                    ->getStateUsing(function($record) {
                                                        $numero = $record->numero_chassis ?? $record->numero_conteneur ?? 'Unité';
                                                        return "📦 {$numero}";
                                                    })
                                                    ->weight(FontWeight::Bold)
                                                    ->size('lg')
                                                    ->color('primary')
                                                    ->columnSpanFull(),

                                                Grid::make(4)->schema([
                                                    // Type d'unité
                                                    TextEntry::make('type')
                                                        ->label('Type')
                                                        ->formatStateUsing(fn($state) => match ($state) {
                                                            'CONTENEUR' => 'Conteneur',
                                                            'CHASSIS' => 'Châssis',
                                                            'CHASSIS_VOITURE' => 'Châssis Voiture',
                                                            'CHASSIS_MACHINE' => 'Châssis Machine',
                                                            default => $state,
                                                        })
                                                        ->badge()
                                                        ->color(fn($state) => match ($state) {
                                                            'CONTENEUR' => 'info',
                                                            'CHASSIS' => 'primary',
                                                            'CHASSIS_VOITURE' => 'warning',
                                                            'CHASSIS_MACHINE' => 'danger',
                                                            default => 'gray',
                                                        }),

                                                    // État actuel
                                                    TextEntry::make('etat')
                                                        ->label('État')
                                                        ->badge()
                                                        ->formatStateUsing(fn($state) => match ($state) {
                                                            'AU_PORT' => 'Au port',
                                                            'A_LA_DOUANE' => 'En douane',
                                                            'EXPERTISE' => 'En expertise',
                                                            'EN_ROUTE' => 'En route',
                                                            'LIVRE' => 'Livré',
                                                            default => $state,
                                                        })
                                                        ->color(fn($state) => match ($state) {
                                                            'AU_PORT' => 'warning',
                                                            'A_LA_DOUANE' => 'info',
                                                            'EXPERTISE' => 'purple',
                                                            'EN_ROUTE' => 'primary',
                                                            'LIVRE' => 'success',
                                                            default => 'gray',
                                                        }),

                                                    // Numéros d'identification
                                                    TextEntry::make('numero_conteneur')
                                                        ->label('N° Conteneur')
                                                        ->placeholder('—')
                                                        ->copyable()
                                                        ->visible(fn($record) => $record->type === 'CONTENEUR'),

                                                    TextEntry::make('numero_chassis')
                                                        ->label('N° Châssis')
                                                        ->placeholder('—')
                                                        ->copyable()
                                                        ->visible(fn($record) => in_array($record->type, ['CHASSIS', 'CHASSIS_VOITURE', 'CHASSIS_MACHINE'])),

                                                    TextEntry::make('vin')
                                                        ->label('VIN')
                                                        ->placeholder('—')
                                                        ->copyable()
                                                        ->visible(fn($record) => in_array($record->type, ['CHASSIS_VOITURE', 'CHASSIS_MACHINE'])),
                                                ]),

                                                // Informations douanières
                                                Fieldset::make('Informations douanières')
                                                    ->schema([
                                                        Grid::make(4)->schema([
                                                            TextEntry::make('num_t1')
                                                                ->label('N° T1')
                                                                ->placeholder('—')
                                                                ->copyable()
                                                                ->badge()
                                                                ->color('info'),

                                                            TextEntry::make('etat_t1')
                                                                ->label('État T1')
                                                                ->formatStateUsing(fn($state) => match ($state) {
                                                                    'FOURNI' => 'Fourni',
                                                                    'PAYE' => 'Payé',
                                                                    default => 'Non défini',
                                                                })
                                                                ->badge()
                                                                ->color(fn($state) => match ($state) {
                                                                    'FOURNI' => 'warning',
                                                                    'PAYE' => 'success',
                                                                    default => 'gray',
                                                                }),

                                                            TextEntry::make('declaration_reference')
                                                                ->label('Réf. déclaration')
                                                                ->placeholder('—')
                                                                ->copyable(),

                                                            TextEntry::make('status_douane')
                                                                ->label('Statut douane')
                                                                ->formatStateUsing(fn($state) => match ($state) {
                                                                    'EN_ATTENTE' => 'En attente',
                                                                    'EN_COURS' => 'En cours',
                                                                    'VALIDE' => 'Validé',
                                                                    default => $state ?? '—',
                                                                })
                                                                ->badge()
                                                                ->color(fn($state) => match ($state) {
                                                                    'EN_ATTENTE' => 'warning',
                                                                    'EN_COURS' => 'info',
                                                                    'VALIDE' => 'success',
                                                                    default => 'gray',
                                                                }),
                                                        ]),

                                                        Grid::make(2)->schema([
                                                            TextEntry::make('date_entree_douane')
                                                                ->label('Entrée douane')
                                                                ->date('d/m/Y')
                                                                ->placeholder('—')
                                                                ->icon('heroicon-o-arrow-left-circle'),

                                                            TextEntry::make('date_sortie_douane')
                                                                ->label('Sortie douane')
                                                                ->date('d/m/Y')
                                                                ->placeholder('—')
                                                                ->icon('heroicon-o-arrow-right-circle'),
                                                        ]),
                                                    ]),

                                                // Informations d'expertise (pour les châssis)
                                                Fieldset::make('Informations d\'expertise')
                                                    ->schema([
                                                        Grid::make(3)->schema([
                                                            // PVC
                                                            TextEntry::make('num_pvc')
                                                                ->label('N° PVC')
                                                                ->placeholder('—')
                                                                ->copyable()
                                                                ->badge()
                                                                ->color('purple'),

                                                            TextEntry::make('etat_pvc')
                                                                ->label('État PVC')
                                                                ->formatStateUsing(fn($state) => match ($state) {
                                                                    'EN_ATTENTE' => 'En attente',
                                                                    'VALIDE' => 'Validé',
                                                                    default => '—',
                                                                })
                                                                ->badge()
                                                                ->color(fn($state) => match ($state) {
                                                                    'EN_ATTENTE' => 'warning',
                                                                    'VALIDE' => 'success',
                                                                    default => 'gray',
                                                                }),

                                                            // AE
                                                            TextEntry::make('num_ae')
                                                                ->label('N° AE')
                                                                ->placeholder('—')
                                                                ->copyable()
                                                                ->badge()
                                                                ->color('purple'),

                                                            TextEntry::make('etat_ae')
                                                                ->label('État AE')
                                                                ->formatStateUsing(fn($state) => match ($state) {
                                                                    'EN_ATTENTE' => 'En attente',
                                                                    'VALIDE' => 'Validé',
                                                                    default => '—',
                                                                })
                                                                ->badge()
                                                                ->color(fn($state) => match ($state) {
                                                                    'EN_ATTENTE' => 'warning',
                                                                    'VALIDE' => 'success',
                                                                    default => 'gray',
                                                                }),

                                                            // CMC
                                                            TextEntry::make('num_cmc')
                                                                ->label('N° CMC')
                                                                ->placeholder('—')
                                                                ->copyable()
                                                                ->badge()
                                                                ->color('purple'),

                                                            TextEntry::make('etat_cmc')
                                                                ->label('État CMC')
                                                                ->formatStateUsing(fn($state) => match ($state) {
                                                                    'EN_ATTENTE' => 'En attente',
                                                                    'VALIDE' => 'Validé',
                                                                    default => '—',
                                                                })
                                                                ->badge()
                                                                ->color(fn($state) => match ($state) {
                                                                    'EN_ATTENTE' => 'warning',
                                                                    'VALIDE' => 'success',
                                                                    default => 'gray',
                                                                }),
                                                        ]),
                                                    ])
                                                    ->visible(fn($record) => in_array($record->type, ['CHASSIS', 'CHASSIS_VOITURE', 'CHASSIS_MACHINE'])),

                                                // Informations de livraison
                                                Fieldset::make('Informations de livraison')
                                                    ->schema([
                                                        Grid::make(3)->schema([
                                                            TextEntry::make('date_livraison')
                                                                ->label('Date livraison')
                                                                ->dateTime('d/m/Y H:i')
                                                                ->placeholder('Non livré')
                                                                ->icon('heroicon-o-truck'),

                                                            TextEntry::make('status_colis_livraison')
                                                                ->label('Statut livraison')
                                                                ->formatStateUsing(fn($state) => match ($state) {
                                                                    'EN_ATTENTE' => 'En attente',
                                                                    'EN_COURS' => 'En cours',
                                                                    'LIVRE' => 'Livré',
                                                                    default => '—',
                                                                })
                                                                ->badge()
                                                                ->color(fn($state) => match ($state) {
                                                                    'EN_ATTENTE' => 'warning',
                                                                    'EN_COURS' => 'info',
                                                                    'LIVRE' => 'success',
                                                                    default => 'gray',
                                                                }),

                                                            TextEntry::make('livraison_commentaire')
                                                                ->label('Commentaire')
                                                                ->placeholder('—')
                                                                ->limit(50)
                                                                ->columnSpanFull(),
                                                        ]),
                                                    ]),

                                                // Séparateur visuel
                                                TextEntry::make('separator')
                                                    ->label('')
                                                    ->getStateUsing(fn() => new HtmlString('<hr class="my-4 border-gray-300">'))
                                                    ->html()
                                                    ->columnSpanFull(),
                                            ])
                                            ->columns(1)
                                            ->contained(false)
                                            ->grid(1),
                                    ]),

                                // ✅ Résumé des statuts détaillé
                                Section::make('Statistiques détaillées')
                                    ->schema(function ($record) {
                                        $stats = $this->getUnitesStats($record);

                                        return [
                                            Grid::make(3)->schema([
                                                Fieldset::make('Par état')
                                                    ->schema([
                                                        TextEntry::make('stats_etats')
                                                            ->label('')
                                                            ->getStateUsing(function() use ($stats) {
                                                                return new HtmlString("
                                                                    <div class='space-y-2'>
                                                                        <div><span class='font-bold'>Au port:</span> {$stats['au_port']}</div>
                                                                        <div><span class='font-bold'>En douane:</span> {$stats['a_la_douane']}</div>
                                                                        <div><span class='font-bold'>En expertise:</span> {$stats['expertise']}</div>
                                                                        <div><span class='font-bold'>En route:</span> {$stats['en_route']}</div>
                                                                        <div><span class='font-bold'>Livré:</span> {$stats['livre']}</div>
                                                                    </div>
                                                                ");
                                                            })
                                                            ->html(),
                                                    ]),

                                                Fieldset::make('Douane T1')
                                                    ->schema([
                                                        TextEntry::make('stats_t1')
                                                            ->label('')
                                                            ->getStateUsing(function() use ($stats) {
                                                                return new HtmlString("
                                                                    <div class='space-y-2'>
                                                                        <div><span class='font-bold'>Fourni:</span> {$stats['t1_fourni']}</div>
                                                                        <div><span class='font-bold'>Payé:</span> {$stats['t1_paye']}</div>
                                                                    </div>
                                                                ");
                                                            })
                                                            ->html(),
                                                    ]),

                                                Fieldset::make('Expertise')
                                                    ->schema([
                                                        TextEntry::make('stats_expertise')
                                                            ->label('')
                                                            ->getStateUsing(function() use ($stats) {
                                                                return new HtmlString("
                                                                    <div class='space-y-2'>
                                                                        <div><span class='font-bold'>Avec PVC:</span> {$stats['avec_pvc']}</div>
                                                                        <div><span class='font-bold'>Avec AE:</span> {$stats['avec_ae']}</div>
                                                                        <div><span class='font-bold'>Avec CMC:</span> {$stats['avec_cmc']}</div>
                                                                        <div><span class='font-bold'>Complete:</span> {$stats['expertise_complete']}</div>
                                                                    </div>
                                                                ");
                                                            })
                                                            ->html(),
                                                    ]),
                                            ]),
                                        ];
                                    })
                                    ->visible(fn($record) => $record->unites->count() > 0),
                            ]),

                        // ===== ÉTAPE DOUANE =====
                        Tab::make('Douane')
                            ->icon('heroicon-o-document-magnifying-glass')
                            ->badge(fn($record) => $record->unites->whereNotNull('num_t1')->count())
                            ->badgeColor('info')
                            ->schema([
                                RepeatableEntry::make('unites')
                                    ->label('Unités avec informations douanières')
                                    ->schema([
                                        Grid::make(4)->schema([
                                            TextEntry::make('numero_chassis')
                                                ->label('Unité')
                                                ->getStateUsing(fn($record) =>
                                                    $record->numero_chassis ?? $record->numero_conteneur ?? 'N/A'
                                                )
                                                ->weight(FontWeight::Bold),

                                            TextEntry::make('num_t1')
                                                ->label('N° T1')
                                                ->placeholder('—')
                                                ->copyable()
                                                ->badge(),

                                            TextEntry::make('etat_t1')
                                                ->label('État')
                                                ->badge()
                                                ->formatStateUsing(fn($state) => match ($state) {
                                                    'FOURNI' => 'Fourni',
                                                    'PAYE' => 'Payé',
                                                    default => '—',
                                                })
                                                ->color(fn($state) => match ($state) {
                                                    'FOURNI' => 'warning',
                                                    'PAYE' => 'success',
                                                    default => 'gray',
                                                }),

                                            TextEntry::make('declaration_reference')
                                                ->label('Déclaration')
                                                ->placeholder('—')
                                                ->copyable(),
                                        ]),
                                    ])
                                    ->columns(1)
                                    ->contained(false)
                                    ->grid(2),
                            ]),

                        // ===== ÉTAPE EXPERTISE =====
                        Tab::make('Expertise')
                            ->icon('heroicon-o-clipboard-document-check')
                            ->badge(fn($record) => $record->unites->whereNotNull('num_pvc')->count())
                            ->badgeColor('purple')
                            ->visible(fn($record) => $this->isVehiculeType($record))
                            ->schema([
                                RepeatableEntry::make('unites')
                                    ->label('Unités avec informations d\'expertise')
                                    ->schema([
                                        Grid::make(4)->schema([
                                            TextEntry::make('numero_chassis')
                                                ->label('Châssis')
                                                ->weight(FontWeight::Bold),

                                            // PVC
                                            TextEntry::make('num_pvc')
                                                ->label('PVC')
                                                ->placeholder('—')
                                                ->badge()
                                                ->color(fn($state) => $state ? 'success' : 'gray'),

                                            // AE
                                            TextEntry::make('num_ae')
                                                ->label('AE')
                                                ->placeholder('—')
                                                ->badge()
                                                ->color(fn($state) => $state ? 'success' : 'gray'),

                                            // CMC
                                            TextEntry::make('num_cmc')
                                                ->label('CMC')
                                                ->placeholder('—')
                                                ->badge()
                                                ->color(fn($state) => $state ? 'success' : 'gray'),
                                        ]),

                                        Grid::make(3)->schema([
                                            TextEntry::make('etat_pvc')
                                                ->label('État PVC')
                                                ->badge()
                                                ->formatStateUsing(fn($state) => $state ?? '—')
                                                ->color(fn($state) => match ($state) {
                                                    'VALIDE' => 'success',
                                                    'EN_ATTENTE' => 'warning',
                                                    default => 'gray',
                                                }),

                                            TextEntry::make('etat_ae')
                                                ->label('État AE')
                                                ->badge()
                                                ->formatStateUsing(fn($state) => $state ?? '—')
                                                ->color(fn($state) => match ($state) {
                                                    'VALIDE' => 'success',
                                                    'EN_ATTENTE' => 'warning',
                                                    default => 'gray',
                                                }),

                                            TextEntry::make('etat_cmc')
                                                ->label('État CMC')
                                                ->badge()
                                                ->formatStateUsing(fn($state) => $state ?? '—')
                                                ->color(fn($state) => match ($state) {
                                                    'VALIDE' => 'success',
                                                    'EN_ATTENTE' => 'warning',
                                                    default => 'gray',
                                                }),
                                        ]),
                                    ])
                                    ->columns(1)
                                    ->contained(false)
                                    ->grid(1)
                                    ->visible(fn($record) => in_array($record->type, ['CHASSIS', 'CHASSIS_VOITURE', 'CHASSIS_MACHINE'])),
                            ]),

                        // ===== ÉTAPE LIVRAISON =====
                        Tab::make('Livraison')
                            ->icon('heroicon-o-truck')
                            ->badge(fn($record) => $record->unites->where('etat', 'LIVRE')->count())
                            ->badgeColor('success')
                            ->schema([
                                RepeatableEntry::make('unites')
                                    ->label('Suivi des livraisons')
                                    ->schema([
                                        Grid::make(4)->schema([
                                            TextEntry::make('numero_chassis')
                                                ->label('Unité')
                                                ->getStateUsing(fn($record) =>
                                                    $record->numero_chassis ?? $record->numero_conteneur ?? 'N/A'
                                                )
                                                ->weight(FontWeight::Bold),

                                            TextEntry::make('etat')
                                                ->label('État')
                                                ->badge()
                                                ->formatStateUsing(fn($state) => match ($state) {
                                                    'LIVRE' => 'Livré',
                                                    'EN_ROUTE' => 'En route',
                                                    default => 'En attente',
                                                })
                                                ->color(fn($state) => match ($state) {
                                                    'LIVRE' => 'success',
                                                    'EN_ROUTE' => 'primary',
                                                    default => 'warning',
                                                }),

                                            TextEntry::make('date_livraison')
                                                ->label('Date livraison')
                                                ->dateTime('d/m/Y H:i')
                                                ->placeholder('—'),

                                            TextEntry::make('status_colis_livraison')
                                                ->label('Statut')
                                                ->badge()
                                                ->formatStateUsing(fn($state) => match ($state) {
                                                    'LIVRE' => 'Livré',
                                                    'EN_COURS' => 'En cours',
                                                    'EN_ATTENTE' => 'En attente',
                                                    default => '—',
                                                })
                                                ->color(fn($state) => match ($state) {
                                                    'LIVRE' => 'success',
                                                    'EN_COURS' => 'info',
                                                    'EN_ATTENTE' => 'warning',
                                                    default => 'gray',
                                                }),
                                        ]),

                                        TextEntry::make('livraison_commentaire')
                                            ->label('Commentaire')
                                            ->placeholder('—')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(1)
                                    ->contained(false)
                                    ->grid(1),
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
                                                ->formatStateUsing(fn($state) => match ($state) {
                                                    'EN_COURS' => 'En cours',
                                                    'TERMINE' => 'Terminé',
                                                    default => $state ?? 'Non défini',
                                                })
                                                ->color(fn($state) => match ($state) {
                                                    'EN_COURS' => 'warning',
                                                    'TERMINE' => 'success',
                                                    default => 'gray',
                                                }),

                                            TextEntry::make('unites_livrees')
                                                ->label('Unités livrées')
                                                ->getStateUsing(fn($record) =>
                                                    $record->unites->where('etat', 'LIVRE')->count() .
                                                    ' / ' . $record->unites->count()
                                                )
                                                ->badge()
                                                ->color(fn($record) =>
                                                    $record->unites->where('etat', 'LIVRE')->count() === $record->unites->count()
                                                    ? 'success' : 'warning'
                                                ),
                                        ]),

                                        TextEntry::make('commentaires_cloture')
                                            ->label('Commentaires')
                                            ->markdown()
                                            ->columnSpanFull()
                                            ->visible(fn($record) => !empty($record->commentaires_cloture)),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
