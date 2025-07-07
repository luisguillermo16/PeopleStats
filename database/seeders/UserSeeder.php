<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        
        $role = Role::firstOrCreate(['name' => 'super-admin']);

        $usuarios = [
            [
                'name' => 'super-admin',
                'email' => 'luis@gmail.com',
                'password' => bcrypt('123456789'),
                'rol' => 'super-admin',
            ],
        ];

        foreach ($usuarios as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']], // evita duplicados
                [
                    'name' => $data['name'],
                    'password' => $data['password'],
                ]
            );

            // Asignamos el rol si aún no lo tiene
            if (!$user->hasRole($data['rol'])) {
                $user->assignRole($data['rol']);
            }
        }
    }
}
