<?php

use App\Models\Deal;
use App\Models\Lead;
use App\Models\Tenant;
use App\Models\User;

beforeEach(fn () => $this->seed(\Database\Seeders\DatabaseSeeder::class));

it('business owner can view any deal in their tenant', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    $salesperson = User::factory()->salesperson()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $salesperson->id]);
    $deal = Deal::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $salesperson->id, 'lead_id' => $lead->id]);

    expect($owner->can('view', $deal))->toBeTrue();
});

it('salesperson can view their own deal', function () {
    $tenant = Tenant::factory()->create();
    $salesperson = User::factory()->salesperson()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $salesperson->id]);
    $deal = Deal::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $salesperson->id, 'lead_id' => $lead->id]);

    expect($salesperson->can('view', $deal))->toBeTrue();
});

it('salesperson cannot view another users deal', function () {
    $tenant = Tenant::factory()->create();
    $salesperson = User::factory()->salesperson()->for($tenant)->create();
    $otherSalesperson = User::factory()->salesperson()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $otherSalesperson->id]);
    $deal = Deal::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $otherSalesperson->id, 'lead_id' => $lead->id]);

    expect($salesperson->can('view', $deal))->toBeFalse();
});

it('business owner can update any deal', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    $salesperson = User::factory()->salesperson()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $salesperson->id]);
    $deal = Deal::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $salesperson->id, 'lead_id' => $lead->id]);

    expect($owner->can('update', $deal))->toBeTrue();
});

it('salesperson can update their own deal', function () {
    $tenant = Tenant::factory()->create();
    $salesperson = User::factory()->salesperson()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $salesperson->id]);
    $deal = Deal::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $salesperson->id, 'lead_id' => $lead->id]);

    expect($salesperson->can('update', $deal))->toBeTrue();
});

it('salesperson cannot update another users deal', function () {
    $tenant = Tenant::factory()->create();
    $salesperson = User::factory()->salesperson()->for($tenant)->create();
    $otherSalesperson = User::factory()->salesperson()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $otherSalesperson->id]);
    $deal = Deal::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $otherSalesperson->id, 'lead_id' => $lead->id]);

    expect($salesperson->can('update', $deal))->toBeFalse();
});

it('business owner can move any deal', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    $salesperson = User::factory()->salesperson()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $salesperson->id]);
    $deal = Deal::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $salesperson->id, 'lead_id' => $lead->id]);

    expect($owner->can('move', $deal))->toBeTrue();
});

it('salesperson can move their own deal', function () {
    $tenant = Tenant::factory()->create();
    $salesperson = User::factory()->salesperson()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $salesperson->id]);
    $deal = Deal::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $salesperson->id, 'lead_id' => $lead->id]);

    expect($salesperson->can('move', $deal))->toBeTrue();
});

it('salesperson cannot move another users deal', function () {
    $tenant = Tenant::factory()->create();
    $salesperson = User::factory()->salesperson()->for($tenant)->create();
    $otherSalesperson = User::factory()->salesperson()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $otherSalesperson->id]);
    $deal = Deal::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $otherSalesperson->id, 'lead_id' => $lead->id]);

    expect($salesperson->can('move', $deal))->toBeFalse();
});

it('both roles can create deals', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    $salesperson = User::factory()->salesperson()->for($tenant)->create();

    expect($owner->can('create', Deal::class))->toBeTrue()
        ->and($salesperson->can('create', Deal::class))->toBeTrue();
});

it('only business owner can assign deals', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    $salesperson = User::factory()->salesperson()->for($tenant)->create();

    expect($owner->can('assign', Deal::class))->toBeTrue()
        ->and($salesperson->can('assign', Deal::class))->toBeFalse();
});
