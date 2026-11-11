<?php

test('home page returns a successful response', function () {
    $this->get('/')->assertStatus(200);
});

test('health endpoint returns a successful response', function () {
    $this->get('/up')->assertStatus(200);
});
