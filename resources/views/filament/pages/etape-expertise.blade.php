<x-filament-panels::page>
    <div class="space-y-6">
        <!-- En-tête avec statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total expertises</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ \App\Models\Colis::where('etat_expertise', '!=', null)->count() }}
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
                        <p class="text-sm text-gray-500 dark:text-gray-400">En attente</p>
                        <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                            {{ \App\Models\Colis::where('etat_expertise', 'EN_ATTENTE')->count() }}
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
                        <p class="text-sm text-gray-500 dark:text-gray-400">Effectuée</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                            {{ \App\Models\Colis::where('etat_expertise', 'EFFECTUEE')->count() }}
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
                        <p class="text-sm text-gray-500 dark:text-gray-400">Dossiers en cours</p>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                            {{ \App\Models\Colis::where('status', 'EN_COURS')->count() }}
                        </p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-500/10 rounded-full">
                        <x-filament::icon icon="heroicon-o-document" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Dossiers terminés</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                            {{ \App\Models\Colis::where('status', 'TERMINE')->count() }}
                        </p>
                    </div>
                    <div class="p-3 bg-green-100 dark:bg-green-500/10 rounded-full">
                        <x-filament::icon icon="heroicon-o-flag" class="w-6 h-6 text-green-600 dark:text-green-400" />
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
                        <span class="text-gray-500">Non reçus</span>
                        <span class="font-medium text-red-600">
                            {{ \App\Models\Colis::where('etat_pvc', 'NON_RECU')->count() }}
                        </span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Reçus</span>
                        <span class="font-medium text-blue-600">
                            {{ \App\Models\Colis::where('etat_pvc', 'RECU')->count() }}
                        </span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Payés</span>
                        <span class="font-medium text-green-600">
                            {{ \App\Models\Colis::where('etat_pvc', 'PAYE')->count() }}
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                        @php
                            $total = \App\Models\Colis::whereNotNull('etat_pvc')->count();
                            $termines = \App\Models\Colis::where('etat_pvc', 'PAYE')->count();
                            $pourcentage = $total > 0 ? ($termines / $total) * 100 : 0;
                        @endphp
                        <div class="bg-green-600 h-2.5 rounded-full" style="width: {{ $pourcentage }}%"></div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Documents AE</h3>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Non valides</span>
                        <span class="font-medium text-red-600">
                            {{ \App\Models\Colis::where('etat_ae', 'NON_VALIDE')->count() }}
                        </span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Valides</span>
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
                        <span class="text-gray-500">Non reçus</span>
                        <span class="font-medium text-red-600">
                            {{ \App\Models\Colis::where('etat_cmc', 'NON_RECU')->count() }}
                        </span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Reçus</span>
                        <span class="font-medium text-green-600">
                            {{ \App\Models\Colis::where('etat_cmc', 'RECU')->count() }}
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                        @php
                            $total = \App\Models\Colis::whereNotNull('etat_cmc')->count();
                            $recus = \App\Models\Colis::where('etat_cmc', 'RECU')->count();
                            $pourcentage = $total > 0 ? ($recus / $total) * 100 : 0;
                        @endphp
                        <div class="bg-green-600 h-2.5 rounded-full" style="width: {{ $pourcentage }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableau des colis -->
        {{ $this->table }}
    </div>
</x-filament-panels::page>