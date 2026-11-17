<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class LocalAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrador local',
                'password' => 'password',
                'role' => 'administrador',
                'email_verified_at' => now(),
                'profesor_id' => null,
            ],
        );
    }
}
