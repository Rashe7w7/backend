<?php

test('public page returns 200', function (string $uri) {
    $this->get($uri)->assertStatus(200);
})->with([
    '/dashboard',
    '/roles',
    '/profesor',
    '/planestudio',
    '/alumnoayudante',
    '/indicadores',
    '/tablas',
]);
