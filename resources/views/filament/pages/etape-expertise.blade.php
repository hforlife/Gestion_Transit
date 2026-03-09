<x-filament-panels::page>
    <div class="space-y-6">
        <!-- En-tête avec statistiques -->
        @php
            $stats = [
                'total' => App\Models\ColisUnite::count(),
                'en_route' => App\Models\ColisUnite::where('etat', 'EN_ROUTE')->count(),
                'livre' => App\Models\ColisUnite::where('etat', 'LIVRE')->count(),
                'au_port' => App\Models\ColisUnite::where('etat', 'AU_PORT')->count(),
                'a_la_douane' => App\Models\ColisUnite::where('etat', 'A_LA_DOUANE')->count(),
                'expertise' => App\Models\ColisUnite::where('etat', 'EXPERTISE')->count(),
                
                // Statistiques par type
                'conteneurs' => App\Models\ColisUnite::where('type', 'CONTENEUR')->count(),
                'chassis' => App\Models\ColisUnite::whereIn('type', ['CHASSIS', 'CHASSIS_VOITURE', 'CHASSIS_MACHINE'])->count(),
                
                // Statistiques avancées
                'aujourdhui' => App\Models\ColisUnite::whereDate('date_livraison', today())->count(),
                'avec_date_livraison' => App\Models\ColisUnite::whereNotNull('date_livraison')->count(),
            ];
            
            $totalNonLivre = $stats['en_route'] + $stats['au_port'] + $stats['a_la_douane'] + $stats['expertise'];
            $tauxReussite = $stats['total'] > 0 
                ? round(($stats['livre'] / $stats['total']) * 100) 
                : 0;
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total unités</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $stats['total'] }}
                        </p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-500/10 rounded-full">
                        <x-filament::icon icon="heroicon-o-cube" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
                <div class="mt-2 text-xs text-gray-500">
                    <span>📦 {{ $stats['conteneurs'] }} conteneurs</span>
                    <span class="ml-2">🚛 {{ $stats['chassis'] }} châssis</span>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Au port</p>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                            {{ $stats['au_port'] }}
                        </p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-500/10 rounded-full">
                        {{-- <x-filament::icon icon="heroicon-o-anchor" class="w-6 h-6 text-blue-600 dark:text-blue-400" /> --}}
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">En douane</p>
                        <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                            {{ $stats['a_la_douane'] }}
                        </p>
                    </div>
                    <div class="p-3 bg-purple-100 dark:bg-purple-500/10 rounded-full">
                        <x-filament::icon icon="heroicon-o-building-library" class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Expertise</p>
                        <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                            {{ $stats['expertise'] }}
                        </p>
                    </div>
                    <div class="p-3 bg-orange-100 dark:bg-orange-500/10 rounded-full">
                        <x-filament::icon icon="heroicon-o-clipboard-document-check" class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">En route</p>
                        <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                            {{ $stats['en_route'] }}
                        </p>
                    </div>
                    <div class="p-3 bg-yellow-100 dark:bg-yellow-500/10 rounded-full">
                        <x-filament::icon icon="heroicon-o-truck" class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Deuxième ligne : Livraisons -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Livrées</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                            {{ $stats['livre'] }}
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
                        <p class="text-sm text-gray-500 dark:text-gray-400">Avec date livraison</p>
                        <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                            {{ $stats['avec_date_livraison'] }}
                        </p>
                    </div>
                    <div class="p-3 bg-indigo-100 dark:bg-indigo-500/10 rounded-full">
                        <x-filament::icon icon="heroicon-o-calendar" class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Livrées aujourd'hui</p>
                        <p class="text-2xl font-bold text-teal-600 dark:text-teal-400">
                            {{ $stats['aujourdhui'] }}
                        </p>
                    </div>
                    <div class="p-3 bg-teal-100 dark:bg-teal-500/10 rounded-full">
                        <x-filament::icon icon="heroicon-o-calendar-days" class="w-6 h-6 text-teal-600 dark:text-teal-400" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques avancées -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Performance livraison</h3>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Taux de livraison</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $tauxReussite }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                        <div class="bg-green-600 h-2.5 rounded-full" style="width: {{ $tauxReussite }}%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500">
                        <span>{{ $stats['livre'] }} livrées</span>
                        <span>{{ $totalNonLivre }} en cours</span>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Livraisons du jour</h3>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Aujourd'hui</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $stats['aujourdhui'] }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Total en cours</span>
                        <span class="font-medium text-yellow-600">{{ $totalNonLivre }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Répartition</h3>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Conteneurs</span>
                        <span class="font-medium text-blue-600">{{ $stats['conteneurs'] }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Châssis</span>
                        <span class="font-medium text-orange-600">{{ $stats['chassis'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableau des unités -->
        {{ $this->table }}
    </div>

    @push('scripts')
    <script>
        // Actualisation automatique toutes les 30 secondes
        setInterval(function() {
            Livewire.dispatch('refresh');
        }, 30000);
    </script>
    @endpush
</x-filament-panels::page>