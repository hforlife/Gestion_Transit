<x-filament-panels::page>
    {{-- Page content --}}
        {{-- <x-filament::section heading="Colis bloqués ou en attente">

        @php
            $bloques = \App\Models\Colis::whereIn('etat_colis', [
                'AU_PORT',
                'A_LA_DOUANE'
            ])->get();
        @endphp

        <table class="w-full text-sm">
            <thead>
                <tr class="border-b">
                    <th class="text-left py-2">BL</th>
                    <th class="text-left py-2">Client</th>
                    <th class="text-left py-2">Statut</th>
                    <th class="text-left py-2">Créé le</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bloques as $colis)
                    <tr class="border-b">
                        <td class="py-2">{{ $colis->numero_bl }}</td>
                        <td class="py-2">{{ $colis->client->name ?? '-' }}</td>
                        <td class="py-2 font-semibold text-orange-600">
                            {{ $colis->etat_colis }}
                        </td>
                        <td class="py-2">
                            {{ $colis->created_at->format('d/m/Y') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </x-filament::section> --}}
</x-filament-panels::page>
