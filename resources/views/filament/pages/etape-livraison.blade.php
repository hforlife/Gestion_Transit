<x-filament-panels::page>
    <div class="space-y-6">
        <!-- En-tête avec statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total livraisons</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ \App\Models\Colis::whereNotNull('status_colis_livraison')->count() }}
                        </p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-500/10 rounded-full">
                        <x-filament::icon icon="heroicon-o-truck" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">En attente</p>
                        <p class="text-2xl font-bold text-gray-600 dark:text-gray-400">
                            {{ \App\Models\Colis::where('status_colis_livraison', 'EN_ATTENTE')->count() }}
                        </p>
                    </div>
                    <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-full">
                        <x-filament::icon icon="heroicon-o-cube" class="w-6 h-6 text-gray-600 dark:text-gray-400" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Livrés</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                            {{ \App\Models\Colis::where('status_colis_livraison', 'LIVRE')->count() }}
                        </p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-500/10 rounded-full">
                        <x-filament::icon icon="heroicon-o-check-circle" class="w-6 h-6 text-green-600 dark:text-green-400" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques avancées -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Performance livraison</h3>
                @php
                    $totalLivres = \App\Models\Colis::where('status_colis_livraison', 'LIVRE')->count();
                    $totalEchecs = \App\Models\Colis::whereIn('status_colis_livraison', ['ECHEC', 'ANNULE'])->count();
                    $total = $totalLivres + $totalEchecs;
                    $tauxReussite = $total > 0 ? round(($totalLivres / $total) * 100) : 0;
                @endphp
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Taux de réussite</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $tauxReussite }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                        <div class="bg-green-600 h-2.5 rounded-full" style="width: {{ $tauxReussite }}%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500">
                        <span>{{ $totalLivres }} livrés</span>
                        <span>{{ $totalEchecs }} échecs</span>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Livraisons du jour</h3>
                @php
                    $aujourdhui = \App\Models\Colis::whereDate('date_livraison', today())->count();
                    $prevu = \App\Models\Colis::where('status_colis_livraison', 'EN_ATTENTE')->count();
                @endphp
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Aujourd'hui</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $aujourdhui }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">En attente</span>
                        <span class="font-medium text-yellow-600">{{ $prevu }}</span>
                    </div>
                </div>
            </div>

            {{-- <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Problèmes</h3>
                @php
                    $echecs = \App\Models\Colis::where('status_colis_livraison', 'ECHEC')->count();
                    $retards = \App\Models\Colis::where('status_colis_livraison', 'EN_COURS_LIVRAISON')
                        ->where('created_at', '<', now()->subDays(3))
                        ->count();
                @endphp
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Échecs</span>
                        <span class="font-medium text-red-600">{{ $echecs }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Retards</span>
                        <span class="font-medium text-orange-600">{{ $retards }}</span>
                    </div>
                </div>
            </div> --}}
        </div>

        <!-- Alertes -->
        {{-- @php
            $echecsRecents = \App\Models\Colis::where('status_colis_livraison', 'ECHEC')
                ->where('updated_at', '>', now()->subDays(2))
                ->count();
            $retardsCritiques = \App\Models\Colis::where('status_colis_livraison', 'EN_COURS_LIVRAISON')
                ->where('created_at', '<', now()->subDays(5))
                ->count();
        @endphp

        @if($echecsRecents > 0 || $retardsCritiques > 0)
            <div class="bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 rounded-lg p-4">
                <div class="flex items-center gap-3">
                    <x-filament::icon icon="heroicon-o-exclamation-triangle" class="w-5 h-5 text-red-600 dark:text-red-400" />
                    <div>
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-300">Alertes livraison</h3>
                        <p class="text-sm text-red-700 dark:text-red-400">
                            @if($echecsRecents > 0) {{ $echecsRecents }} échec(s) de livraison récent(s). @endif
                            @if($retardsCritiques > 0) {{ $retardsCritiques }} livraison(s) en retard (>5 jours). @endif
                        </p>
                    </div>
                </div>
            </div>
        @endif --}}

        <!-- Filtres rapides -->
        {{-- <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Filtres rapides</h3>
                <span class="text-xs text-gray-500 dark:text-gray-400">Cliquez pour filtrer</span>
            </div>
            <div class="flex flex-wrap gap-2">
                <button 
                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['tableFilters' => ['status_colis_livraison' => ['values' => ['EN_PREPARATION']]]]) }}'"
                    class="px-3 py-1.5 text-xs font-medium rounded-full border border-gray-200 bg-gray-50 text-gray-700 hover:bg-gray-100"
                >
                    En préparation
                </button>
                <button 
                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['tableFilters' => ['status_colis_livraison' => ['values' => ['PRET']]]]) }}'"
                    class="px-3 py-1.5 text-xs font-medium rounded-full border border-blue-200 bg-blue-50 text-blue-700 hover:bg-blue-100"
                >
                    Prêts
                </button>
                <button 
                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['tableFilters' => ['status_colis_livraison' => ['values' => ['EN_COURS_LIVRAISON']]]]) }}'"
                    class="px-3 py-1.5 text-xs font-medium rounded-full border border-yellow-200 bg-yellow-50 text-yellow-700 hover:bg-yellow-100"
                >
                    En cours
                </button>
                <button 
                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['tableFilters' => ['status_colis_livraison' => ['values' => ['LIVRE']]]]) }}'"
                    class="px-3 py-1.5 text-xs font-medium rounded-full border border-green-200 bg-green-50 text-green-700 hover:bg-green-100"
                >
                    Livrés
                </button>
                <button 
                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['tableFilters' => ['status_colis_livraison' => ['values' => ['ECHEC']]]]) }}'"
                    class="px-3 py-1.5 text-xs font-medium rounded-full border border-red-200 bg-red-50 text-red-700 hover:bg-red-100"
                >
                    Échecs
                </button>
                <button 
                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['tableFilters' => ['en_retard' => true]]) }}'"
                    class="px-3 py-1.5 text-xs font-medium rounded-full border border-orange-200 bg-orange-50 text-orange-700 hover:bg-orange-100"
                >
                    En retard
                </button>
                <button 
                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['tableFilters' => ['date_livraison' => ['livraison_from' => now()->format('Y-m-d'), 'livraison_until' => now()->format('Y-m-d')]]]) }}'"
                    class="px-3 py-1.5 text-xs font-medium rounded-full border border-purple-200 bg-purple-50 text-purple-700 hover:bg-purple-100"
                >
                    Aujourd'hui
                </button>
                <button 
                    onclick="window.location.href='{{ request()->url() }}'"
                    class="px-3 py-1.5 text-xs font-medium rounded-full border border-gray-300 bg-gray-100 text-gray-700"
                >
                    Réinitialiser
                </button>
            </div>
        </div> --}}

        <!-- Tableau des colis -->
        {{ $this->table }}
    </div>
</x-filament-panels::page>