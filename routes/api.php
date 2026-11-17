<?php

use App\Http\Controllers\AlumnoAyudanteController;
use App\Http\Controllers\Api\DataController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'throttle:60,1'])->group(function () {
    Route::get('/facultades', [DataController::class, 'getFacultades']);
    Route::post('/facultades', [DataController::class, 'storeFacultad'])->middleware(['can:write-academic-data', 'department.scope']);
    Route::get('/departamentos', [DataController::class, 'getDepartamentos']);
    Route::post('/departamentos', [DataController::class, 'storeDepartamento'])->middleware(['can:write-academic-data', 'department.scope']);
    Route::get('/carreras', [DataController::class, 'getCarreras']);
    Route::post('/carreras', [DataController::class, 'storeCarrera'])->middleware(['can:write-academic-data', 'department.scope']);
    Route::get('/planes-estudio', [DataController::class, 'getPlanesEstudio']);
    Route::get('/planes-by-carrera/{carreraId}', [DataController::class, 'getPlanesByCarrera']);
    Route::post('/planes-estudio', [DataController::class, 'storePlanEstudio'])->middleware(['can:write-academic-data', 'department.scope']);
    Route::get('/disciplinas', [DataController::class, 'getDisciplinas']);
    Route::post('/disciplinas', [DataController::class, 'storeDisciplina'])->middleware(['can:write-academic-data', 'department.scope']);
    Route::get('/asignaturas', [DataController::class, 'getAsignaturas']);
    Route::post('/asignaturas', [DataController::class, 'storeAsignatura'])->middleware(['can:write-academic-data', 'department.scope']);
    Route::get('/curriculos', [DataController::class, 'getCurriculos']);
    Route::post('/curriculos', [DataController::class, 'storeCurriculo'])->middleware(['can:write-academic-data', 'department.scope']);
    Route::get('/cursos-academicos', [DataController::class, 'getAniosAcademicos']);
    Route::get('/anios-academicos', [DataController::class, 'getYearsAcademicos']);
    Route::post('/anios-academicos', [DataController::class, 'storeAnioAcademico'])->middleware(['can:write-academic-data', 'department.scope']);
    Route::get('/grupos', [DataController::class, 'getGrupos']);
    Route::post('/grupos', [DataController::class, 'storeGrupo'])->middleware(['can:write-academic-data', 'department.scope']);
    Route::get('/alumnos', [DataController::class, 'getAlumnos']);
    Route::post('/alumnos', [DataController::class, 'storeAlumno'])->middleware(['can:write-academic-data', 'department.scope']);
    Route::get('/categorias-indicador', [DataController::class, 'getCategoriasIndicador']);
    Route::post('/categorias-indicador', [DataController::class, 'storeCategoriaIndicador'])->middleware(['can:write-academic-data', 'department.scope']);
    Route::get('/indicadores', [DataController::class, 'getIndicadores']);
    Route::post('/indicadores', [DataController::class, 'storeIndicador'])->middleware('can:create-indicators');
    Route::delete('/indicadores/{id}', [DataController::class, 'destroyIndicador'])->middleware('can:manage-indicator-definitions');
    Route::get('/indicadores-carrera', [DataController::class, 'getIndicadoresCarrera']);
    Route::post('/indicadores-carrera', [DataController::class, 'storeIndicadoresCarrera'])->middleware('can:manage-indicator-definitions');
    Route::post('/indicadores/create-and-assign', [DataController::class, 'createAndAssignIndicador']);

    Route::post('/alumnoayudante', [AlumnoAyudanteController::class, 'storeFromForm']);
    Route::delete('/alumnoayudante/{id}', [AlumnoAyudanteController::class, 'destroy']);
    Route::get('/profesores-by-asignatura/{asignaturaId}', [AlumnoAyudanteController::class, 'getProfesoresByAsignatura']);
    Route::get('/asignaturas-by-profesor/{profesorId}', [AlumnoAyudanteController::class, 'getAsignaturasByProfesor']);
});
