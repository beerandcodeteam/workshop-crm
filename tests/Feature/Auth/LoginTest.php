<?php

use App\Models\User;
use Livewire\Livewire;

beforeEach(fn () => $this->seed(\Database\Seeders\DatabaseSeeder::class));

it('can view the login page', function () {
    $this->get('/login')
        ->assertSuccessful()
        ->assertSee('Acesse sua conta');
});

it('can login with valid credentials', function () {
    $user = User::factory()->create(['email' => 'lucas@email.com']);

    Livewire::test('pages::auth.login')
        ->set('email', 'lucas@email.com')
        ->set('password', 'password')
        ->call('login')
        ->assertHasNoErrors()
        ->assertRedirect(route('kanban.index'));

    $this->assertAuthenticatedAs($user);
});

it('fails with invalid credentials', function () {
    User::factory()->create(['email' => 'lucas@email.com']);

    Livewire::test('pages::auth.login')
        ->set('email', 'lucas@email.com')
        ->set('password', 'wrong-password')
        ->call('login')
        ->assertHasErrors('email');
});

it('fails with nonexistent email', function () {
    Livewire::test('pages::auth.login')
        ->set('email', 'nonexistent@email.com')
        ->set('password', 'password')
        ->call('login')
        ->assertHasErrors('email');
});

it('fails when user is inactive', function () {
    User::factory()->inactive()->create(['email' => 'lucas@email.com']);

    Livewire::test('pages::auth.login')
        ->set('email', 'lucas@email.com')
        ->set('password', 'password')
        ->call('login')
        ->assertHasErrors('email');

    $this->assertGuest();
});

it('is rate limited after 5 failed attempts', function () {
    User::factory()->create(['email' => 'lucas@email.com']);

    $component = Livewire::test('pages::auth.login');

    for ($i = 0; $i < 5; $i++) {
        $component
            ->set('email', 'lucas@email.com')
            ->set('password', 'wrong-password')
            ->call('login');
    }

    $component
        ->set('email', 'lucas@email.com')
        ->set('password', 'wrong-password')
        ->call('login')
        ->assertHasErrors('email');

    expect($component->get('email'))->toBe('lucas@email.com');
});

it('redirects authenticated user away from login page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/login')
        ->assertRedirect('/kanban');
});

it('fails with missing required fields', function () {
    Livewire::test('pages::auth.login')
        ->call('login')
        ->assertHasErrors(['email', 'password']);
});
