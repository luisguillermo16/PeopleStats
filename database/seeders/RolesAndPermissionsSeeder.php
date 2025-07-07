<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ============================================
        // CREACIÓN DE PERMISOS DEL SISTEMA ELECTORAL
        // ============================================
        
        // Permisos de gestión de usuarios
        Permission::firstOrCreate(['name' => 'ver usuarios']);
        Permission::firstOrCreate(['name' => 'crear usuarios']);
        Permission::firstOrCreate(['name' => 'editar usuarios']);
        Permission::firstOrCreate(['name' => 'eliminar usuarios']);

        // Permisos de gestión de perfiles
        Permission::firstOrCreate(['name' => 'ver perfiles']);
        Permission::firstOrCreate(['name' => 'crear perfiles']);
        Permission::firstOrCreate(['name' => 'editar perfiles']);

        // Permisos específicos del sistema electoral
        Permission::firstOrCreate(['name' => 'crear alcaldes']);
        Permission::firstOrCreate(['name' => 'crear concejales']);
        Permission::firstOrCreate(['name' => 'crear lideres']);
        Permission::firstOrCreate(['name' => 'ingresar votantes']);
        Permission::firstOrCreate(['name' => 'ver votantes del alcalde']);
        Permission::firstOrCreate(['name' => 'ver votantes del concejal']);
        Permission::firstOrCreate(['name' => 'ver todo dashboard']);
        Permission::firstOrCreate(['name' => 'ver logs sistema']);
        Permission::firstOrCreate(['name' => 'administrar sistema']);

        // Permiso especial para acceder al panel admin
        Permission::firstOrCreate(['name' => 'acceder admin']);

        // ============================================
        // CREACIÓN DE ROLES Y ASIGNACIÓN DE PERMISOS
        // ============================================

        // 🟩 SUPER ADMIN - Control total del sistema
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $roleSuperAdmin->syncPermissions([
            'ver usuarios',
            'crear usuarios',
            'editar usuarios',
            'eliminar usuarios',
            'ver perfiles',
            'crear perfiles',
            'editar perfiles',
            'crear alcaldes',
            'ver todo dashboard',
            'ver logs sistema',
            'administrar sistema',
            'acceder admin'  // IMPORTANTE: Solo super-admin puede acceder al admin
        ]);

        // 🟨 ASPIRANTE A LA ALCALDÍA - Control sobre concejales y votantes directos
        $roleAlcalde = Role::firstOrCreate(['name' => 'aspirante-alcaldia']);
        $roleAlcalde->syncPermissions([
            'crear concejales',
            'ver votantes del alcalde',
            'ingresar votantes',
            'ver perfiles',
            'editar perfiles'
        ]);

        // 🟧 ASPIRANTE AL CONCEJO - Control sobre líderes y sus votantes
        $roleConcejal = Role::firstOrCreate(['name' => 'aspirante-concejo']);
        $roleConcejal->syncPermissions([
            'crear lideres',
            'ver votantes del concejal',
            'ver perfiles',
            'editar perfiles'
        ]);

        // 🟦 LÍDER - Registra votantes bajo su concejal
        $roleLider = Role::firstOrCreate(['name' => 'lider']);
        $roleLider->syncPermissions([
            'ingresar votantes',
            'ver perfiles',
            'editar perfiles'
        ]);
    }
}