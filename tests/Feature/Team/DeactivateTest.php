<?php

use App\Models\Deal;
use App\Models\Lead;
use App\Models\Tenant;
use App\Models\User;
use Livewire\Livewire;

beforeEach(fn () => $this->seed(\Database\Seeders\DatabaseSeeder::class));

it('business owner can deactivate a salesperson', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    $salesperson = User::factory()->salesperson()->for($tenant)->create();

    Livewire::actingAs($owner)
        ->test('pages::team.index')
        ->call('deactivate', $salesperson->id)
        ->assertHasNoErrors();

    $salesperson->refresh();
    expect($salesperson->userStatus->name)->toBe('Inactive');
});

it('deactivated user cannot login', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    $salesperson = User::factory()->salesperson()->for($tenant)->create(['password' => 'password']);

    Livewire::actingAs($owner)
        ->test('pages::team.index')
        ->call('deactivate', $salesperson->id);

    Livewire::test('pages::auth.login')
        ->set('email', $salesperson->email)
        ->set('password', 'password')
        ->call('login')
        ->assertHasErrors('email');
});

it('business owner cannot deactivate self', function () {
    $owner = User::factory()->businessOwner()->create();

    Livewire::actingAs($owner)
        ->test('pages::team.index')
        ->call('deactivate', $owner->id)
        ->assertForbidden();
});

it('salesperson cannot deactivate anyone', function () {
    $salesperson = User::factory()->salesperson()->create();

    Livewire::actingAs($salesperson)
        ->test('pages::team.index')
        ->assertForbidden();
});

it('leads and deals remain after deactivation', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    $salesperson = User::factory()->salesperson()->for($tenant)->create();

    $lead = Lead::factory()->create([
        'tenant_id' => $tenant->id,
        'user_id' => $salesperson->id,
    ]);

    $deal = Deal::factory()->create([
        'tenant_id' => $tenant->id,
        'user_id' => $salesperson->id,
        'lead_id' => $lead->id,
    ]);

    Livewire::actingAs($owner)
        ->test('pages::team.index')
        ->call('deactivate', $salesperson->id);

    expect(Lead::withoutGlobalScopes()->find($lead->id))->not->toBeNull();
    expect(Deal::withoutGlobalScopes()->find($deal->id))->not->toBeNull();
});

it('shows deactivate button only for active non-self members', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create(['name' => 'Owner']);
    User::factory()->salesperson()->active()->for($tenant)->create(['name' => 'Vendedor Ativo']);
    User::factory()->salesperson()->inactive()->for($tenant)->create(['name' => 'Vendedor Inativo']);

    Livewire::actingAs($owner)
        ->test('pages::team.index')
        ->assertSee('Vendedor Ativo')
        ->assertSee('Vendedor Inativo')
        ->assertSee('Desativar');
});
