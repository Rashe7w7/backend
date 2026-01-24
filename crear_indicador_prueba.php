<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\CategoriaIndicador;
use App\Models\Indicador;
use App\Models\IndicadorCarrera;
use App\Models\Carrera;
use App\Models\CursoAcademico;
use App\Models\AnioAcademico;

// Verificar si hay categorías
$categoria = CategoriaIndicador::first();

if (!$categoria) {
    echo "Creando categoría...\n";
    $categoria = CategoriaIndicador::create(['nombre' => 'Categoría de Prueba']);
    echo "Categoría creada con ID: {$categoria->id}\n";
} else {
    echo "Categoría existente: {$categoria->nombre} (ID: {$categoria->id})\n";
}

// Obtener carrera, curso académico y año académico
$carrera = Carrera::first();
$anio = AnioAcademico::first();

if (!$carrera) {
    echo "ERROR: No hay carreras en la base de datos\n";
    exit(1);
}

if (!$anio) {
    echo "Creando año académico...\n";
    $anio = AnioAcademico::create([
        'nombre' => 'Año 1',
        'carrera_id' => $carrera->id
    ]);
    echo "Año académico creado con ID: {$anio->id}\n";
}

echo "Carrera: {$carrera->nombre} (ID: {$carrera->id})\n";
$nombreAnio = $anio->nombre ?? $anio->id;
echo "Año Académico: {$nombreAnio} (ID: {$anio->id})\n";

// Crear indicador de tipo carrera
echo "\nCreando indicador de tipo carrera...\n";
$indicador = Indicador::create([
    'nombre' => 'Indicador de Prueba - Carrera',
    'categoria_indicador_id' => $categoria->id
]);

echo "Indicador creado con ID: {$indicador->id}\n";

// Asignar a carrera
$indCarrera = IndicadorCarrera::create([
    'nombre' => 'Indicador de Prueba - Carrera',
    'indicador_id' => $indicador->id,
    'carrera_id' => $carrera->id,
    'curso_academico_id' => $anio->id
]);

echo "IndicadorCarrera creado con ID: {$indCarrera->id}\n";
echo "\n✓ Indicador de prueba creado exitosamente!\n";
