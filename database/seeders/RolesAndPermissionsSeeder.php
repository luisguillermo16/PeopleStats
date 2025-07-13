<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // PERMISOS DEL SISTEMA ELECTORAL
        Permission::firstOrCreate(['name' => 'crear alcaldes']);
        Permission::firstOrCreate(['name' => 'crear concejales']);
        Permission::firstOrCreate(['name' => 'ver votaciones alcaldes']);
        Permission::firstOrCreate(['name' => 'ver votaciones concejales']);
        
        Permission::firstOrCreate(['name' => 'crear concejales vinculados al alcalde']);
        
        Permission::firstOrCreate(['name' => 'crear lideres']);
        Permission::firstOrCreate(['name' => 'ingresar votantes']);
        Permission::firstOrCreate(['name' => 'ver votantes del alcalde']);
        Permission::firstOrCreate(['name' => 'ver votantes del concejal']);
        
        
        // Otros permisos comunes
        Permission::firstOrCreate(['name' => 'ver todo dashboard']);
        Permission::firstOrCreate(['name' => 'acceder admin']);

        // ROLES Y SUS PERMISOS
        // Administrador
        $roleAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $roleAdmin->syncPermissions([
            'crear alcaldes',
            'crear concejales vinculados al alcalde',
            'ver votaciones alcaldes',
            'ver votaciones concejales',
            'ver todo dashboard',
            'acceder admin',

        ]);
        

        // Rol Alcalde
        $roleAlcalde = Role::firstOrCreate(['name' => 'aspirante-alcaldia']);
        $roleAlcalde->syncPermissions([
            'ver votantes del alcalde',
            'crear concejales',
             'crear lideres',
           
        ]);

        // Rol Concejal
        $roleConcejal = Role::firstOrCreate(['name' => 'aspirante-concejo']);
        $roleConcejal->syncPermissions([
            'ver votantes del concejal',
            'crear lideres',
            'ingresar votantes',
          
        ]);

        // Rol Líder
        $roleLider = Role::firstOrCreate(['name' => 'lider']);
        $roleLider->syncPermissions([
            'ingresar votantes',
           
        ]);
    }
}
