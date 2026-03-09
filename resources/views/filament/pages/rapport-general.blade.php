<x-filament-panels::page>
    <!-- <div class="space-y-6"> -->
        <!-- Formulaire de filtres -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-medium mb-4">Filtres du rapport</h3>
            {{ $this->filtersForm }}
        </div>

       

        <!-- Note d'information -->
        @if(!$this->hasActiveFilters())
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <div class="flex items-center gap-3">
                    <x-filament::icon
                        icon="heroicon-o-information-circle"
                        class="w-5 h-5 text-blue-600 dark:text-blue-400"
                    />
                    <p class="text-sm text-blue-700 dark:text-blue-300">
                        Aucun filtre appliqué. Utilisez les filtres ci-dessus pour obtenir des statistiques personnalisées.
                    </p>
                </div>
            </div>
        @endif
    <!-- </div> -->
</x-filament-panels::page>