<?php

use App\Models\Deal;
use App\Models\Lead;
use App\Models\Tenant;
use App\Models\User;

beforeEach(fn () => $this->seed(\Database\Seeders\DatabaseSeeder::class));

it('authenticated user can only query leads from their own tenant', function () {
    $tenant1 = Tenant::factory()->create();
    $user1 = User::factory()->for($tenant1)->create();
    $lead1 = Lead::factory()->create(['tenant_id' => $tenant1->id, 'user_id' => $user1->id]);

    $tenant2 = Tenant::factory()->create();
    $user2 = User::factory()->for($tenant2)->create();
    $lead2 = Lead::factory()->create(['tenant_id' => $tenant2->id, 'user_id' => $user2->id]);

    $this->actingAs($user1);

    $leads = Lead::all();

    expect($leads)->toHaveCount(1)
        ->and($leads->first()->id)->toBe($lead1->id);
});

it('user from Tenant A cannot see Tenant B leads', function () {
    $tenantA = Tenant::factory()->create();
    $userA = User::factory()->for($tenantA)->create();

    $tenantB = Tenant::factory()->create();
    $userB = User::factory()->for($tenantB)->create();
    Lead::factory()->create(['tenant_id' => $tenantB->id, 'user_id' => $userB->id]);

    $this->actingAs($userA);

    expect(Lead::count())->toBe(0);
});

it('user from Tenant A cannot see Tenant B deals', function () {
    $tenantA = Tenant::factory()->create();
    $userA = User::factory()->for($tenantA)->create();

    $tenantB = Tenant::factory()->create();
    $userB = User::factory()->for($tenantB)->create();
    $leadB = Lead::factory()->create(['tenant_id' => $tenantB->id, 'user_id' => $userB->id]);
    Deal::factory()->create(['tenant_id' => $tenantB->id, 'user_id' => $userB->id, 'lead_id' => $leadB->id]);

    $this->actingAs($userA);

    expect(Deal::count())->toBe(0);
});

it('user from Tenant A cannot see Tenant B invitations', function () {
    $tenantA = Tenant::factory()->create();
    $userA = User::factory()->for($tenantA)->create();

    $tenantB = Tenant::factory()->create();
    $userB = User::factory()->for($tenantB)->create();
    \App\Models\Invitation::factory()->create(['tenant_id' => $tenantB->id, 'invited_by_user_id' => $userB->id]);

    $this->actingAs($userA);

    expect(\App\Models\Invitation::count())->toBe(0);
});

it('unauthenticated requests do not apply tenant scope - returns all records', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->for($tenant)->create();
    Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $user->id]);

    // When no user is authenticated, the scope does not filter (no tenant context available).
    // Data protection for unauthenticated users is handled by auth middleware on routes.
    expect(Lead::count())->toBe(1);
});
