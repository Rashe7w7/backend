<?php
use Illuminate\Support\Facades\DB;
use App\Models\Alumno;
use App\Models\Grupo;
use App\Models\AnioAcademico;
use App\Models\Carrera;
use App\Models\Profesor;
use App\Models\Asignatura;

echo "=== AUDITORIA DE BASE DE DATOS ===\n\n";

// 1. Verificar alumnos con grupos inválidos
echo "1. Alumnos con grupos no válidos:\n";
$alumnosInvalidos = Alumno::whereNotExists(function($query) {
    $query->select(DB::raw(1))->from('grupos')->whereColumn('grupos.id', 'alumnos.grupo_id');
})->count();
echo "   Encontrados: $alumnosInvalidos\n";

// 2. Verificar grupos con años académicos inválidos
echo "\n2. Grupos con años académicos no válidos:\n";
$gruposInvalidos = Grupo::whereNotExists(function($query) {
    $query->select(DB::raw(1))->from('anios_academicos')->whereColumn('anios_academicos.id', 'grupos.anio_academico_id');
})->count();
echo "   Encontrados: $gruposInvalidos\n";

// 3. Verificar años académicos con carreras inválidas
echo "\n3. Años académicos con carreras no válidas:\n";
$aniosInvalidos = AnioAcademico::whereNotExists(function($query) {
    $query->select(DB::raw(1))->from('carreras')->whereColumn('carreras.id', 'anios_academicos.carrera_id');
})->count();
echo "   Encontrados: $aniosInvalidos\n";

// 4. Verificar asignaturas con disciplinas inválidas
echo "\n4. Asignaturas con disciplinas no válidas:\n";
$asignaturasInvalidas = Asignatura::whereNotExists(function($query) {
    $query->select(DB::raw(1))->from('disciplinas')->whereColumn('disciplinas.id', 'asignaturas.disciplina_id');
})->count();
echo "   Encontrados: $asignaturasInvalidas\n";

// 5. Verificar profesor_asignatura con referencias inválidas
echo "\n5. Relaciones profesor_asignatura con referencias inválidas:\n";
$profAsignInvalidos = DB::table('profesor_asignatura')
    ->whereNotExists(function($query) {
        $query->select(DB::raw(1))->from('profesores')->whereColumn('profesores.id', 'profesor_asignatura.profesor_id');
    })->count();
echo "   Profesores inválidos: $profAsignInvalidos\n";

$asignAsignInvalidos = DB::table('profesor_asignatura')
    ->whereNotExists(function($query) {
        $query->select(DB::raw(1))->from('asignaturas')->whereColumn('asignaturas.id', 'profesor_asignatura.asignatura_id');
    })->count();
echo "   Asignaturas inválidas: $asignAsignInvalidos\n";

// 6. Verificar alumnos ayudantes con referencias inválidas
echo "\n6. Alumnos ayudantes con referencias inválidas:\n";
$aaAlumnosInvalidos = DB::table('alumno_ayudante')
    ->whereNotExists(function($query) {
        $query->select(DB::raw(1))->from('alumnos')->whereColumn('alumnos.id', 'alumno_ayudante.alumno_id');
    })->count();
echo "   Alumnos inválidos: $aaAlumnosInvalidos\n";

$aaAsignInvalidos = DB::table('alumno_ayudante')
    ->whereNotExists(function($query) {
        $query->select(DB::raw(1))->from('asignaturas')->whereColumn('asignaturas.id', 'alumno_ayudante.asignatura_id');
    })->count();
echo "   Asignaturas inválidas: $aaAsignInvalidos\n";

// 7. Mostrar estadísticas generales
echo "\n7. Estadísticas generales:\n";
echo "   Facultades: " . DB::table('facultades')->count() . "\n";
echo "   Carreras: " . DB::table('carreras')->count() . "\n";
echo "   Años académicos: " . DB::table('anios_academicos')->count() . "\n";
echo "   Grupos: " . DB::table('grupos')->count() . "\n";
echo "   Alumnos: " . DB::table('alumnos')->count() . "\n";
echo "   Profesores: " . DB::table('profesores')->count() . "\n";
echo "   Asignaturas: " . DB::table('asignaturas')->count() . "\n";
echo "   Profesor-Asignatura: " . DB::table('profesor_asignatura')->count() . "\n";
echo "   Alumno Ayudante: " . DB::table('alumno_ayudante')->count() . "\n";
echo "   Planes de Estudio: " . DB::table('planes_estudio')->count() . "\n";

// 8. Verificar planes de estudio
echo "\n8. Planes de estudio con carreras inválidas:\n";
$planesInvalidos = DB::table('planes_estudio')
    ->whereNotExists(function($query) {
        $query->select(DB::raw(1))->from('carreras')->whereColumn('carreras.id', 'planes_estudio.carrera_id');
    })->count();
echo "   Encontrados: $planesInvalidos\n";

echo "\n=== FIN AUDITORIA ===\n";
