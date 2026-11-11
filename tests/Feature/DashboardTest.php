<?php

// NOTE: this app currently has no authentication, so the dashboard is public.
// This test must change (guests redirected) when real auth is added.
test('dashboard is reachable', function () {
    $this->get(route('dashboard'))->assertStatus(200);
});
