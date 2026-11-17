<?php

test('the login page is available', function () {
    $this->get(route('login'))->assertOk();
});
