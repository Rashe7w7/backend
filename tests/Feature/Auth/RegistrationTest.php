<?php

use Illuminate\Support\Facades\Route;

test('public registration is disabled', function () {
    expect(Route::has('register'))->toBeFalse()
        ->and(Route::has('register.store'))->toBeFalse();

    $this->get('/register')->assertNotFound();
    $this->post('/register', [
        'name' => 'Usuario no invitado',
        'email' => 'uninvited@example.test',
        'password' => 'password',
    ])->assertNotFound();
});
