<?php

namespace Database\Seeders;

use App\Models\CursoAcademico;
use Illuminate\Database\Seeder;

class CursoAcademicoSeeder extends Seeder
{
    public function run(): void
    {
        $cursos = [
            [
                'nombre' => '2023-2024',
                'fecha_inicio' => '2023-09-01',
                'fecha_fin' => '2024-06-30',
                'activo' => false,
            ],
            [
                'nombre' => '2024-2025',
                'fecha_inicio' => '2024-09-01',
                'fecha_fin' => '2025-06-30',
                'activo' => true,
            ],
            [
                'nombre' => '2025-2026',
                'fecha_inicio' => '2025-09-01',
                'fecha_fin' => '2026-06-30',
                'activo' => false,
            ],
        ];

        foreach ($cursos as $curso) {
            CursoAcademico::firstOrCreate(
                ['nombre' => $curso['nombre']],
                $curso
            );
        }
    }
}
