<?php

use App\Models\Lead;
use App\Models\Tenant;
use App\Models\User;

beforeEach(fn () => $this->seed(\Database\Seeders\DatabaseSeeder::class));

it('business owner can view any lead in their tenant', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    $salesperson = User::factory()->salesperson()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $salesperson->id]);

    expect($owner->can('view', $lead))->toBeTrue();
});

it('salesperson can view their own lead', function () {
    $tenant = Tenant::factory()->create();
    $salesperson = User::factory()->salesperson()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $salesperson->id]);

    expect($salesperson->can('view', $lead))->toBeTrue();
});

it('salesperson cannot view another users lead', function () {
    $tenant = Tenant::factory()->create();
    $salesperson = User::factory()->salesperson()->for($tenant)->create();
    $otherSalesperson = User::factory()->salesperson()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $otherSalesperson->id]);

    expect($salesperson->can('view', $lead))->toBeFalse();
});

it('business owner can update any lead in their tenant', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    $salesperson = User::factory()->salesperson()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $salesperson->id]);

    expect($owner->can('update', $lead))->toBeTrue();
});

it('salesperson can update their own lead', function () {
    $tenant = Tenant::factory()->create();
    $salesperson = User::factory()->salesperson()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $salesperson->id]);

    expect($salesperson->can('update', $lead))->toBeTrue();
});

it('salesperson cannot update another users lead', function () {
    $tenant = Tenant::factory()->create();
    $salesperson = User::factory()->salesperson()->for($tenant)->create();
    $otherSalesperson = User::factory()->salesperson()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $otherSalesperson->id]);

    expect($salesperson->can('update', $lead))->toBeFalse();
});

it('both roles can create leads', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    $salesperson = User::factory()->salesperson()->for($tenant)->create();

    expect($owner->can('create', Lead::class))->toBeTrue()
        ->and($salesperson->can('create', Lead::class))->toBeTrue();
});

it('both roles can view any leads list', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    $salesperson = User::factory()->salesperson()->for($tenant)->create();

    expect($owner->can('viewAny', Lead::class))->toBeTrue()
        ->and($salesperson->can('viewAny', Lead::class))->toBeTrue();
});

it('only business owner can assign leads', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    $salesperson = User::factory()->salesperson()->for($tenant)->create();

    expect($owner->can('assign', Lead::class))->toBeTrue()
        ->and($salesperson->can('assign', Lead::class))->toBeFalse();
});
