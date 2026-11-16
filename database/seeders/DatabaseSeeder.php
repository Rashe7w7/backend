<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (app()->environment(['local', 'testing'])) {
            User::factory()->withoutTwoFactor()->create([
                'name' => 'Administrador',
                'email' => env('APP_ADMIN_EMAIL', 'admin@example.com'),
                'password' => env('APP_ADMIN_PASSWORD', 'password'),
                'role' => 'administrador',
            ]);
        }

        $this->call(CursoAcademicoSeeder::class);

        if (app()->environment(['local', 'testing'])) {
            $this->call(FakeDataSeeder::class);
        }
    }
}
