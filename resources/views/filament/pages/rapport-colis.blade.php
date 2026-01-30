<x-filament-panels::page>
    {{-- Page content --}}
    {{-- <x-filament::section heading="Rapport global des colis">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-4 bg-white rounded-xl shadow">
                <p class="text-sm text-gray-500">Total colis</p>
                <p class="text-2xl font-bold">
                    {{ \App\Models\Colis::count() }}
                </p>
            </div>

            <div class="p-4 bg-white rounded-xl shadow">
                <p class="text-sm text-gray-500">En cours</p>
                <p class="text-2xl font-bold">
                    {{ \App\Models\Colis::whereNot('etat_colis', 'LIVRE')->count() }}
                </p>
            </div>

            <div class="p-4 bg-white rounded-xl shadow">
                <p class="text-sm text-gray-500">Livrés</p>
                <p class="text-2xl font-bold text-green-600">
                    {{ \App\Models\Colis::where('etat_colis', 'LIVRE')->count() }}
                </p>
            </div>
        </div>

    </x-filament::section> --}}
</x-filament-panels::page>
