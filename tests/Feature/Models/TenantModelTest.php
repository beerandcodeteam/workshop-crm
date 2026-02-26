<?php

use App\Models\Invitation;
use App\Models\Lead;
use App\Models\Tenant;
use App\Models\User;
use App\Models\WhatsappConnection;

beforeEach(fn () => $this->seed(\Database\Seeders\DatabaseSeeder::class));

it('can create a tenant', function () {
    $tenant = Tenant::factory()->create(['name' => 'Empresa Teste']);

    expect($tenant)->toBeInstanceOf(Tenant::class)
        ->and($tenant->name)->toBe('Empresa Teste');
});

it('has users relationship', function () {
    $tenant = Tenant::factory()->create();
    User::factory()->for($tenant)->create();

    expect($tenant->users)->toHaveCount(1);
});

it('has leads relationship', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->for($tenant)->create();
    Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $user->id]);

    expect($tenant->leads)->toHaveCount(1);
});

it('has deals relationship', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $user->id]);
    \App\Models\Deal::factory()->create([
        'tenant_id' => $tenant->id,
        'user_id' => $user->id,
        'lead_id' => $lead->id,
    ]);

    expect($tenant->deals)->toHaveCount(1);
});

it('has invitations relationship', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->for($tenant)->create();
    Invitation::factory()->create(['tenant_id' => $tenant->id, 'invited_by_user_id' => $user->id]);

    expect($tenant->invitations)->toHaveCount(1);
});

it('has whatsappConnection relationship', function () {
    $tenant = Tenant::factory()->create();
    WhatsappConnection::factory()->create(['tenant_id' => $tenant->id]);

    expect($tenant->whatsappConnection)->toBeInstanceOf(WhatsappConnection::class);
});
