<?php

use App\Models\User;

test('invalid credentials never establish an authenticated session', function () {
    $user = User::factory()->withoutTwoFactor()->create(['password' => 'correct-password']);

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'incorrect-password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('logout ends the session and blocks subsequent protected requests', function () {
    $user = User::factory()->withoutTwoFactor()->create();

    $this->actingAs($user)->post(route('logout'))->assertRedirect('/');

    $this->assertGuest();
    $this->get(route('dashboard'))->assertRedirect(route('login'));
    $this->getJson('/api/alumnos')->assertUnauthorized();
});

test('public registration cannot create an administrator', function () {
    $this->get('/register')->assertNotFound();
    $this->post('/register', [
        'name' => 'Uninvited administrator',
        'email' => 'uninvited@example.com',
        'password' => 'strong-password',
        'password_confirmation' => 'strong-password',
        'role' => 'administrador',
    ])->assertNotFound();

    $this->assertDatabaseMissing('users', ['email' => 'uninvited@example.com']);
});

test('guests cannot access protected application screens', function (string $route) {
    $this->get(route($route))->assertRedirect(route('login'));
})->with(['roles', 'indicadores', 'indicadores.bajas', 'alumnoayudante', 'planestudio']);

test('read only roles can consult data but cannot write or manage users', function (string $role) {
    $user = User::factory()->withoutTwoFactor()->create(['role' => $role]);
    $this->actingAs($user);

    $this->getJson('/api/facultades')->assertOk();
    $this->postJson('/api/facultades', ['nombre' => 'Forbidden faculty'])->assertForbidden();
    $this->deleteJson('/api/indicadores/999999')->assertForbidden();
    $this->postJson('/api/indicadores', [])->assertForbidden();
    $this->post('/valores', [])->assertForbidden();
    $this->get(route('roles'))->assertForbidden();
    $this->post(route('roles.store'), [])->assertForbidden();
    $this->put(route('roles.update', $user), ['role' => 'administrador'])->assertForbidden();

    $this->assertDatabaseMissing('facultad', ['nombre' => 'Forbidden faculty']);
    expect($user->fresh()->role)->toBe($role);
})->with(['consulta', 'profesor']);

test('administrators cannot accidentally remove their own administrative access', function () {
    $admin = User::factory()->withoutTwoFactor()->create(['role' => 'administrador']);

    $this->actingAs($admin)->from(route('roles'))
        ->put(route('roles.update', $admin), ['role' => 'consulta', 'profesor_id' => null])
        ->assertRedirect(route('roles'))
        ->assertSessionHasErrors('role');

    expect($admin->fresh()->role)->toBe('administrador');
});

test('administrators can grant a read only user a role', function () {
    $admin = User::factory()->withoutTwoFactor()->create(['role' => 'administrador']);
    $user = User::factory()->withoutTwoFactor()->create(['role' => 'consulta']);

    $this->actingAs($admin)->from(route('roles'))
        ->put(route('roles.update', $user), ['role' => 'administrador', 'profesor_id' => null])
        ->assertRedirect(route('roles'))
        ->assertSessionHasNoErrors();

    expect($user->fresh()->role)->toBe('administrador');
});

test('session authenticated api writes require a valid csrf token outside the test bypass', function () {
    $admin = User::factory()->withoutTwoFactor()->create(['role' => 'administrador']);
    $this->actingAs($admin);

    // Laravel skips CSRF checks while the application environment is testing.
    $originalEnvironment = $this->app->environment();
    $this->app->instance('env', 'local');

    try {
        $this->postJson('/api/facultades', ['nombre' => 'Missing CSRF'])->assertStatus(419);
        $this->assertDatabaseMissing('facultad', ['nombre' => 'Missing CSRF']);

        $token = str_repeat('a', 40);
        $this->withSession(['_token' => $token])
            ->withHeader('X-CSRF-TOKEN', $token)
            ->postJson('/api/facultades', ['nombre' => 'Valid CSRF'])
            ->assertCreated();

        $this->assertDatabaseHas('facultad', ['nombre' => 'Valid CSRF']);
    } finally {
        $this->app->instance('env', $originalEnvironment);
    }
});
