<?php

use Database\Seeders\DatabaseSeeder;

test('production seeding inserts the coherent catalog without demo people or operational records', function () {
    $originalEnvironment = $this->app->environment();
    $this->app->instance('env', 'production');

    try {
        $this->artisan('db:seed', ['--class' => DatabaseSeeder::class, '--force' => true])
            ->assertSuccessful();

        $this->assertDatabaseCount('users', 7);
        foreach (['administrador', 'ppa', 'jefe_departamento', 'decano', 'rector', 'vicerrector', 'secretaria'] as $role) {
            $this->assertDatabaseHas('users', ['role' => $role]);
        }
        $this->assertDatabaseCount('facultad', 2);
        $this->assertDatabaseCount('carrera', 7);
        $this->assertDatabaseCount('asignatura', 84);
        $this->assertDatabaseHas('asignatura', [
            'codigo' => 'INF-101',
            'nombre' => 'Introducción a la Programación',
        ]);
        $this->assertDatabaseCount('estudiante', 0);
        $this->assertDatabaseCount('profesor', 0);
        $this->assertDatabaseCount('indicador', 0);
    } finally {
        $this->app->instance('env', $originalEnvironment);
    }
});
