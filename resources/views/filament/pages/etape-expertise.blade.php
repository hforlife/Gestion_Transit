<x-filament-panels::page>
    <div class="space-y-6">
        <!-- En-tête avec statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total expertises</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ \App\Models\Colis::whereNotNull('etat_expertise')->count() }}
                        </p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-500/10 rounded-full">
                        <x-filament::icon icon="heroicon-o-clipboard-document-list" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Non commencé</p>
                        <p class="text-2xl font-bold text-gray-600 dark:text-gray-400">
                            {{ \App\Models\Colis::where('etat_expertise', 'NON_COMMENCE')->count() }}
                        </p>
                    </div>
                    <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-full">
                        <x-filament::icon icon="heroicon-o-x-circle" class="w-6 h-6 text-gray-600 dark:text-gray-400" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">En cours</p>
                        <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                            {{ \App\Models\Colis::where('etat_expertise', 'EN_COURS')->count() }}
                        </p>
                    </div>
                    <div class="p-3 bg-yellow-100 dark:bg-yellow-500/10 rounded-full">
                        <x-filament::icon icon="heroicon-o-arrow-path" class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">En attente docs</p>
                        <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                            {{ \App\Models\Colis::where('etat_expertise', 'EN_ATTENTE_DOCUMENTS')->count() }}
                        </p>
                    </div>
                    <div class="p-3 bg-orange-100 dark:bg-orange-500/10 rounded-full">
                        <x-filament::icon icon="heroicon-o-document" class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">En attente validation</p>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                            {{ \App\Models\Colis::where('etat_expertise', 'EN_ATTENTE_VALIDATION')->count() }}
                        </p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-500/10 rounded-full">
                        <x-filament::icon icon="heroicon-o-clock" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Terminé</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                            {{ \App\Models\Colis::where('etat_expertise', 'TERMINE')->count() }}
                        </p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-500/10 rounded-full">
                        <x-filament::icon icon="heroicon-o-check-circle" class="w-6 h-6 text-green-600 dark:text-green-400" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Indicateurs de progression -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Documents PVC</h3>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Fournis</span>
                        <span class="font-medium text-gray-900 dark:text-white">
                            {{ \App\Models\Colis::whereNotNull('num_pvc')->count() }}
                        </span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Validés</span>
                        <span class="font-medium text-green-600">
                            {{ \App\Models\Colis::where('etat_pvc', 'VALIDE')->count() }}
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                        @php
                            $total = \App\Models\Colis::whereNotNull('etat_pvc')->count();
                            $valides = \App\Models\Colis::where('etat_pvc', 'VALIDE')->count();
                            $pourcentage = $total > 0 ? ($valides / $total) * 100 : 0;
                        @endphp
                        <div class="bg-green-600 h-2.5 rounded-full" style="width: {{ $pourcentage }}%"></div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Documents AE</h3>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Fournis</span>
                        <span class="font-medium text-gray-900 dark:text-white">
                            {{ \App\Models\Colis::whereNotNull('num_ae')->count() }}
                        </span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Validés</span>
                        <span class="font-medium text-green-600">
                            {{ \App\Models\Colis::where('etat_ae', 'VALIDE')->count() }}
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                        @php
                            $total = \App\Models\Colis::whereNotNull('etat_ae')->count();
                            $valides = \App\Models\Colis::where('etat_ae', 'VALIDE')->count();
                            $pourcentage = $total > 0 ? ($valides / $total) * 100 : 0;
                        @endphp
                        <div class="bg-green-600 h-2.5 rounded-full" style="width: {{ $pourcentage }}%"></div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Documents CMC</h3>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Fournis</span>
                        <span class="font-medium text-gray-900 dark:text-white">
                            {{ \App\Models\Colis::whereNotNull('num_cmc')->count() }}
                        </span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Validés</span>
                        <span class="font-medium text-green-600">
                            {{ \App\Models\Colis::where('etat_cmc', 'VALIDE')->count() }}
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                        @php
                            $total = \App\Models\Colis::whereNotNull('etat_cmc')->count();
                            $valides = \App\Models\Colis::where('etat_cmc', 'VALIDE')->count();
                            $pourcentage = $total > 0 ? ($valides / $total) * 100 : 0;
                        @endphp
                        <div class="bg-green-600 h-2.5 rounded-full" style="width: {{ $pourcentage }}%"></div>
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
                <button 
                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['tableFilters' => ['etat_expertise' => ['values' => ['EN_COURS']]]]) }}'"
                    class="px-3 py-1.5 text-xs font-medium rounded-full border border-yellow-200 bg-yellow-50 text-yellow-700 hover:bg-yellow-100"
                >
                    En cours
                </button>
                <button 
                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['tableFilters' => ['etat_expertise' => ['values' => ['EN_ATTENTE_DOCUMENTS']]]]) }}'"
                    class="px-3 py-1.5 text-xs font-medium rounded-full border border-orange-200 bg-orange-50 text-orange-700 hover:bg-orange-100"
                >
                    En attente documents
                </button>
                <button 
                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['tableFilters' => ['etat_expertise' => ['values' => ['EN_ATTENTE_VALIDATION']]]]) }}'"
                    class="px-3 py-1.5 text-xs font-medium rounded-full border border-blue-200 bg-blue-50 text-blue-700 hover:bg-blue-100"
                >
                    En attente validation
                </button>
                <button 
                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['tableFilters' => ['documents_manquants' => true]]) }}'"
                    class="px-3 py-1.5 text-xs font-medium rounded-full border border-red-200 bg-red-50 text-red-700 hover:bg-red-100"
                >
                    Documents manquants
                </button>
                <button 
                    onclick="window.location.href='{{ request()->fullUrlWithQuery(['tableFilters' => ['status' => ['values' => ['APPROUVE']]]]) }}'"
                    class="px-3 py-1.5 text-xs font-medium rounded-full border border-green-200 bg-green-50 text-green-700 hover:bg-green-100"
                >
                    Approuvés
                </button>
                <button 
                    onclick="window.location.href='{{ request()->url() }}'"
                    class="px-3 py-1.5 text-xs font-medium rounded-full border border-gray-300 bg-gray-100 text-gray-700"
                >
                    Réinitialiser
                </button>
            </div>
        </div>

        <!-- Tableau des colis -->
        {{ $this->table }}
    </div>
</x-filament-panels::page>