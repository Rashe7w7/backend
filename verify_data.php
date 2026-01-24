<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Verificar que las tablas y modelos existen
echo "=== Verificando datos disponibles ===\n";

echo "\nCursos Académicos:\n";
$cursos = \App\Models\CursoAcademico::all();
foreach ($cursos as $curso) {
    echo "ID: " . $curso->id . " | Nombre: " . $curso->nombre . "\n";
}

echo "\nAsignaturas (primeros 5):\n";
$asignaturas = \App\Models\Asignatura::limit(5)->get();
foreach ($asignaturas as $asig) {
    echo "ID: " . $asig->id . " | Nombre: " . $asig->nombre . "\n";
}

echo "\nProfesores (primeros 5):\n";
$profesores = \App\Models\Profesor::limit(5)->get();
foreach ($profesores as $prof) {
    echo "ID: " . $prof->id . " | Nombre: " . $prof->nombre . "\n";
}

echo "\nRelaciones Profesor-Asignatura (primeras 5):\n";
$relaciones = \App\Models\ProfesorAsignatura::with('profesor', 'asignatura')->limit(5)->get();
foreach ($relaciones as $rel) {
    echo "ID: " . $rel->id . " | Profesor: " . $rel->profesor->nombre . " | Asignatura: " . $rel->asignatura->nombre . "\n";
}
