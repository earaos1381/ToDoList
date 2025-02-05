<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class CarreraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('carreras')->insert([
            [
                'id' => 1,
                'descripcion' => 'Ing. En Tecnologias de la Información y Comunicaciones',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 2,
                'descripcion' => 'Ing. Sistemas Computacionales',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 3,
                'descripcion' => 'Ing. Inteligencia Artificial',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
        ]);
    }
}
