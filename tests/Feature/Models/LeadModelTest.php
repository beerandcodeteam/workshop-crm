<?php

use App\Models\Deal;
use App\Models\Lead;
use App\Models\Tenant;
use App\Models\User;

beforeEach(fn () => $this->seed(\Database\Seeders\DatabaseSeeder::class));

it('belongs to tenant', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $user->id]);

    expect($lead->tenant)->toBeInstanceOf(Tenant::class);
});

it('belongs to owner (user)', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $user->id]);

    expect($lead->owner)->toBeInstanceOf(User::class)
        ->and($lead->owner->id)->toBe($user->id);
});

it('has deals relationship', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $user->id]);
    Deal::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $user->id, 'lead_id' => $lead->id]);

    expect($lead->deals)->toHaveCount(1);
});

it('unique email per tenant enforced', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->for($tenant)->create();

    Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $user->id, 'email' => 'duplicate@test.com']);

    expect(fn () => Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $user->id, 'email' => 'duplicate@test.com']))
        ->toThrow(\Illuminate\Database\QueryException::class);
});

it('same email in different tenants is allowed', function () {
    $tenant1 = Tenant::factory()->create();
    $user1 = User::factory()->for($tenant1)->create();

    $tenant2 = Tenant::factory()->create();
    $user2 = User::factory()->for($tenant2)->create();

    $lead1 = Lead::factory()->create(['tenant_id' => $tenant1->id, 'user_id' => $user1->id, 'email' => 'same@test.com']);
    $lead2 = Lead::factory()->create(['tenant_id' => $tenant2->id, 'user_id' => $user2->id, 'email' => 'same@test.com']);

    expect($lead1)->toBeInstanceOf(Lead::class)
        ->and($lead2)->toBeInstanceOf(Lead::class);
});
