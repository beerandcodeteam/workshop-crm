<?php

use App\Models\Lead;
use App\Models\Tenant;
use App\Models\User;

beforeEach(fn () => $this->seed(\Database\Seeders\DatabaseSeeder::class));

it('creating a Lead while authenticated automatically sets tenant_id', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->for($tenant)->create();

    $this->actingAs($user);

    $lead = Lead::create([
        'user_id' => $user->id,
        'name' => 'Auto Lead',
        'email' => 'auto@test.com',
    ]);

    expect($lead->tenant_id)->toBe($tenant->id);
});

it('creating a Deal while authenticated automatically sets tenant_id', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->for($tenant)->create();

    $this->actingAs($user);

    $lead = Lead::create([
        'user_id' => $user->id,
        'name' => 'Deal Lead',
        'email' => 'deal-lead@test.com',
    ]);

    $pipelineStage = \App\Models\PipelineStage::where('name', 'New Lead')->first();

    $deal = \App\Models\Deal::create([
        'lead_id' => $lead->id,
        'user_id' => $user->id,
        'pipeline_stage_id' => $pipelineStage->id,
        'title' => 'Auto Deal',
        'value' => 1000.00,
    ]);

    expect($deal->tenant_id)->toBe($tenant->id);
});

it('tenant_id is not overwritten if explicitly provided', function () {
    $tenant1 = Tenant::factory()->create();
    $tenant2 = Tenant::factory()->create();
    $user = User::factory()->for($tenant1)->create();

    $this->actingAs($user);

    $lead = Lead::create([
        'tenant_id' => $tenant2->id,
        'user_id' => $user->id,
        'name' => 'Explicit Tenant Lead',
        'email' => 'explicit@test.com',
    ]);

    expect($lead->tenant_id)->toBe($tenant2->id);
});
