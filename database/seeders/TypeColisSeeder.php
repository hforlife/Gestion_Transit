<?php

namespace Database\Seeders;

use App\Models\TypeColis;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypeColisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            'Conteneur',
            'Chassis',
            'Chassis Voiture',
            'Chassis Machine',
        ];

        foreach ($types as $type) {
            TypeColis::firstOrCreate([
                'nom' => $type
            ]);
        }
    }
}
