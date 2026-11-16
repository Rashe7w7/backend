<?php

use App\Http\Controllers\AlumnoAyudanteController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\BajasController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\IndicadorController;
use App\Http\Controllers\PpaController;
use App\Http\Controllers\ReportExportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
})->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/roles', [UserController::class, 'index'])
        ->middleware('can:manage-users')->name('roles');
    Route::post('/roles', [UserController::class, 'store'])
        ->middleware('can:manage-users')->name('roles.store');
    Route::put('/roles/{user}', [UserController::class, 'update'])
        ->middleware('can:manage-users')->name('roles.update');
    Route::post('/roles/{user}/alcances', [UserController::class, 'storeScope'])
        ->middleware('can:manage-users')->name('roles.scopes.store');
    Route::delete('/roles/{user}/alcances/{scope}', [UserController::class, 'destroyScope'])
        ->middleware('can:manage-users')->name('roles.scopes.destroy');
    Route::view('/profesor', 'tables.profesor')->name('profesor');
    Route::view('/planestudio', 'tables.planestudio')->name('planestudio');

    Route::get('/profesores-principales', [PpaController::class, 'index'])->name('ppa.index');
    Route::post('/profesores-principales', [PpaController::class, 'store'])->name('ppa.store');
    Route::get('/profesores-principales/{asignacionPpa}/estudiantes', [PpaController::class, 'students'])->name('ppa.students');

    Route::get('/alumnoayudante', [AlumnoAyudanteController::class, 'index'])->name('alumnoayudante');

    Route::get('/indicadores', [IndicadorController::class, 'index'])->name('indicadores');
    Route::post('/indicadores', [IndicadorController::class, 'store'])->middleware('can:create-indicators')->name('indicadores.store');
    Route::post('/indicadores/aplicaciones', [IndicadorController::class, 'apply'])->name('indicadores.apply');
    Route::delete('/indicadores/aplicaciones/{type}/{application}', [IndicadorController::class, 'destroyApplication'])->name('indicadores.applications.destroy');
    Route::post('/indicadores/preparar-csv', [ReportExportController::class, 'csv'])->name('indicadores.csv');
    Route::post('/valores', [IndicadorController::class, 'storeValores'])->middleware('can:manage-users')->name('valores.store');
    Route::get('/indicadores/bajas', [BajasController::class, 'index'])->name('indicadores.bajas');
    Route::get('/informes/indicadores/excel', [ReportExportController::class, 'excel'])->name('reports.indicadores.excel');
    Route::get('/informes/indicadores/pdf', [ReportExportController::class, 'pdf'])->name('reports.indicadores.pdf');
    Route::get('/auditoria', AuditController::class)->middleware('can:view-audit')->name('audit.index');

    Route::middleware(['can:manage-users'])->group(function () {
        Route::get('/importar', [ImportController::class, 'index'])->name('imports.index');
        Route::post('/importar/preview', [ImportController::class, 'preview'])->name('imports.preview');
        Route::post('/importar', [ImportController::class, 'store'])->name('imports.store');
    });

    Route::post('/categorias', [CategoriaController::class, 'store'])->middleware('can:manage-users')->name('categorias.store');
    Route::delete('/categorias/{categoria}', [CategoriaController::class, 'destroy'])->middleware('can:manage-users')->name('categorias.destroy');

    Route::get('/tablas', function () {
        abort_unless(app()->environment('local'), 404);

        $excluded = [
            'cache', 'cache_locks', 'sessions', 'job_batches', 'jobs', 'failed_jobs',
            'password_reset_tokens', 'users',
        ];

        $tablesData = collect(Schema::getTables())
            ->pluck('name')
            ->reject(fn (string $table) => in_array($table, $excluded, true))
            ->map(fn (string $table) => [
                'name' => $table,
                'columns' => Schema::getColumns($table),
                'count' => DB::table($table)->count(),
                'data' => DB::table($table)->limit(5)->get(),
            ])
            ->all();

        return view('tablas', compact('tablesData'));
    })->name('tables');
});
