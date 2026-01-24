<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DataController;

// Facultades
Route::get('/facultades', [DataController::class, 'getFacultades']);
Route::post('/facultades', [DataController::class, 'storeFacultad']);

// Departamentos
Route::get('/departamentos', [DataController::class, 'getDepartamentos']);
Route::post('/departamentos', [DataController::class, 'storeDepartamento']);

// Carreras
Route::get('/carreras', [DataController::class, 'getCarreras']);
Route::post('/carreras', [DataController::class, 'storeCarrera']);

// Debug endpoint
Route::get('/debug/planes-by-carrera/{carreraId}', function($carreraId) {
    $planes = \App\Models\PlanEstudio::where('carrera_id', $carreraId)->get();
    $carrera = \App\Models\Carrera::find($carreraId);
    return response()->json([
        'carrera_id' => $carreraId,
        'carrera' => $carrera,
        'planes_count' => count($planes),
        'planes' => $planes
    ]);
});

// Planes de Estudio
Route::get('/planes-estudio', [DataController::class, 'getPlanesEstudio']);
Route::get('/planes-by-carrera/{carreraId}', [DataController::class, 'getPlanesByCarrera']);
Route::post('/planes-estudio', [DataController::class, 'storePlanEstudio']);

// Disciplinas
Route::get('/disciplinas', [DataController::class, 'getDisciplinas']);
Route::post('/disciplinas', [DataController::class, 'storeDisciplina']);

// Asignaturas
Route::get('/asignaturas', [DataController::class, 'getAsignaturas']);
Route::post('/asignaturas', [DataController::class, 'storeAsignatura']);

// Currículos
Route::get('/curriculos', [DataController::class, 'getCurriculos']);
Route::post('/curriculos', [DataController::class, 'storeCurriculo']);

// Cursos Académicos (tabla curso_academico: períodos como 2024-2025)
Route::get('/cursos-academicos', [DataController::class, 'getAniosAcademicos']);

// Años Académicos (tabla anio_academico: 1er año, 2do año, etc.)
Route::get('/anios-academicos', [DataController::class, 'getYearsAcademicos']);
Route::post('/anios-academicos', [DataController::class, 'storeAnioAcademico']);

// Grupos
Route::get('/grupos', [DataController::class, 'getGrupos']);
Route::post('/grupos', [DataController::class, 'storeGrupo']);

// Alumnos
Route::get('/alumnos', [DataController::class, 'getAlumnos']);
Route::post('/alumnos', [DataController::class, 'storeAlumno']);

// Categorías de Indicador
Route::get('/categorias-indicador', [DataController::class, 'getCategoriasIndicador']);
Route::post('/categorias-indicador', [DataController::class, 'storeCategoriaIndicador']);

// Indicadores
Route::get('/indicadores', [DataController::class, 'getIndicadores']);
Route::get('/indicadores-debug', function() {
    return response()->json([
        'indicador_carrera' => \App\Models\IndicadorCarrera::count(),
        'indicador_grupo' => \App\Models\IndicadorGrupo::count(),
        'indicador_facultad' => \App\Models\IndicadorFacultad::count(),
        'indicador_departamento' => \App\Models\IndicadorDepartamento::count(),
        'indicador_anno' => \App\Models\IndicadorAnio::count(),
    ]);
});
Route::post('/indicadores', [DataController::class, 'storeIndicador']);
Route::delete('/indicadores/{id}', [DataController::class, 'destroyIndicador']);

// Indicadores por Carrera
Route::get('/indicadores-carrera', [DataController::class, 'getIndicadoresCarrera']);
Route::post('/indicadores-carrera', [DataController::class, 'storeIndicadoresCarrera']);

// Crear y asignar indicador en un solo paso
Route::post('/indicadores/create-and-assign', [DataController::class, 'createAndAssignIndicador']);

// Genérico
Route::get('/tabla/{table}', [DataController::class, 'getTableData']);

// Alumno Ayudante
Route::post('/alumnoayudante', [\App\Http\Controllers\AlumnoAyudanteController::class, 'storeFromForm']);
Route::delete('/alumnoayudante/{id}', [\App\Http\Controllers\AlumnoAyudanteController::class, 'destroy']);
