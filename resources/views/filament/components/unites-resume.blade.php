@php
    use Illuminate\Support\Collection;
    
    $stats = [
        'AU_PORT' => ['label' => 'Au port', 'count' => 0, 'color' => '#f59e0b', 'bg' => '#fef3c7'],
        'A_LA_DOUANE' => ['label' => 'En douane', 'count' => 0, 'color' => '#3b82f6', 'bg' => '#dbeafe'],
        'EXPERTISE' => ['label' => 'En expertise', 'count' => 0, 'color' => '#8b5cf6', 'bg' => '#ede9fe'],
        'EN_ROUTE' => ['label' => 'En route', 'count' => 0, 'color' => '#06b6d4', 'bg' => '#cffafe'],
        'LIVRE' => ['label' => 'Livré', 'count' => 0, 'color' => '#10b981', 'bg' => '#d1fae5'],
    ];
    
    $total = 0;
    foreach ($unites as $unite) {
        if (isset($stats[$unite->etat])) {
            $stats[$unite->etat]['count']++;
            $total++;
        }
    }
    
    $termine = $stats['LIVRE']['count'];
    $pourcentage = $total > 0 ? round(($termine / $total) * 100) : 0;
@endphp

<div class="mt-6 p-5 bg-gray-50 rounded-xl border border-gray-200">
    <h4 class="text-base font-semibold text-gray-800 mb-4 flex items-center gap-2">
        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
        </svg>
        Suivi des unités ({{ $total }} au total)
    </h4>
    
    <!-- Barre de progression -->
    @if($total > 0)
    <div class="mb-5">
        <div class="flex justify-between text-xs text-gray-600 mb-1.5">
            <span class="font-medium">Progression globale</span>
            <span class="font-semibold text-green-600">{{ $termine }}/{{ $total }} livrés ({{ $pourcentage }}%)</span>
        </div>
        <div class="w-full h-3 bg-gray-200 rounded-full overflow-hidden">
            <div class="h-full bg-gradient-to-r from-green-400 to-green-600 rounded-full transition-all duration-500" 
                 style="width: {{ $pourcentage }}%"></div>
        </div>
    </div>
    @endif
    
    <!-- Statistiques par état -->
    <div class="grid grid-cols-5 gap-3">
        @foreach($stats as $key => $stat)
            @if($stat['count'] > 0)
                <div class="text-center p-2 rounded-lg" style="background-color: {{ $stat['bg'] }}">
                    <span class="text-xs font-medium text-gray-600">{{ $stat['label'] }}</span>
                    <div class="text-xl font-bold" style="color: {{ $stat['color'] }}">
                        {{ $stat['count'] }}
                    </div>
                </div>
            @else
                <div class="text-center p-2 rounded-lg bg-gray-100 opacity-50">
                    <span class="text-xs text-gray-500">{{ $stat['label'] }}</span>
                    <div class="text-xl font-bold text-gray-400">0</div>
                </div>
            @endif
        @endforeach
    </div>
    
    <!-- Liste rapide des unités (optionnel) -->
    @if($total > 0 && $total <= 10)
    <div class="mt-4 pt-4 border-t border-gray-200">
        <h5 class="text-xs font-medium text-gray-500 mb-2">Détail des unités</h5>
        <div class="grid grid-cols-2 gap-2 text-xs">
            @foreach($unites as $unite)
                <div class="flex items-center gap-2 p-1.5 bg-white rounded border border-gray-100">
                    <span class="w-2 h-2 rounded-full" style="background-color: {{ $stats[$unite->etat]['color'] }}"></span>
                    <span class="font-mono">{{ $unite->numero_chassis ?? $unite->numero_conteneur ?? 'N/A' }}</span>
                    <span class="text-gray-500">{{ $unite->type }}</span>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>