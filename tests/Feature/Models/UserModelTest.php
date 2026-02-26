<?php

use App\Models\Deal;
use App\Models\Invitation;
use App\Models\Lead;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserStatus;

beforeEach(fn () => $this->seed(\Database\Seeders\DatabaseSeeder::class));

it('belongs to tenant', function () {
    $user = User::factory()->create();

    expect($user->tenant)->toBeInstanceOf(Tenant::class);
});

it('belongs to role', function () {
    $user = User::factory()->create();

    expect($user->role)->toBeInstanceOf(Role::class);
});

it('belongs to userStatus', function () {
    $user = User::factory()->create();

    expect($user->userStatus)->toBeInstanceOf(UserStatus::class);
});

it('has leads relationship', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->for($tenant)->create();
    Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $user->id]);

    expect($user->leads)->toHaveCount(1);
});

it('has deals relationship', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $user->id]);
    Deal::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $user->id, 'lead_id' => $lead->id]);

    expect($user->deals)->toHaveCount(1);
});

it('factory works with businessOwner state', function () {
    $user = User::factory()->businessOwner()->create();

    expect($user->role->name)->toBe('Business Owner');
});

it('factory works with salesperson state', function () {
    $user = User::factory()->salesperson()->create();

    expect($user->role->name)->toBe('Salesperson');
});

it('factory works with inactive state', function () {
    $user = User::factory()->inactive()->create();

    expect($user->userStatus->name)->toBe('Inactive');
});

it('has invitationsSent relationship', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->for($tenant)->create();
    Invitation::factory()->create(['tenant_id' => $tenant->id, 'invited_by_user_id' => $user->id]);

    expect($user->invitationsSent)->toHaveCount(1);
});
