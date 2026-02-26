<?php

use App\Models\Deal;
use App\Models\DealNote;
use App\Models\Lead;
use App\Models\Tenant;
use App\Models\User;

beforeEach(fn () => $this->seed(\Database\Seeders\DatabaseSeeder::class));

it('business owner can create note on any deal', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    $salesperson = User::factory()->salesperson()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $salesperson->id]);
    $deal = Deal::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $salesperson->id, 'lead_id' => $lead->id]);

    expect($owner->can('create', [DealNote::class, $deal]))->toBeTrue();
});

it('salesperson can create note on their own deal', function () {
    $tenant = Tenant::factory()->create();
    $salesperson = User::factory()->salesperson()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $salesperson->id]);
    $deal = Deal::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $salesperson->id, 'lead_id' => $lead->id]);

    expect($salesperson->can('create', [DealNote::class, $deal]))->toBeTrue();
});

it('salesperson cannot create note on another users deal', function () {
    $tenant = Tenant::factory()->create();
    $salesperson = User::factory()->salesperson()->for($tenant)->create();
    $otherSalesperson = User::factory()->salesperson()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $otherSalesperson->id]);
    $deal = Deal::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $otherSalesperson->id, 'lead_id' => $lead->id]);

    expect($salesperson->can('create', [DealNote::class, $deal]))->toBeFalse();
});

it('business owner can view any deal note', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    $salesperson = User::factory()->salesperson()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $salesperson->id]);
    $deal = Deal::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $salesperson->id, 'lead_id' => $lead->id]);
    $note = DealNote::factory()->create(['tenant_id' => $tenant->id, 'deal_id' => $deal->id, 'user_id' => $salesperson->id]);

    expect($owner->can('view', $note))->toBeTrue();
});

it('salesperson can view notes on their own deal', function () {
    $tenant = Tenant::factory()->create();
    $salesperson = User::factory()->salesperson()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $salesperson->id]);
    $deal = Deal::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $salesperson->id, 'lead_id' => $lead->id]);
    $note = DealNote::factory()->create(['tenant_id' => $tenant->id, 'deal_id' => $deal->id, 'user_id' => $salesperson->id]);

    expect($salesperson->can('view', $note))->toBeTrue();
});

it('salesperson cannot view notes on another users deal', function () {
    $tenant = Tenant::factory()->create();
    $salesperson = User::factory()->salesperson()->for($tenant)->create();
    $otherSalesperson = User::factory()->salesperson()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $otherSalesperson->id]);
    $deal = Deal::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $otherSalesperson->id, 'lead_id' => $lead->id]);
    $note = DealNote::factory()->create(['tenant_id' => $tenant->id, 'deal_id' => $deal->id, 'user_id' => $otherSalesperson->id]);

    expect($salesperson->can('view', $note))->toBeFalse();
});
