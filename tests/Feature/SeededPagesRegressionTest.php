<?php

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\DB;

test('application pages render with seeded academic data and lazy loading protection', function () {
    $this->seed(DatabaseSeeder::class);
    foreach (['indicador', 'baja_estudiante', 'estudiante_ayudante'] as $table) {
        expect(DB::table($table)->count())->toBeGreaterThan(0);
    }

    $administrator = User::where('role', 'administrador')->firstOrFail();
    $this->actingAs($administrator);

    foreach (['indicadores', 'indicadores.bajas', 'alumnoayudante', 'roles'] as $route) {
        $this->get(route($route))->assertOk();
    }
});
