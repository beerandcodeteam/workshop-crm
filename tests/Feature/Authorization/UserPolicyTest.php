<?php

use App\Models\Tenant;
use App\Models\User;

beforeEach(fn () => $this->seed(\Database\Seeders\DatabaseSeeder::class));

it('business owner can view team members', function () {
    $owner = User::factory()->businessOwner()->create();

    expect($owner->can('viewAny', User::class))->toBeTrue();
});

it('salesperson cannot view team members', function () {
    $salesperson = User::factory()->salesperson()->create();

    expect($salesperson->can('viewAny', User::class))->toBeFalse();
});

it('business owner can deactivate a team member', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    $salesperson = User::factory()->salesperson()->for($tenant)->create();

    expect($owner->can('deactivate', $salesperson))->toBeTrue();
});

it('business owner cannot deactivate themselves', function () {
    $owner = User::factory()->businessOwner()->create();

    expect($owner->can('deactivate', $owner))->toBeFalse();
});

it('salesperson cannot deactivate anyone', function () {
    $tenant = Tenant::factory()->create();
    $salesperson = User::factory()->salesperson()->for($tenant)->create();
    $otherSalesperson = User::factory()->salesperson()->for($tenant)->create();

    expect($salesperson->can('deactivate', $otherSalesperson))->toBeFalse();
});
