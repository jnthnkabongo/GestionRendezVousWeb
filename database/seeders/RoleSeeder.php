<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'nom' => 'chef',
                'description' => 'Chef de projet - Peut gérer les rendez-vous et les clients',
            ],
            [
                'nom' => 'secretaire',
                'description' => 'Secrétaire - Peut créer et gérer les rendez-vous',
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['nom' => $role['nom']],
                ['description' => $role['description']]
            );
        }
    }
}
