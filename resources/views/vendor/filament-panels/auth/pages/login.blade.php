<x-filament-panels::page.simple>

    <div class="min-h-screen flex items-center justify-center bg-cover bg-center"
         style="background-image: url('{{ asset('images/login-bg.jpg') }}');">

        <div class="w-full max-w-md bg-white/80 backdrop-blur-md rounded-xl shadow-xl p-6">

            {{ $this->form }}

        </div>

    </div>

</x-filament-panels::page.simple>
