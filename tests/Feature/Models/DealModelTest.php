<?php

use App\Models\Deal;
use App\Models\DealNote;
use App\Models\Lead;
use App\Models\PipelineStage;
use App\Models\Tenant;
use App\Models\User;

beforeEach(fn () => $this->seed(\Database\Seeders\DatabaseSeeder::class));

it('belongs to tenant', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $user->id]);
    $deal = Deal::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $user->id, 'lead_id' => $lead->id]);

    expect($deal->tenant)->toBeInstanceOf(Tenant::class);
});

it('belongs to lead', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $user->id]);
    $deal = Deal::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $user->id, 'lead_id' => $lead->id]);

    expect($deal->lead)->toBeInstanceOf(Lead::class);
});

it('belongs to owner (user)', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $user->id]);
    $deal = Deal::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $user->id, 'lead_id' => $lead->id]);

    expect($deal->owner)->toBeInstanceOf(User::class);
});

it('belongs to pipelineStage', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $user->id]);
    $deal = Deal::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $user->id, 'lead_id' => $lead->id]);

    expect($deal->pipelineStage)->toBeInstanceOf(PipelineStage::class);
});

it('has notes relationship', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $user->id]);
    $deal = Deal::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $user->id, 'lead_id' => $lead->id]);
    DealNote::factory()->create(['tenant_id' => $tenant->id, 'deal_id' => $deal->id, 'user_id' => $user->id]);

    expect($deal->notes)->toHaveCount(1);
});

it('value is cast to decimal', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $user->id]);
    $deal = Deal::factory()->create([
        'tenant_id' => $tenant->id,
        'user_id' => $user->id,
        'lead_id' => $lead->id,
        'value' => 1234.50,
    ]);

    expect($deal->value)->toBe('1234.50');
});
