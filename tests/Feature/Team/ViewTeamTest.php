<?php

use App\Models\Tenant;
use App\Models\User;
use Livewire\Livewire;

beforeEach(fn () => $this->seed(\Database\Seeders\DatabaseSeeder::class));

it('business owner can view team page', function () {
    $owner = User::factory()->businessOwner()->create();

    $this->actingAs($owner)
        ->get('/team')
        ->assertSuccessful()
        ->assertSee('Membros da equipe');
});

it('team list shows all tenant users', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create(['name' => 'João Dono']);
    $salesperson = User::factory()->salesperson()->for($tenant)->create(['name' => 'Maria Vendedora']);

    Livewire::actingAs($owner)
        ->test('pages::team.index')
        ->assertSee('João Dono')
        ->assertSee('Maria Vendedora');
});

it('team list does not show other tenant users', function () {
    $tenant1 = Tenant::factory()->create();
    $tenant2 = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant1)->create();
    User::factory()->salesperson()->for($tenant2)->create(['name' => 'Outro Vendedor']);

    Livewire::actingAs($owner)
        ->test('pages::team.index')
        ->assertDontSee('Outro Vendedor');
});

it('salesperson cannot access team page', function () {
    $salesperson = User::factory()->salesperson()->create();

    $this->actingAs($salesperson)
        ->get('/team')
        ->assertForbidden();
});
