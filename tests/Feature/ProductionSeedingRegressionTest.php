<?php

use Database\Seeders\DatabaseSeeder;

test('production seeding never inserts demo users or fictional academic data', function () {
    $originalEnvironment = $this->app->environment();
    $this->app->instance('env', 'production');

    try {
        $this->artisan('db:seed', ['--class' => DatabaseSeeder::class, '--force' => true])
            ->assertSuccessful();

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('estudiante', 0);
        $this->assertDatabaseCount('profesor', 0);
        $this->assertDatabaseCount('indicador', 0);
    } finally {
        $this->app->instance('env', $originalEnvironment);
    }
});
