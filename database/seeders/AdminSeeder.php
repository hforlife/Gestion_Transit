<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $AdminRole = Role::firstOrCreate(['name' => 'admin']);
        $ExpertRole = Role::firstOrCreate(['name' => 'expert']);
        $AdminRole->syncPermissions(Permission::all());

        $admins = [
            [
                'name' => 'Super Admin',
                'email' => 'admin@doucsoft.com',
                'telephone' => '60221414',
                'password' => env('ADMIN_PASSWORD', 'password'),
            ],
            [
                'name' => 'Abdoulaye Y HAIDARA',
                'email' => 'abdoulayeyoro.haidara@doucsoft.com',
                'telephone' => '60221414',
                'password' => env('ADMIN1_PASSWORD', 'password'),
            ],
            [
                'name' => 'Moussa DOUCOURE',
                'email' => 'moussdouc@doucsoft.tech',
                'telephone' => '60221414',
                'password' => env('ADMIN2_PASSWORD', 'password'),
            ],
            [
                'name' => 'Malle MAGASSA',
                'email' => 'mallemagass@doucsoft.tech',
                'telephone' => '60221414',
                'password' => env('ADMIN3_PASSWORD', 'password'),
            ],
        ];

        foreach ($admins as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => bcrypt($data['password']),
                    'telephone' => $data['telephone'],
                    'remember_token' => Str::random(10),
                ]
            );

            $user->assignRole($AdminRole);
        }
    }
}
