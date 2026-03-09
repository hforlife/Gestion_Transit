<x-filament-panels::page>
    <div class="space-y-6">
        <!-- En-tête avec statistiques -->
        @php
            $stats = [
                'total' => \App\Models\ColisUnite::whereNotNull('status_douane')->count(),
                'en_attente' => \App\Models\ColisUnite::where('status_douane', 'EN_ATTENTE')->count(),
                'entre' => \App\Models\ColisUnite::where('status_douane', 'ENTRE')->count(),
                'sorti' => \App\Models\ColisUnite::where('status_douane', 'SORTI')->count(),
                'bloque' => \App\Models\ColisUnite::where('status_douane', 'BLOQUE')->count(),

                't1_fourni' => \App\Models\ColisUnite::where('etat_t1', 'FOURNI')->count(),
                't1_paye' => \App\Models\ColisUnite::where('etat_t1', 'PAYE')->count(),
                'sans_t1' => \App\Models\ColisUnite::whereNull('num_t1')->count(),

                'entrees_sans_sortie' => \App\Models\ColisUnite::whereNotNull('date_entree_douane')
                    ->whereNull('date_sortie_douane')
                    ->where('date_entree_douane', '<', now()->subDays(7))
                    ->count(),
            ];
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
                        <x-filament::icon icon="heroicon-o-building-library"
                            class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">En attente</p>
                        <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                            {{ $stats['en_attente'] }}
                        </p>
                    </div>
                    <div class="p-3 bg-yellow-100 dark:bg-yellow-500/10 rounded-full">
                        <x-filament::icon icon="heroicon-o-clock"
                            class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Entrées</p>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                            {{ $stats['entre'] }}
                        </p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-500/10 rounded-full">
                        <x-filament::icon icon="heroicon-o-arrow-left-circle"
                            class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Sorties</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                            {{ $stats['sorti'] }}
                        </p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-500/10 rounded-full">
                        <x-filament::icon icon="heroicon-o-check-circle"
                            class="w-6 h-6 text-green-600 dark:text-green-400" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Bloqués</p>
                        <p class="text-2xl font-bold text-red-600 dark:text-red-400">
                            {{ $stats['bloque'] }}
                        </p>
                    </div>
                    <div class="p-3 bg-red-100 dark:bg-red-500/10 rounded-full">
                        <x-filament::icon icon="heroicon-o-exclamation-triangle"
                            class="w-6 h-6 text-red-600 dark:text-red-400" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques T1 -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">T1 Fourni</p>
                        <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                            {{ $stats['t1_fourni'] }}
                        </p>
                    </div>
                    <div class="p-3 bg-orange-100 dark:bg-orange-500/10 rounded-full">
                        <x-filament::icon icon="heroicon-o-document-plus"
                            class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">T1 Payé</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                            {{ $stats['t1_paye'] }}
                        </p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-500/10 rounded-full">
                        <x-filament::icon icon="heroicon-o-check-badge"
                            class="w-6 h-6 text-green-600 dark:text-green-400" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Sans T1</p>
                        <p class="text-2xl font-bold text-gray-600 dark:text-gray-400">
                            {{ $stats['sans_t1'] }}
                        </p>
                    </div>
                    <div class="p-3 bg-gray-100 dark:bg-gray-500/10 rounded-full">
                        <x-filament::icon icon="heroicon-o-x-circle" class="w-6 h-6 text-gray-600 dark:text-gray-400" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Alertes et notifications -->
        @if ($stats['entrees_sans_sortie'] > 0)
            <div class="bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 rounded-lg p-4">
                <div class="flex items-center gap-3">
                    <x-filament::icon icon="heroicon-o-exclamation-triangle"
                        class="w-5 h-5 text-red-600 dark:text-red-400" />
                    <div>
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-300">Alertes douane</h3>
                        <p class="text-sm text-red-700 dark:text-red-400">
                            {{ $stats['entrees_sans_sortie'] }} unité(s) en douane depuis plus de 7 jours.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Types d'unités -->
        @php
            $types = [
                'CONTENEUR' => [
                    'label' => 'Conteneurs',
                    'color' => 'blue',
                    'count' => \App\Models\ColisUnite::where('type', 'CONTENEUR')->count(),
                ],
                'CHASSIS' => [
                    'label' => 'Châssis',
                    'color' => 'yellow',
                    'count' => \App\Models\ColisUnite::where('type', 'CHASSIS')->count(),
                ],
                'CHASSIS_VOITURE' => [
                    'label' => 'Châssis Voiture',
                    'color' => 'green',
                    'count' => \App\Models\ColisUnite::where('type', 'CHASSIS_VOITURE')->count(),
                ],
                'CHASSIS_MACHINE' => [
                    'label' => 'Châssis Machine',
                    'color' => 'red',
                    'count' => \App\Models\ColisUnite::where('type', 'CHASSIS_MACHINE')->count(),
                ],
            ];
        @endphp

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Types d'unités</h3>
            </div>
            <div class="flex flex-wrap gap-2">
                @foreach ($types as $value => $type)
                    @if ($type['count'] > 0)
                        <button
                            onclick="window.location.href='{{ request()->fullUrlWithQuery(['tableFilters' => ['type' => ['values' => [$value]]]]) }}'"
                            class="px-3 py-1.5 text-xs font-medium rounded-full border border-{{ $type['color'] }}-200 bg-{{ $type['color'] }}-50 text-{{ $type['color'] }}-700 hover:bg-{{ $type['color'] }}-100">
                            {{ $type['label'] }} ({{ $type['count'] }})
                        </button>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Tableau des unités -->
        {{ $this->table }}
    </div>
</x-filament-panels::page>
