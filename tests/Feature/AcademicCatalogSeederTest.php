<?php

use Database\Seeders\AcademicCatalogSeeder;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

test('the academic catalog seeder creates a coherent and complete structure', function () {
    $this->seed(AcademicCatalogSeeder::class);

    $this->assertDatabaseCount('facultad', 2);
    $this->assertDatabaseCount('departamento', 6);
    $this->assertDatabaseCount('carrera', 7);
    $this->assertDatabaseCount('plan_estudio', 7);
    $this->assertDatabaseCount('disciplina', 28);
    $this->assertDatabaseCount('asignatura', 84);
    $this->assertDatabaseCount('anio_academico', 28);
    $this->assertDatabaseCount('grupo', 56);

    $this->assertDatabaseHas('asignatura', [
        'codigo' => 'INF-101',
        'nombre' => 'Introducción a la Programación',
    ]);
    $this->assertDatabaseHas('asignatura', [
        'codigo' => 'LET-112',
        'nombre' => 'Taller de Investigación Literaria',
    ]);
    $this->assertDatabaseHas('anio_academico', [
        'nombre' => '3.º año · Ingeniería Informática',
        'nivel' => 3,
    ]);

    expect(DB::table('asignatura')->where('nombre', 'like', 'Disciplina %')->doesntExist())->toBeTrue()
        ->and(DB::table('anio_academico')->where('nombre', 'like', '%Carrera 0%')->doesntExist())->toBeTrue()
        ->and(Schema::hasIndex('anio_academico', ['carrera_id', 'nivel'], 'unique'))->toBeTrue()
        ->and(Schema::hasIndex('profesor_asignatura', ['profesor_id', 'asignatura_id', 'curso_academico_id'], 'unique'))->toBeTrue();
});

test('the academic catalog seeder is idempotent', function () {
    $this->seed(AcademicCatalogSeeder::class);
    $firstCounts = collect(['facultad', 'departamento', 'carrera', 'plan_estudio', 'disciplina', 'asignatura', 'anio_academico', 'grupo'])
        ->mapWithKeys(fn (string $table): array => [$table => DB::table($table)->count()]);

    $this->seed(AcademicCatalogSeeder::class);

    $secondCounts = $firstCounts->keys()
        ->mapWithKeys(fn (string $table): array => [$table => DB::table($table)->count()]);

    expect($secondCounts->all())->toBe($firstCounts->all());
});

test('the complete database seeder is idempotent', function () {
    $tables = ['users', 'facultad', 'departamento', 'carrera', 'asignatura', 'profesor', 'estudiante', 'profesor_asignatura', 'estudiante_ayudante'];

    $this->seed(DatabaseSeeder::class);
    $firstCounts = collect($tables)->mapWithKeys(fn (string $table): array => [$table => DB::table($table)->count()]);

    $this->seed(DatabaseSeeder::class);
    $secondCounts = collect($tables)->mapWithKeys(fn (string $table): array => [$table => DB::table($table)->count()]);

    expect($secondCounts->all())->toBe($firstCounts->all())
        ->and($secondCounts['profesor'])->toBe(7)
        ->and($secondCounts['estudiante'])->toBe(14)
        ->and($secondCounts['estudiante_ayudante'])->toBe(7);
});
