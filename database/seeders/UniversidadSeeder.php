<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class UniversidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('universidades')->insert([
            [
                'id' => 1,
                'descripcion' => 'Instituto Tecnológico de Chetumal',
                'acronimo' => 'ITCH',
                'correo' => 'gestion@chetumal.tecnm.mx',
                'telefono' => '(983) 832 1019 / (983) 832 2330 ext 125',
                'imagen' => 'imagenes_institutos/9555f428-2573-4c14-832e-6c9187aaa952.png',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 4,
                'descripcion' => 'Universidad Tecnológica de La Riviera Maya',
                'acronimo' => 'UTRM',
                'correo' => 'rectoria@utrivieramaya.edu.mx | alejandra.lopez@utrivieramaya.edu.mx',
                'telefono' => '(984)877 4600 ext 1302',
                'imagen' => '',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 5,
                'descripcion' => 'Universidad Tecnológica de Chetumal',
                'acronimo' => 'UTCH',
                'correo' => 'carlos.cohuo@utchetumal.edu.mx',
                'telefono' => '(983)129 17 65 EXT 1025 o 1014',
                'imagen' => '',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 6,
                'descripcion' => 'Universidad Tecnológica de Cancún',
                'acronimo' => 'UTCANCUN',
                'correo' => 'ryam@utcancun.edu.mx',
                'telefono' => '(998) 881-1900 Ext. 1172',
                'imagen' => '',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 7,
                'descripcion' => 'Universidad TecMilenio',
                'acronimo' => 'TECMILENIO',
                'correo' => 'balam.h@tecmilenio.mx, mj_bautista@tecmilenio.mx',
                'telefono' => '(998)-881-4700',
                'imagen' => '',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 8,
                'descripcion' => 'Universidad Politécnica de Quintana Roo',
                'acronimo' => 'UPQROO',
                'correo' => 'vinculacion@upqroo.edu.mx',
                'telefono' => '998 283 1859 ext. 136',
                'imagen' => '',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 9,
                'descripcion' => 'Universidad Politécnica de Bacalar',
                'acronimo' => 'UPB',
                'correo' => 'maria.diaz@upb.edu.mx; vinculacion@upb.edu.mx',
                'telefono' => '983 83 42340 ext. 1011',
                'imagen' => '',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 10,
                'descripcion' => 'Universidad Latinoamericana del Caribe',
                'acronimo' => 'UNICARIBE',
                'correo' => 'informacion@universidadlatinoamericanadelcaribe.edu.mx',
                'telefono' => '(998) 887 3518 | (998) 887 3654',
                'imagen' => '',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 11,
                'descripcion' => 'Universidad Intercultural Maya del Estado de Quintana Roo',
                'acronimo' => 'UIMQROO',
                'correo' => 'oscar.parrao@uimqroo.edu.mx',
                'telefono' => '(997) 68-81441, 68-81438 Extensión:134',
                'imagen' => '',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 12,
                'descripcion' => 'Universidad Anáhuac Cancún',
                'acronimo' => 'ANAHUAC CANCUN',
                'correo' => 'a',
                'telefono' => '(998) 881 7750',
                'imagen' => '',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 13,
                'descripcion' => 'Universidad del Caribe',
                'acronimo' => 'UNICARIBE',
                'correo' => 'unicaribe@unicaribe.edu.mx',
                'telefono' => '998 881 4400',
                'imagen' => '',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
        ]);
    }
}
