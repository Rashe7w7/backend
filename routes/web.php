<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BajasController;
use App\Http\Controllers\IndicadorController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\AlumnoAyudanteController;
use App\Models\Carrera;
use App\Models\AnioAcademico;
use App\Models\Plan;

/*
|--------------------------------------------------------------------------
| Página principal → LOGIN
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function () {
    // Por ahora, solo redireccionamos al dashboard
    return redirect()->route('dashboard');
})->name('login.post');

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/roles', function () {
    return view('roles');
})->name('roles');

Route::get('/alumnoayudante', [AlumnoAyudanteController::class, 'index'])->name('alumnoayudante');
Route::post('/alumnoayudante', [AlumnoAyudanteController::class, 'store'])->name('alumnoayudante.store');
Route::delete('/alumnoayudante/{id}', [AlumnoAyudanteController::class, 'destroy'])->name('alumnoayudante.destroy');
Route::get('/api/profesores-by-asignatura/{asignaturaId}', [AlumnoAyudanteController::class, 'getProfesoresByAsignatura'])->name('api.profesores.by.asignatura');
Route::get('/api/asignaturas-by-profesor/{profesorId}', [AlumnoAyudanteController::class, 'getAsignaturasByProfesor'])->name('api.asignaturas.by.profesor');

Route::get('/profesor', function () {
    return view('tables.profesor');
})->name('profesor');

Route::get('/planestudio', function () {
    $carreras = Carrera::all();
    $anios = AnioAcademico::all();
    
    return view('tables.planestudio', compact('carreras', 'anios'));
})->name('planestudio');

Route::get('/indicadores', [IndicadorController::class, 'index'])->name('indicadores');
Route::post('/indicadores', [IndicadorController::class, 'store'])->name('indicadores.store');
Route::post('/valores', [IndicadorController::class, 'storeValores'])->name('valores.store');

Route::post('/categorias', [CategoriaController::class, 'store'])->name('categorias.store');

Route::delete('/categorias/{categoria}', [CategoriaController::class, 'destroy'])->name('categorias.destroy');

/*
|--------------------------------------------------------------------------
| Indicadores de bajas
|--------------------------------------------------------------------------
*/
Route::get('/indicadores/bajas', [BajasController::class, 'index'])
    ->name('indicadores.bajas');

/*
|--------------------------------------------------------------------------
| Tablas de la base de datos
|--------------------------------------------------------------------------
*/
Route::get('/tablas', function () {

    $tables = Schema::getTables();
    $tableNames = array_column($tables, 'name');

    $appTables = array_filter($tableNames, function ($table) {
        return !in_array($table, [
            'cache',
            'sessions',
            'job_batches',
            'jobs',
            'failed_jobs',
            'password_reset_tokens'
        ]);
    });

    $tablesData = [];

    foreach ($appTables as $table) {
        $columns = Schema::getColumns($table);
        $count = DB::table($table)->count();

        $tablesData[] = [
            'name' => $table,
            'columns' => $columns,
            'count' => $count,
            'data' => DB::table($table)->limit(5)->get()
        ];
    }

    return view('tablas', compact('tablesData'));

})->name('tables');
