<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleUserSeeder::class);
        $this->call(CursoAcademicoSeeder::class);
        $this->call(AcademicCatalogSeeder::class);

        if (app()->environment(['local', 'testing'])) {
            $this->call(LocalAdminSeeder::class);
            $this->call(DemoAcademicDataSeeder::class);
        }
    }
}
