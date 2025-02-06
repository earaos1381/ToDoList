<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'ADMINISTRADOR PRINCIPAL',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('9v4odghgjczUD53'),
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00',
            ]
        ]);
    }
}
