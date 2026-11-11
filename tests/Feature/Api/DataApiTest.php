<?php

use Illuminate\Support\Facades\DB;

test('list endpoints return json arrays', function (string $uri) {
    $this->getJson($uri)->assertOk()->assertJsonIsArray();
})->with([
    '/api/facultades',
    '/api/departamentos',
    '/api/carreras',
    '/api/indicadores',
    '/api/cursos-academicos',
]);

test('list returns inserted rows', function () {
    $id = DB::table('facultad')->insertGetId(['nombre' => 'Informatica']);
    DB::table('departamento')->insert(['nombre' => 'DIS', 'facultad_id' => $id]);
    DB::table('carrera')->insert(['nombre' => 'Ingenieria', 'facultad_id' => $id]);

    $this->getJson('/api/facultades')->assertOk()->assertJsonCount(1)->assertJsonFragment(['nombre' => 'Informatica']);
    $this->getJson('/api/departamentos')->assertOk()->assertJsonCount(1);
    $this->getJson('/api/carreras')->assertOk()->assertJsonCount(1);
});

test('storing a facultad returns 201 and duplicates return 422', function () {
    $this->postJson('/api/facultades', ['nombre' => 'Ciencias'])
        ->assertCreated()
        ->assertJsonFragment(['nombre' => 'Ciencias']);

    $this->assertDatabaseHas('facultad', ['nombre' => 'Ciencias']);

    $this->postJson('/api/facultades', ['nombre' => 'Ciencias'])
        ->assertStatus(422)
        ->assertJsonValidationErrors('nombre');
});

test('storing a carrera works with a valid facultad', function () {
    $id = DB::table('facultad')->insertGetId(['nombre' => 'Ciencias']);

    $this->postJson('/api/carreras', ['nombre' => 'Fisica', 'facultad_id' => $id])->assertCreated();
    $this->assertDatabaseHas('carrera', ['nombre' => 'Fisica', 'facultad_id' => $id]);
});

test('storing a carrera with a nonexistent facultad returns 422', function () {
    $this->postJson('/api/carreras', ['nombre' => 'Fisica', 'facultad_id' => 9999])
        ->assertStatus(422)
        ->assertJsonValidationErrors('facultad_id');
});

test('storing an indicador returns 201 and duplicates return 422', function () {
    $payload = ['nombre' => 'Retencion', 'especificacion' => 'carrera'];

    $this->postJson('/api/indicadores', $payload)
        ->assertCreated()
        ->assertJsonFragment(['nombre' => 'Retencion', 'especificacion' => 'carrera']);

    $this->assertDatabaseHas('indicador', ['nombre' => 'Retencion']);

    $this->postJson('/api/indicadores', $payload)
        ->assertStatus(422)
        ->assertJsonValidationErrors('nombre');
});

test('storing an indicador with invalid especificacion returns 422', function () {
    $this->postJson('/api/indicadores', ['nombre' => 'X', 'especificacion' => 'otro'])
        ->assertStatus(422)
        ->assertJsonValidationErrors('especificacion');
});

test('deleting an indicador removes the row', function () {
    $id = $this->postJson('/api/indicadores', ['nombre' => 'Temporal', 'especificacion' => 'grupo'])
        ->assertCreated()
        ->json('id');

    $this->deleteJson("/api/indicadores/{$id}")->assertOk();

    $this->assertDatabaseMissing('indicador', ['id' => $id]);
});

test('deleting a missing indicador returns 404', function () {
    $this->deleteJson('/api/indicadores/9999')->assertNotFound();
});

test('table endpoint returns rows for a known table', function () {
    DB::table('facultad')->insert(['nombre' => 'Ciencias']);

    $this->getJson('/api/tabla/facultades')->assertOk()->assertJsonCount(1);
});

test('table endpoint returns 404 for an unknown table', function () {
    $this->getJson('/api/tabla/nope')->assertNotFound();
});
