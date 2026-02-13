<x-filament-panels::page>
    <div class="space-y-6">
        <!-- En-tête avec statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total colis au port</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ \App\Models\Colis::whereHas('port')->count() }}
                        </p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-500/10 rounded-full">
                        <x-filament::icon icon="heroicon-o-cube" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">En attente</p>
                        <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                            {{ \App\Models\Colis::whereHas('port')->where('status_colis_port', 'EN_ATTENTE')->count() }}
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
                        <p class="text-sm text-gray-500 dark:text-gray-400">En cours</p>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                            {{ \App\Models\Colis::whereHas('port')->whereIn('status_colis_port', ['EN_COURS', 'CHARGEMENT', 'DECHARGEMENT'])->count() }}
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
                            {{ \App\Models\Colis::whereHas('port')->where('status_colis_port', 'SORTI')->count() }}
                        </p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-500/10 rounded-full">
                        <x-filament::icon icon="heroicon-o-check-circle" class="w-6 h-6 text-green-600 dark:text-green-400" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres rapides -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Filtres rapides</h3>
                <span class="text-xs text-gray-500 dark:text-gray-400">Cliquez pour filtrer</span>
            </div>
            <div class="flex flex-wrap gap-2">
                @php
                    $ports = \App\Models\Port::take(5)->get();
                @endphp
                @foreach($ports as $port)
                    <button 
                        onclick="window.location.href='{{ request()->fullUrlWithQuery(['tableFilters' => ['port' => ['value' => $port->id]]]) }}'"
                        class="px-3 py-1.5 text-xs font-medium rounded-full border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700"
                    >
                        {{ $port->nom }}
                    </button>
                @endforeach
                <button 
                    onclick="window.location.href='{{ request()->url() }}'"
                    class="px-3 py-1.5 text-xs font-medium rounded-full border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700"
                >
                    Réinitialiser
                </button>
            </div>
        </div>

        <!-- Tableau des colis -->
        {{ $this->table }}
    </div>
</x-filament-panels::page>

@push('scripts')
<script>
    // Actualisation automatique toutes les 30 secondes (optionnel)
    setInterval(function() {
        Livewire.dispatch('refresh');
    }, 30000);
</script>
@endpush