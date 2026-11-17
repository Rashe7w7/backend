<?php

use Illuminate\Support\Facades\Route;

test('email verification routes are disabled for administrator managed accounts', function () {
    expect(Route::has('verification.notice'))->toBeFalse()
        ->and(Route::has('verification.verify'))->toBeFalse();

    $this->get('/email/verify')->assertNotFound();
});
