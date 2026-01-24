<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CategoriaIndicador;
use App\Models\Indicador;
use App\Models\CursoAcademico;
use App\Models\Carrera;

echo "=== Verificación de datos en la base de datos ===\n";
echo "Categorías de Indicador: " . CategoriaIndicador::count() . "\n";
echo "Indicadores: " . Indicador::count() . "\n";
echo "Cursos Académicos: " . CursoAcademico::count() . "\n";
echo "Carreras: " . Carrera::count() . "\n";

echo "\n=== Categorías (primeras 5) ===\n";
foreach (CategoriaIndicador::take(5)->get() as $cat) {
    echo "- ID: {$cat->id}, Nombre: {$cat->nombre}\n";
}

echo "\n=== Indicadores (primeros 5) ===\n";
foreach (Indicador::take(5)->get() as $ind) {
    echo "- ID: {$ind->id}, Nombre: {$ind->nombre}\n";
}

echo "\n=== Cursos Académicos (primeros 5) ===\n";
foreach (CursoAcademico::take(5)->get() as $curso) {
    echo "- ID: {$curso->id}, Nombre: {$curso->nombre}\n";
}
