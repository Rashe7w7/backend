<?php

use Illuminate\Support\Facades\Route;

test('profile settings are not exposed outside administrator account management', function () {
    expect(Route::has('profile.edit'))->toBeFalse();

    $this->get('/settings/profile')->assertNotFound();
});
