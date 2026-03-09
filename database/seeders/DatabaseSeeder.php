<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Créer plusieurs clients
        Client::factory()->count(10)->create();

        $this->call(AdminSeeder::class);
        $this->call(TypeColisSeeder::class);
    }
}
