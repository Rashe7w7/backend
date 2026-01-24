<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CategoriaIndicador;
use App\Models\Indicador;
use App\Models\CursoAcademico;

echo "=== Insertando datos de prueba ===\n";

// Crear categorías
$categorias = [
    ['nombre' => 'Numérico'],
    ['nombre' => 'Porcentual'],
    ['nombre' => 'Cualitativo'],
    ['nombre' => 'Booleano'],
];

foreach ($categorias as $cat) {
    CategoriaIndicador::firstOrCreate(
        ['nombre' => $cat['nombre']],
        []
    );
    echo "✓ Categoría creada: {$cat['nombre']}\n";
}

// Crear cursos académicos
$cursos = [
    ['nombre' => '2024-2025', 'fecha_inicio' => '2024-09-01', 'fecha_fin' => '2025-06-30', 'activo' => true],
    ['nombre' => '2025-2026', 'fecha_inicio' => '2025-09-01', 'fecha_fin' => '2026-06-30', 'activo' => true],
    ['nombre' => '2026-2027', 'fecha_inicio' => '2026-09-01', 'fecha_fin' => '2027-06-30', 'activo' => false],
];

foreach ($cursos as $curso) {
    CursoAcademico::firstOrCreate(
        ['nombre' => $curso['nombre']],
        [
            'fecha_inicio' => $curso['fecha_inicio'],
            'fecha_fin' => $curso['fecha_fin'],
            'activo' => $curso['activo'],
        ]
    );
    echo "✓ Curso académico creado: {$curso['nombre']}\n";
}

// Crear indicadores
$indicadores = [
    ['nombre' => 'Tasa de aprobación'],
    ['nombre' => 'Tasa de asistencia'],
    ['nombre' => 'Índice de desempeño'],
    ['nombre' => 'Calidad de enseñanza'],
    ['nombre' => 'Satisfacción estudiantil'],
];

foreach ($indicadores as $ind) {
    Indicador::firstOrCreate(
        ['nombre' => $ind['nombre']],
        []
    );
    echo "✓ Indicador creado: {$ind['nombre']}\n";
}

echo "\n=== Verificación final ===\n";
echo "Categorías: " . CategoriaIndicador::count() . "\n";
echo "Indicadores: " . Indicador::count() . "\n";
echo "Cursos: " . CursoAcademico::count() . "\n";

echo "\n✓ Datos de prueba insertados correctamente!\n";
