<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

test('password recovery pages render for guests', function () {
    $this->get(route('password.request'))->assertOk();
    $this->get(route('password.reset', ['token' => 'example-token', 'email' => 'person@example.com']))
        ->assertOk();
});

test('a requested reset link changes the password and invalidates its token', function () {
    Notification::fake();
    $user = User::factory()->withoutTwoFactor()->create();

    $this->post(route('password.email'), ['email' => $user->email])
        ->assertSessionHasNoErrors()
        ->assertSessionHas('status');

    $token = null;
    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use (&$token) {
        $token = $notification->token;

        return true;
    });

    $this->post(route('password.update'), [
        'token' => $token,
        'email' => $user->email,
        'password' => 'Replacement-password-123!',
        'password_confirmation' => 'Replacement-password-123!',
    ])->assertSessionHasNoErrors()->assertRedirect(route('login'));

    expect(Hash::check('Replacement-password-123!', $user->fresh()->password))->toBeTrue();
    expect(Password::tokenExists($user, $token))->toBeFalse();
});

test('an invalid reset token cannot change a password', function () {
    $user = User::factory()->withoutTwoFactor()->create();
    $originalHash = $user->password;

    $this->post(route('password.update'), [
        'token' => 'invalid-token',
        'email' => $user->email,
        'password' => 'Replacement-password-123!',
        'password_confirmation' => 'Replacement-password-123!',
    ])->assertSessionHasErrors('email');

    expect($user->fresh()->password)->toBe($originalHash);
});
