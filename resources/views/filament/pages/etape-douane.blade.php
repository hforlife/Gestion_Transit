<x-filament-panels::page>
    <div class="space-y-6">
        <!-- En-tête avec statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total à la douane</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ \App\Models\Colis::whereNotNull('status_colis_douane')->count() }}
                        </p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-500/10 rounded-full">
                        <x-filament::icon icon="heroicon-o-building-library" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">En attente</p>
                        <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                            {{ \App\Models\Colis::where('status_colis_douane', 'EN_ATTENTE')->count() }}
                        </p>
                    </div>
                    <div class="p-3 bg-yellow-100 dark:bg-yellow-500/10 rounded-full">
                        <x-filament::icon icon="heroicon-o-clock" class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Entrée</p>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                            {{ \App\Models\Colis::whereIn('status_colis_douane', ['ENTRE'])->count() }}
                        </p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-500/10 rounded-full">
                        <x-filament::icon icon="heroicon-o-arrow-path" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Sortis</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                            {{ \App\Models\Colis::where('status_colis_douane', 'SORTI')->count() }}
                        </p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-500/10 rounded-full">
                        <x-filament::icon icon="heroicon-o-check-circle" class="w-6 h-6 text-green-600 dark:text-green-400" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Bloqués</p>
                        <p class="text-2xl font-bold text-red-600 dark:text-red-400">
                            {{ \App\Models\Colis::where('status_colis_douane', 'BLOQUE')->count() }}
                        </p>
                    </div>
                    <div class="p-3 bg-red-100 dark:bg-red-500/10 rounded-full">
                        <x-filament::icon icon="heroicon-o-exclamation-triangle" class="w-6 h-6 text-red-600 dark:text-red-400" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Alertes et notifications -->
        @php
            $bloques = \App\Models\Colis::where('status_colis_douane', 'ENTRE')->count();
            $retards = \App\Models\Colis::whereNotNull('date_entree_douane')
                ->whereNull('date_sortie_douane')
                ->where('date_entree_douane', '<', now()->subDays(7))
                ->count();
        @endphp

        @if($bloques > 0 || $retards > 0)
            <div class="bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 rounded-lg p-4">
                <div class="flex items-center gap-3">
                    <x-filament::icon icon="heroicon-o-exclamation-triangle" class="w-5 h-5 text-red-600 dark:text-red-400" />
                    <div>
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-300">Alertes douane</h3>
                        <p class="text-sm text-red-700 dark:text-red-400">
                            @if($bloques > 0) {{ $bloques }} colis entré en douane. @endif
                            @if($retards > 0) {{ $retards }} colis en retard de sortie (>7 jours). @endif
                        </p>
                    </div>
                </div>
            </div>
        @endif

        {{-- <!-- Filtres rapides -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Filtres rapides</h3>
                <span class="text-xs text-gray-500 dark:text-gray-400">Cliquez pour filtrer</span>
            </div>
            <div class="flex flex-wrap gap-2">
                <button 
                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['tableFilters' => ['status_colis_douane' => ['values' => ['EN_ATTENTE']]]]) }}'"
                    class="px-3 py-1.5 text-xs font-medium rounded-full border border-yellow-200 bg-yellow-50 text-yellow-700 hover:bg-yellow-100"
                >
                    En attente
                </button>
                <button 
                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['tableFilters' => ['status_colis_douane' => ['values' => ['EN_COURS', 'ENTRE']]]]) }}'"
                    class="px-3 py-1.5 text-xs font-medium rounded-full border border-blue-200 bg-blue-50 text-blue-700 hover:bg-blue-100"
                >
                    En cours
                </button>
                <button 
                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['tableFilters' => ['status_colis_douane' => ['values' => ['SORTI']]]]) }}'"
                    class="px-3 py-1.5 text-xs font-medium rounded-full border border-green-200 bg-green-50 text-green-700 hover:bg-green-100"
                >
                    Sortis
                </button>
                <button 
                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['tableFilters' => ['status_colis_douane' => ['values' => ['BLOQUE']]]]) }}'"
                    class="px-3 py-1.5 text-xs font-medium rounded-full border border-red-200 bg-red-50 text-red-700 hover:bg-red-100"
                >
                    Bloqués
                </button>
                <button 
                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['tableFilters' => ['etat_t1' => ['values' => ['NON_FOURNI']]]]) }}'"
                    class="px-3 py-1.5 text-xs font-medium rounded-full border border-gray-200 bg-gray-50 text-gray-700 hover:bg-gray-100"
                >
                    T1 non fourni
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