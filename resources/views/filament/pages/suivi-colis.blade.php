<x-filament-panels::page>

    @if(!$colis)
        <x-filament::section heading="Suivi colis">
            <p>Aucun colis trouvé.</p>
        </x-filament::section>
    @else

    @php
        $steps = $this->getSteps();
        $currentIndex = $this->getCurrentStepIndex();
        $percent = (($currentIndex + 1) / count($steps)) * 100;
    @endphp

    {{-- Progress bar globale --}}
    <x-filament::section heading="Progression du colis #{{ $colis->id }}">

        <div class="w-full bg-gray-200 rounded-full h-4 mb-4">
            <div
                class="bg-primary-600 h-4 rounded-full transition-all"
                style="width: {{ $percent }}%">
            </div>
        </div>

        <div class="flex justify-between text-xs">
            @foreach ($steps as $i => $step)
                <span class="{{ $i <= $currentIndex ? 'font-semibold text-primary-600' : 'text-gray-400' }}">
                    @switch($step)
                        @case('BL_ENREGISTRE') BL @break
                        @case('AU_PORT') Port @break
                        @case('A_LA_DOUANE') Douane @break
                        @case('EN_ROUTE') Route @break
                        @case('LIVRE') Livré @break
                    @endswitch
                </span>
            @endforeach
        </div>

    </x-filament::section>

    {{-- Tableau des opérations --}}
    <x-filament::section heading="Détail des étapes">

        <table class="w-full text-sm border rounded-lg overflow-hidden">
            <thead class="bg-gray-100">
                <tr>
                    <th class="text-left p-2">Étape</th>
                    <th class="text-left p-2">Date</th>
                    <th class="text-left p-2">Statut</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-t">
                    <td class="p-2">BL enregistré</td>
                    <td class="p-2">{{ $colis->created_at }}</td>
                    <td class="p-2">
                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded">Terminé</span>
                    </td>
                </tr>

                <tr class="border-t">
                    <td class="p-2">Passage au port</td>
                    <td class="p-2">
                        {{ optional($colis->portOperation)->date_entree_port ?? '—' }}
                    </td>
                    <td class="p-2">
                        @if($colis->portOperation)
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded">Effectué</span>
                        @else
                            <span class="px-2 py-1 bg-gray-100 text-gray-500 rounded">En attente</span>
                        @endif
                    </td>
                </tr>

                <tr class="border-t">
                    <td class="p-2">Douane</td>
                    <td class="p-2">
                        {{ optional($colis->douaneOperation)->date_entree_douane ?? '—' }}
                    </td>
                    <td class="p-2">
                        @if($colis->douaneOperation)
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded">En cours / OK</span>
                        @else
                            <span class="px-2 py-1 bg-gray-100 text-gray-500 rounded">Non commencé</span>
                        @endif
                    </td>
                </tr>

                <tr class="border-t">
                    <td class="p-2">Expertise</td>
                    <td class="p-2">
                        {{ optional($colis->expertise)->updated_at ?? '—' }}
                    </td>
                    <td class="p-2">
                        @if($colis->expertise)
                            <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded">Réalisée</span>
                        @else
                            <span class="px-2 py-1 bg-gray-100 text-gray-500 rounded">Non requise / attente</span>
                        @endif
                    </td>
                </tr>

                <tr class="border-t">
                    <td class="p-2">Livraison</td>
                    <td class="p-2">
                        {{ optional($colis->livraisonOperation)->date_livraison ?? '—' }}
                    </td>
                    <td class="p-2">
                        @if($colis->etat_colis === 'LIVRE')
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded">Livré</span>
                        @else
                            <span class="px-2 py-1 bg-gray-100 text-gray-500 rounded">En attente</span>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>

    </x-filament::section>

    {{-- Historique des événements --}}
    <x-filament::section heading="Historique de suivi">

        <ul class="space-y-2">
            @foreach($colis->trackingEvents()->latest()->get() as $event)
                <li class="p-3 border rounded-lg">
                    <div class="font-semibold">{{ $event->label }}</div>
                    <div class="text-xs text-gray-500">
                        {{ $event->created_at->format('d/m/Y H:i') }}
                        @if($event->user)
                            • par {{ $event->user->name }}
                        @endif
                    </div>
                    @if($event->commentaire)
                        <div class="text-sm mt-1">{{ $event->commentaire }}</div>
                    @endif
                </li>
            @endforeach
        </ul>

    </x-filament::section>

    @endif

</x-filament-panels::page>
