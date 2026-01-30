<x-filament-panels::page>

    <x-filament::section heading="Suivi du colis - {{ $record->numero_bl }}">
        
        <div class="mb-6">
            <x-filament::section>
                {{ $this->form }}
            </x-filament::section>
        </div>

        <x-filament::section heading="Historique des événements">
            @if ($record->trackingEvents->isEmpty())
                <p class="text-sm text-gray-500">
                    Aucun événement de suivi pour ce colis.
                </p>
            @else
                <ol class="relative border-l border-gray-300">
                    @foreach ($record->trackingEvents->sortByDesc('created_at') as $event)
                        <li class="mb-6 ml-4">
                            <div class="absolute w-3 h-3 rounded-full -left-1.5 bg-primary-600"></div>

                            <time class="text-sm text-gray-500">
                                {{ $event->created_at->format('d/m/Y H:i') }}
                            </time>

                            <h3 class="font-semibold">
                                {{ $event->label }}
                            </h3>

                            @if ($event->user)
                                <p class="text-xs text-gray-500">
                                    Par {{ $event->user->name }}
                                </p>
                            @endif

                            @if ($event->commentaire)
                                <p class="text-sm text-gray-600">
                                    {{ $event->commentaire }}
                                </p>
                            @endif
                        </li>
                    @endforeach
                </ol>
            @endif
        </x-filament::section>

    </x-filament::section>

</x-filament-panels::page>