<?php

use App\Models\Invitation;
use App\Models\Tenant;
use App\Models\User;

beforeEach(fn () => $this->seed(\Database\Seeders\DatabaseSeeder::class));

it('business owner can view invitations', function () {
    $owner = User::factory()->businessOwner()->create();

    expect($owner->can('viewAny', Invitation::class))->toBeTrue();
});

it('salesperson cannot view invitations', function () {
    $salesperson = User::factory()->salesperson()->create();

    expect($salesperson->can('viewAny', Invitation::class))->toBeFalse();
});

it('business owner can create invitations', function () {
    $owner = User::factory()->businessOwner()->create();

    expect($owner->can('create', Invitation::class))->toBeTrue();
});

it('salesperson cannot create invitations', function () {
    $salesperson = User::factory()->salesperson()->create();

    expect($salesperson->can('create', Invitation::class))->toBeFalse();
});

it('business owner can revoke invitations', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    $invitation = Invitation::factory()->create(['tenant_id' => $tenant->id, 'invited_by_user_id' => $owner->id]);

    expect($owner->can('revoke', $invitation))->toBeTrue();
});

it('salesperson cannot revoke invitations', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    $salesperson = User::factory()->salesperson()->for($tenant)->create();
    $invitation = Invitation::factory()->create(['tenant_id' => $tenant->id, 'invited_by_user_id' => $owner->id]);

    expect($salesperson->can('revoke', $invitation))->toBeFalse();
});
