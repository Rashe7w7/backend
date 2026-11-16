<?php

use Illuminate\Support\Facades\Route;

test('self service password updates are not exposed', function () {
    expect(Route::has('user-password.update'))->toBeFalse();

    $this->put('/user/password', [
        'current_password' => 'password',
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ])->assertNotFound();
});
