<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                'id' => 1,
                'name' => 'Administrador',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 2,
                'name' => 'Usuario',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 8,
                'name' => 'Root',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 9,
                'name' => 'Respaldos',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 10,
                'name' => 'Juez',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ]
        ]);
    }
}
