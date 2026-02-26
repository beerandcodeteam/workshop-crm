<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;

beforeEach(fn () => $this->seed(\Database\Seeders\DatabaseSeeder::class));

it('can view the forgot password page', function () {
    $this->get('/forgot-password')
        ->assertSuccessful()
        ->assertSee('Esqueceu sua senha?');
});

it('can request a password reset link', function () {
    Notification::fake();

    $user = User::factory()->create(['email' => 'lucas@email.com']);

    Livewire::test('pages::auth.forgot-password')
        ->set('email', 'lucas@email.com')
        ->call('sendResetLink')
        ->assertHasNoErrors()
        ->assertSee('Enviamos um link de redefinição de senha para o seu e-mail.');

    Notification::assertSentTo($user, ResetPassword::class);
});

it('shows error for nonexistent email on forgot password', function () {
    Notification::fake();

    Livewire::test('pages::auth.forgot-password')
        ->set('email', 'nonexistent@email.com')
        ->call('sendResetLink')
        ->assertHasErrors('email');

    Notification::assertNothingSent();
});

it('can view the reset password page', function () {
    $this->get('/reset-password/test-token?email=lucas@email.com')
        ->assertSuccessful()
        ->assertSee('Redefinir senha');
});

it('can reset password with valid token', function () {
    $user = User::factory()->create(['email' => 'lucas@email.com']);

    $token = Password::createToken($user);

    Livewire::test('pages::auth.reset-password', ['token' => $token])
        ->set('email', 'lucas@email.com')
        ->set('password', 'new-password123')
        ->set('password_confirmation', 'new-password123')
        ->call('resetPassword')
        ->assertHasNoErrors()
        ->assertRedirect(route('login'));

    expect(auth()->attempt(['email' => 'lucas@email.com', 'password' => 'new-password123']))->toBeTrue();
});

it('fails reset with invalid token', function () {
    User::factory()->create(['email' => 'lucas@email.com']);

    Livewire::test('pages::auth.reset-password', ['token' => 'invalid-token'])
        ->set('email', 'lucas@email.com')
        ->set('password', 'new-password123')
        ->set('password_confirmation', 'new-password123')
        ->call('resetPassword')
        ->assertHasErrors('email');
});

it('fails reset with short password', function () {
    $user = User::factory()->create(['email' => 'lucas@email.com']);
    $token = Password::createToken($user);

    Livewire::test('pages::auth.reset-password', ['token' => $token])
        ->set('email', 'lucas@email.com')
        ->set('password', '123')
        ->set('password_confirmation', '123')
        ->call('resetPassword')
        ->assertHasErrors('password');
});

it('redirects to login with success message after reset', function () {
    $user = User::factory()->create(['email' => 'lucas@email.com']);
    $token = Password::createToken($user);

    Livewire::test('pages::auth.reset-password', ['token' => $token])
        ->set('email', 'lucas@email.com')
        ->set('password', 'new-password123')
        ->set('password_confirmation', 'new-password123')
        ->call('resetPassword')
        ->assertRedirect(route('login'));

    $this->get('/login')
        ->assertSee('Sua senha foi redefinida com sucesso.');
});
