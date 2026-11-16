<?php

use App\Models\User;

test('valid credentials start an authenticated session', function () {
    $user = User::factory()->withoutTwoFactor()->create([
        'email' => 'admin@example.com',
        'password' => 'secret-password',
    ]);

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'secret-password',
    ])->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($user);
});

test('application pages render for authenticated administrators', function (string $route) {
    $this->actingAs(User::factory()->withoutTwoFactor()->create(['role' => 'administrador']));

    $this->get(route($route))->assertOk();
})->with([
    'dashboard',
    'roles',
    'profesor',
    'planestudio',
    'alumnoayudante',
    'indicadores',
    'indicadores.bajas',
]);

test('api rejects unauthenticated requests', function () {
    $this->getJson('/api/facultades')->assertUnauthorized();
});

test('api reads and validates writes for authenticated users', function () {
    $this->actingAs(User::factory()->withoutTwoFactor()->create(['role' => 'administrador']));

    $this->getJson('/api/facultades')->assertOk()->assertExactJson([]);
    $this->postJson('/api/facultades', ['nombre' => 'FMFC'])
        ->assertCreated()
        ->assertJsonPath('nombre', 'FMFC');
    $this->postJson('/api/facultades', ['nombre' => 'FMFC'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('nombre');
});
