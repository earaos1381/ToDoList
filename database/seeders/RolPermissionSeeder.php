<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class RolPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('role_has_permissions')->insert([
            /*  ROL 1  Administrador*/
            [
                'permission_id' => 1,
                'role_id' => 1,
            ],
            [
                'permission_id' => 2,
                'role_id' => 1,
            ],
            [
                'permission_id' => 10,
                'role_id' => 1,
            ],
            [
                'permission_id' => 14,
                'role_id' => 1,
            ],
            [
                'permission_id' => 15,
                'role_id' => 1,
            ],
            [
                'permission_id' => 23,
                'role_id' => 1,
            ],
            [
                'permission_id' => 24,
                'role_id' => 1,
            ],
            [
                'permission_id' => 25,
                'role_id' => 1,
            ],
            [
                'permission_id' => 27,
                'role_id' => 1,
            ],
            [
                'permission_id' => 28,
                'role_id' => 1,
            ],
            [
                'permission_id' => 29,
                'role_id' => 1,
            ],
            [
                'permission_id' => 30,
                'role_id' => 1,
            ],
            [
                'permission_id' => 31,
                'role_id' => 1,
            ],
            [
                'permission_id' => 32,
                'role_id' => 1,
            ],
            [
                'permission_id' => 36,
                'role_id' => 1,
            ],
            [
                'permission_id' => 37,
                'role_id' => 1,
            ],
            [
                'permission_id' => 38,
                'role_id' => 1,
            ],
            [
                'permission_id' => 39,
                'role_id' => 1,
            ],
            [
                'permission_id' => 40,
                'role_id' => 1,
            ],
            [
                'permission_id' => 41,
                'role_id' => 1,
            ],

            /*ROL 2 Usuario*/
            [
                'permission_id' => 1,
                'role_id' => 2,
            ],
            [
                'permission_id' => 14,
                'role_id' => 2,
            ],
            [
                'permission_id' => 46,
                'role_id' => 2,
            ],

            /*ROL 8 Root*/
            [
                'permission_id' => 1,
                'role_id' => 8,
            ],
            [
                'permission_id' => 2,
                'role_id' => 8,
            ],
            [
                'permission_id' => 10,
                'role_id' => 8,
            ],
            [
                'permission_id' => 14,
                'role_id' => 8,
            ],
            [
                'permission_id' => 15,
                'role_id' => 8,
            ],
            [
                'permission_id' => 16,
                'role_id' => 8,
            ],
            [
                'permission_id' => 17,
                'role_id' => 8,
            ],
            [
                'permission_id' => 18,
                'role_id' => 8,
            ],
            [
                'permission_id' => 19,
                'role_id' => 8,
            ],
            [
                'permission_id' => 20,
                'role_id' => 8,
            ],
            [
                'permission_id' => 21,
                'role_id' => 8,
            ],
            [
                'permission_id' => 22,
                'role_id' => 8,
            ],
            [
                'permission_id' => 23,
                'role_id' => 8,
            ],
            [
                'permission_id' => 24,
                'role_id' => 8,
            ],
            [
                'permission_id' => 25,
                'role_id' => 8,
            ],
            [
                'permission_id' => 26,
                'role_id' => 8,
            ],
            [
                'permission_id' => 27,
                'role_id' => 8,
            ],
            [
                'permission_id' => 28,
                'role_id' => 8,
            ],
            [
                'permission_id' => 29,
                'role_id' => 8,
            ],
            [
                'permission_id' => 30,
                'role_id' => 8,
            ],
            [
                'permission_id' => 31,
                'role_id' => 8,
            ],
            [
                'permission_id' => 32,
                'role_id' => 8,
            ],
            [
                'permission_id' => 34,
                'role_id' => 8,
            ],
            [
                'permission_id' => 35,
                'role_id' => 8,
            ],
            [
                'permission_id' => 36,
                'role_id' => 8,
            ],
            [
                'permission_id' => 37,
                'role_id' => 8,
            ],
            [
                'permission_id' => 38,
                'role_id' => 8,
            ],
            [
                'permission_id' => 39,
                'role_id' => 8,
            ],
            [
                'permission_id' => 40,
                'role_id' => 8,
            ],
            [
                'permission_id' => 41,
                'role_id' => 8,
            ],
            [
                'permission_id' => 42,
                'role_id' => 8,
            ],
            [
                'permission_id' => 43,
                'role_id' => 8,
            ],
            [
                'permission_id' => 44,
                'role_id' => 8,
            ],
            [
                'permission_id' => 45,
                'role_id' => 8,
            ],
            [
                'permission_id' => 46,
                'role_id' => 8,
            ],
            [
                'permission_id' => 47,
                'role_id' => 8,
            ],
            [
                'permission_id' => 48,
                'role_id' => 8,
            ],
            [
                'permission_id' => 49,
                'role_id' => 8,
            ],
            [
                'permission_id' => 50,
                'role_id' => 8,
            ],
            [
                'permission_id' => 51,
                'role_id' => 8,
            ],
            [
                'permission_id' => 52,
                'role_id' => 8,
            ],
            [
                'permission_id' => 53,
                'role_id' => 8,
            ],

            /*ROL 9 Respaldos*/
            [
                'permission_id' => 1,
                'role_id' => 9,
            ],
            [
                'permission_id' => 14,
                'role_id' => 9,
            ],
            [
                'permission_id' => 35,
                'role_id' => 9,
            ],
            [
                'permission_id' => 42,
                'role_id' => 9,
            ],
            [
                'permission_id' => 43,
                'role_id' => 9,
            ], 
            [
                'permission_id' => 44,
                'role_id' => 9,
            ],
            [
                'permission_id' => 45,
                'role_id' => 9,
            ],

            /*ROL 10 Juez*/
            [
                'permission_id' => 40,
                'role_id' => 10,
            ],

        ]);

    }
}
