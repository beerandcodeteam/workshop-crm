<?php

use App\Models\User;

beforeEach(fn () => $this->seed(\Database\Seeders\DatabaseSeeder::class));

it('can logout', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/logout')
        ->assertRedirect(route('login'));

    $this->assertGuest();
});

it('redirects to login after logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $response->assertRedirect(route('login'));
});

it('guest cannot access logout', function () {
    $this->post('/logout')
        ->assertRedirect('/login');
});
