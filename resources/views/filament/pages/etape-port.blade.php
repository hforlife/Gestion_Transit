<x-filament-panels::page>
    <div class="space-y-6">
        <!-- En-tête avec statistiques -->
        @php
            $stats = [
                'total' => App\Models\ColisUnite::whereNotNull('colis_id')->count(),
                'en_attente' => App\Models\ColisUnite::where('status_port', 'EN_ATTENTE')->count(),
                'entre' => App\Models\ColisUnite::where('status_port', 'ENTRE')->count(),
                'sorti' => App\Models\ColisUnite::where('status_port', 'SORTI')->count(),

                // Statistiques par type
                'conteneurs' => App\Models\ColisUnite::where('type', 'CONTENEUR')->count(),
                'chassis' => App\Models\ColisUnite::whereIn('type', ['CHASSIS', 'CHASSIS_VOITURE', 'CHASSIS_MACHINE'])->count(),
            ];
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total unités au port</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $stats['total'] }}
                        </p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-500/10 rounded-full">
                        <x-filament::icon icon="heroicon-o-cube" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
                <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    <span class="inline-block mr-3">📦 {{ $stats['conteneurs'] }} conteneurs</span>
                    <span class="inline-block">🚛 {{ $stats['chassis'] }} châssis</span>
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
                        <x-filament::icon icon="heroicon-o-clock" class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
                    </div>
                </div>
                <div class="mt-2 text-xs text-gray-500">
                    @if($stats['en_attente'] > 0)
                        <span class="text-yellow-600 dark:text-yellow-400">En attente de traitement</span>
                    @endif
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
                        <x-filament::icon icon="heroicon-o-arrow-left-circle" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
                <div class="mt-2 text-xs text-gray-500">
                    @if($stats['entre'] > 0)
                        <span class="text-blue-600 dark:text-blue-400">En cours de dédouanement</span>
                    @endif
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Sortis</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                            {{ $stats['sorti'] }}
                        </p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-500/10 rounded-full">
                        <x-filament::icon icon="heroicon-o-check-circle" class="w-6 h-6 text-green-600 dark:text-green-400" />
                    </div>
                </div>
                <div class="mt-2 text-xs text-gray-500">
                    @if($stats['sorti'] > 0)
                        <span class="text-green-600 dark:text-green-400">Prêts pour la douane</span>
                    @endif
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
