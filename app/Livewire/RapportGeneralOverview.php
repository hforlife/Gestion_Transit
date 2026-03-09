<?php

namespace App\Livewire;

use App\Models\Colis;
use App\Models\Client;
use App\Models\DossierTransit;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;

class RapportGeneralOverview extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected function getColumns(): int
    {
        return 4;
    }

    protected function getStats(): array
    {
        $dateDebut = $this->pageFilters['date_debut'] ?? null;
        $dateFin = $this->pageFilters['date_fin'] ?? null;
        $portId = $this->pageFilters['port_id'] ?? null;
        $typeDossierId = $this->pageFilters['type_dossier_id'] ?? null;
        $typeColisId = $this->pageFilters['type_colis_id'] ?? null;

        // Information sur les filtres
        $filtreInfo = $this->getFiltreInfo($dateDebut, $dateFin, $portId, $typeDossierId, $typeColisId);

        /**
         * =========================
         * 📁 STATISTIQUES DOSSIERS
         * =========================
         */
        $queryDossiers = DossierTransit::query()
            ->when($dateDebut, fn ($q) => $q->whereDate('date_depot', '>=', $dateDebut))
            ->when($dateFin, fn ($q) => $q->whereDate('date_depot', '<=', $dateFin))
            ->when($typeDossierId, fn ($q) => $q->where('id_type_dossier', $typeDossierId));

        $totalDossiers = $queryDossiers->count();
        $dossiersParstatus = $queryDossiers->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $dossiersCompletes = $dossiersParstatus['COMPLETE'] ?? 0;
        $dossiersEnCours = $dossiersParstatus['EN_COURS'] ?? 0;
        $dossiersEnAttente = $dossiersParstatus['EN_ATTENTE'] ?? 0;

        $tauxCompletion = $totalDossiers > 0 
            ? round(($dossiersCompletes / $totalDossiers) * 100, 1)
            : 0;

        /**
         * =========================
         * 📦 STATISTIQUES COLIS
         * =========================
         */
        $queryColis = Colis::query()
            ->when($dateDebut, fn ($q) => $q->whereDate('created_at', '>=', $dateDebut))
            ->when($dateFin, fn ($q) => $q->whereDate('created_at', '<=', $dateFin))
            ->when($portId, fn ($q) => $q->where('id_port', $portId))
            ->when($typeColisId, fn ($q) => $q->where('id_type_colis', $typeColisId));

        $totalColis = $queryColis->count();
        
        // Statistiques des unités
        $statsUnites = $this->getStatsUnites($queryColis);
        
        $totalUnites = $statsUnites['total'] ?? 0;
        $unitesLivrees = $statsUnites['LIVRE'] ?? 0;
        $unitesEnCours = $totalUnites - $unitesLivrees;

        $tauxLivraison = $totalUnites > 0 
            ? round(($unitesLivrees / $totalUnites) * 100, 1)
            : 0;

        /**
         * =========================
         * 👥 STATISTIQUES CLIENTS
         * =========================
         */
        $clientsActifs = Client::whereHas('dossierTransit', function ($q) use ($dateDebut, $dateFin) {
            $q->when($dateDebut, fn ($q) => $q->whereDate('date_depot', '>=', $dateDebut))
              ->when($dateFin, fn ($q) => $q->whereDate('date_depot', '<=', $dateFin));
        })->count();

        $nouveauxClients = Client::whereDate('created_at', '>=', $dateDebut ?? now()->subMonth())
            ->whereDate('created_at', '<=', $dateFin ?? now())
            ->count();

        /**
         * =========================
         * 📊 TENDANCES
         * =========================
         */
        $tendances = $this->getTendances($dateDebut, $dateFin, $portId, $typeDossierId);

        $stats = [];

        // Information sur les filtres
        if ($filtreInfo) {
            $stats[] = Stat::make('Filtres appliqués', $filtreInfo)
                ->description('Période et critères sélectionnés')
                ->descriptionIcon('heroicon-m-funnel')
                ->color('gray')
                ->extraAttributes(['class' => 'col-span-4 text-sm']);
        }

        // Statistiques principales
        $stats = array_merge($stats, [
            // DOSSIERS
            Stat::make('Total Dossiers', $totalDossiers)
                ->description($dossiersCompletes . ' complétés, ' . $dossiersEnCours . ' en cours')
                //->descriptionIcon('heroicon-m-folder')
                ->color('primary')
                ->chart([$dossiersCompletes, $dossiersEnCours, $dossiersEnAttente]),

            Stat::make('Taux de complétion', $tauxCompletion . '%')
                ->description($dossiersCompletes . ' dossiers complétés')
                //->descriptionIcon('heroicon-m-check-badge')
                ->color($tauxCompletion >= 70 ? 'success' : ($tauxCompletion >= 30 ? 'warning' : 'danger')),

            // COLIS
            Stat::make('Total Colis', $totalColis)
                ->description($statsUnites['total'] ?? 0 . ' unités au total')
                //->descriptionIcon('heroicon-m-cube')
                ->color('info'),

            Stat::make('Unités livrées', $unitesLivrees . '/' . $totalUnites)
                ->description($tauxLivraison . '% des unités livrées')
                //->descriptionIcon('heroicon-m-check-circle')
                ->color($tauxLivraison >= 80 ? 'success' : ($tauxLivraison >= 40 ? 'warning' : 'danger')),

            // ÉTATS DES UNITÉS
            Stat::make('Unités au port', $statsUnites['AU_PORT'] ?? 0)
                ->description('En attente de traitement')
                //->descriptionIcon('heroicon-m-anchor')
                ->color('warning'),

            Stat::make('Unités à la douane', $statsUnites['A_LA_DOUANE'] ?? 0)
                ->description('En cours de dédouanement')
                //->descriptionIcon('heroicon-m-document-text')
                ->color('info'),

            Stat::make('Unités en route', $statsUnites['EN_ROUTE'] ?? 0)
                ->description('En cours de livraison')
                //->descriptionIcon('heroicon-m-truck')
                ->color('primary'),

            Stat::make('Unités en expertise', $statsUnites['EXPERTISE'] ?? 0)
                ->description('En cours d\'expertise')
               // ->descriptionIcon('heroicon-m-magnifying-glass')
                ->color('danger'),

            // CLIENTS
            Stat::make('Clients actifs', $clientsActifs)
                ->description($nouveauxClients . ' nouveaux clients')
               // ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            // TENDANCES
            Stat::make('Tendance dossiers', $tendances['dossiers'] . '%')
                ->description('vs période précédente')
                //->descriptionIcon($tendances['dossiers'] >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($tendances['dossiers'] >= 0 ? 'success' : 'danger'),

            Stat::make('Tendance colis', $tendances['colis'] . '%')
                ->description('vs période précédente')
                //->descriptionIcon($tendances['colis'] >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($tendances['colis'] >= 0 ? 'success' : 'danger'),
        ]);

        return $stats;
    }

    /**
     * Calcule les statistiques des unités pour tous les colis
     */
    protected function getStatsUnites($queryColis): array
    {
        $stats = [
            'AU_PORT' => 0,
            'A_LA_DOUANE' => 0,
            'EXPERTISE' => 0,
            'EN_ROUTE' => 0,
            'LIVRE' => 0,
            'total' => 0,
        ];

        $colis = $queryColis->with('unites')->get();

        foreach ($colis as $c) {
            $unitesStats = $c->statsUnites();
            foreach ($unitesStats as $key => $value) {
                $stats[$key] = ($stats[$key] ?? 0) + $value;
                $stats['total'] += $value;
            }
        }

        return $stats;
    }

    /**
     * Calcule les tendances par rapport à la période précédente
     */
    protected function getTendances($dateDebut, $dateFin, $portId, $typeDossierId): array
    {
        if (!$dateDebut || !$dateFin) {
            return ['dossiers' => 0, 'colis' => 0];
        }

        // Période actuelle
        $debut = \Carbon\Carbon::parse($dateDebut);
        $fin = \Carbon\Carbon::parse($dateFin);
        $duree = $debut->diffInDays($fin);

        // Période précédente (même durée)
        $debutPrec = $debut->copy()->subDays($duree + 1);
        $finPrec = $fin->copy()->subDays($duree + 1);

        // Dossiers période actuelle
        $dossiersActuels = DossierTransit::whereBetween('date_depot', [$debut, $fin])
            ->when($typeDossierId, fn ($q) => $q->where('id_type_dossier', $typeDossierId))
            ->count();

        // Dossiers période précédente
        $dossiersPrec = DossierTransit::whereBetween('date_depot', [$debutPrec, $finPrec])
            ->when($typeDossierId, fn ($q) => $q->where('id_type_dossier', $typeDossierId))
            ->count();

        // Colis période actuelle
        $colisActuels = Colis::whereBetween('created_at', [$debut, $fin])
            ->when($portId, fn ($q) => $q->where('id_port', $portId))
            ->count();

        // Colis période précédente
        $colisPrec = Colis::whereBetween('created_at', [$debutPrec, $finPrec])
            ->when($portId, fn ($q) => $q->where('id_port', $portId))
            ->count();

        $tendanceDossiers = $dossiersPrec > 0 
            ? round((($dossiersActuels - $dossiersPrec) / $dossiersPrec) * 100, 1)
            : ($dossiersActuels > 0 ? 100 : 0);

        $tendanceColis = $colisPrec > 0 
            ? round((($colisActuels - $colisPrec) / $colisPrec) * 100, 1)
            : ($colisActuels > 0 ? 100 : 0);

        return [
            'dossiers' => $tendanceDossiers,
            'colis' => $tendanceColis,
        ];
    }

    protected function getFiltreInfo($dateDebut, $dateFin, $portId, $typeDossierId, $typeColisId): ?string
    {
        $infos = [];
        
        if ($dateDebut && $dateFin) {
            $infos[] = 'Période: ' . \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') . 
                       ' - ' . \Carbon\Carbon::parse($dateFin)->format('d/m/Y');
        } elseif ($dateDebut) {
            $infos[] = 'À partir du: ' . \Carbon\Carbon::parse($dateDebut)->format('d/m/Y');
        } elseif ($dateFin) {
            $infos[] = 'Jusqu\'au: ' . \Carbon\Carbon::parse($dateFin)->format('d/m/Y');
        }
        
        if ($portId) {
            $port = \App\Models\Port::find($portId);
            if ($port) {
                $infos[] = 'Port: ' . $port->nom;
            }
        }
        
        if ($typeDossierId) {
            $type = \App\Models\TypeDossier::find($typeDossierId);
            if ($type) {
                $infos[] = 'Type dossier: ' . $type->nom;
            }
        }
        
        if ($typeColisId) {
            $type = \App\Models\TypeColis::find($typeColisId);
            if ($type) {
                $infos[] = 'Type colis: ' . $type->nom;
            }
        }
        
        return !empty($infos) ? implode(' | ', $infos) : null;
    }

    protected function getListeners(): array
    {
        return [
            'filters-reset' => '$refresh',
        ];
    }
}