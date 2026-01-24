<?php
require 'bootstrap/app.php';

$app = app();
$db = $app->make('db');

echo "=== RESUMEN DE DATOS POBLADOS ===\n\n";

$stats = [
    'Facultades' => $db->table('facultades')->count(),
    'Departamentos' => $db->table('departamentos')->count(),
    'Carreras' => $db->table('carreras')->count(),
    'Años Académicos' => $db->table('anios_academicos')->count(),
    'Grupos' => $db->table('grupos')->count(),
    'Alumnos' => $db->table('alumnos')->count(),
    'Bajas Estudiantes' => $db->table('bajas_estudiantes')->count(),
    'Evaluaciones Integrales' => $db->table('evaluaciones_integrales')->count(),
    'Profesores' => $db->table('profesores')->count(),
    'Categorías Docentes' => $db->table('categorias_docentes')->count(),
    'Categorías Científicas' => $db->table('categorias_cientificas')->count(),
    'Grados de Título' => $db->table('grados_titulo')->count(),
    'Cursos Académicos' => $db->table('curso_academico')->count(),
    'Versiones' => $db->table('versiones')->count(),
    'Modificaciones' => $db->table('modificaciones')->count(),
    'Indicadores' => $db->table('indicadores')->count(),
    'Categorías Indicador' => $db->table('categorias_indicador')->count(),
];

foreach ($stats as $tabla => $cantidad) {
    echo "$tabla: $cantidad\n";
}

echo "\n=== RELACIONES POBLADAS ===\n\n";

$relaciones = [
    'Profesor-Categoría-Grado' => $db->table('profesor_categoria_grado')->count(),
    'Miembro Departamento' => $db->table('miembro_departamento')->count(),
    'Jefe Departamento' => $db->table('jefe_departamento')->count(),
    'Alumno Ayudante' => $db->table('alumno_ayudante')->count(),
    'Carrera-Departamento' => $db->table('carrera_departamento')->count(),
    'Carrera-Plan' => $db->table('carrera_plan')->count(),
    'Facultad-Departamento' => $db->table('facultad_departamento')->count(),
    'Indicador-Facultad' => $db->table('indicador_facultad')->count(),
    'Indicador-Departamento' => $db->table('indicador_departamento')->count(),
    'Indicador-Carrera' => $db->table('indicador_carrera')->count(),
    'Indicador-Grupo' => $db->table('indicador_grupo')->count(),
    'Indicador-Año' => $db->table('indicador_anno')->count(),
    'Indicador-Categoría' => $db->table('indicador_categoria')->count(),
    'Indicador-Curso' => $db->table('indicador_curso')->count(),
    'Version-Modificaciones' => $db->table('version_modificaciones')->count(),
];

foreach ($relaciones as $relacion => $cantidad) {
    echo "$relacion: $cantidad\n";
}
