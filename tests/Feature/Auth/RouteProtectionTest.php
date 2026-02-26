<?php

use App\Models\User;

beforeEach(fn () => $this->seed(\Database\Seeders\DatabaseSeeder::class));

it('unauthenticated user is redirected to login from protected routes', function (string $route) {
    $this->get($route)->assertRedirect('/login');
})->with([
    '/dashboard',
    '/kanban',
    '/team',
    '/settings',
]);

it('authenticated user is redirected from guest routes', function (string $route) {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get($route)
        ->assertRedirect('/kanban');
})->with([
    '/login',
    '/register',
    '/forgot-password',
]);

it('inactive user is logged out when accessing protected routes', function () {
    $user = User::factory()->inactive()->create();

    $this->actingAs($user)
        ->get('/kanban')
        ->assertRedirect(route('login'));

    $this->assertGuest();
});

it('inactive user sees error message after being logged out', function () {
    $user = User::factory()->inactive()->create();

    $this->actingAs($user)
        ->get('/kanban')
        ->assertRedirect(route('login'));

    $this->get('/login')
        ->assertSee('Sua conta está inativa');
});

it('active user can access protected routes', function () {
    $user = User::factory()->active()->create();

    $this->actingAs($user)
        ->get('/kanban')
        ->assertSuccessful();
});
