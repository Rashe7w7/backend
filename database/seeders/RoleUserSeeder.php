<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class RoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            ['name' => 'Administrador', 'email' => 'administrador@sistema.test', 'password' => 'Administrador123!', 'role' => 'administrador'],
            ['name' => 'PPA', 'email' => 'ppa@sistema.test', 'password' => 'Ppa12345!', 'role' => 'ppa'],
            ['name' => 'Jefe de Departamento', 'email' => 'jefe.departamento@sistema.test', 'password' => 'JefeDepartamento123!', 'role' => 'jefe_departamento'],
            ['name' => 'Decano', 'email' => 'decano@sistema.test', 'password' => 'Decano123!', 'role' => 'decano'],
            ['name' => 'Rector', 'email' => 'rector@sistema.test', 'password' => 'Rector123!', 'role' => 'rector'],
            ['name' => 'Vicerrector', 'email' => 'vicerrector@sistema.test', 'password' => 'Vicerrector123!', 'role' => 'vicerrector'],
            ['name' => 'Secretaria', 'email' => 'secretaria@sistema.test', 'password' => 'Secretaria123!', 'role' => 'secretaria'],
        ];

        foreach ($accounts as $account) {
            User::query()->updateOrCreate(
                ['email' => $account['email']],
                [
                    ...$account,
                    'email_verified_at' => now(),
                    'profesor_id' => null,
                ],
            );
        }
    }
}
