<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,  
            RoleSeeder::class,  
            RolPermissionSeeder::class,  
            CarreraSeeder::class,  
            UniversidadSeeder::class,  
            UserSeeder::class,  
            ModelRolSeeder::class,
        ]);
    }
}
