<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('permissions')->insert([
            [
                'id' => 1,
                'name' => 'Modulo_Dashboard',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 2,
                'name' => 'Modulo_Administracion',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 10,
                'name' => 'Gestion_Estado',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 14,
                'name' => 'Editar_Proyecto',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 15,
                'name' => 'Eliminar_Proyecto',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 16,
                'name' => 'Agregar_Permiso',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 17,
                'name' => 'Eliminar_Permiso',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 18,
                'name' => 'Editar_Permiso',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 19,
                'name' => 'Agregar_Rol',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 20,
                'name' => 'Editar_Rol',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 21,
                'name' => 'Eliminar_Rol',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 22,
                'name' => 'Asignar_Permisos',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 23,
                'name' => 'Agregar_Usuario',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 24,
                'name' => 'Editar_Usuario',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 25,
                'name' => 'Eliminar_Usuario',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 26,
                'name' => 'Asignar_Rol',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 27,
                'name' => 'Agregar_Carrera',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 28,
                'name' => 'Editar_Carrera',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 29,
                'name' => 'Eliminar_Carrera',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 30,
                'name' => 'Agregar_Universidad',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 31,
                'name' => 'Editar_Universidad',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 32,
                'name' => 'Eliminar_Universidad',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 34,
                'name' => 'Modulo_Log',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 35,
                'name' => 'Modulo_Utilidades',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 36,
                'name' => 'Exportar_Excel',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 37,
                'name' => 'Observaciones_Proyecto',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 38,
                'name' => 'Agregar_Observacion_Proyecto',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 39,
                'name' => 'Acciones_Observacion_Proyecto',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 40,
                'name' => 'Modulo_Evaluacion',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 41,
                'name' => 'Generar_Caratula',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 42,
                'name' => 'Modulo_Api',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 43,
                'name' => 'Agregar_Tabla',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 44,
                'name' => 'Editar_Tabla',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 45,
                'name' => 'Eliminar_Tabla',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 46,
                'name' => 'Cambiar_password',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 47,
                'name' => 'Modulo_Evento',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 48,
                'name' => 'Agregar_Evento',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 49,
                'name' => 'Editar_Evento',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 50,
                'name' => 'Eliminar_Evento',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 51,
                'name' => 'Agregar_Acceso',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 52,
                'name' => 'Editar_Acceso',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ],
            [
                'id' => 53,
                'name' => 'Eliminar_Acceso',
                'guard_name' => 'web',
                'created_at' => '2025-01-29 10:00:00',
                'updated_at' => '2025-01-29 10:00:00', 
            ]
            
        ]);


    }
}
