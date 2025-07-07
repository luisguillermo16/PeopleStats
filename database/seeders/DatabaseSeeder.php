<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Llamar a los seeders en orden
        $this->call([
            RolesAndPermissionsSeeder::class,
            UserSeeder::class,
        ]);
    }
}