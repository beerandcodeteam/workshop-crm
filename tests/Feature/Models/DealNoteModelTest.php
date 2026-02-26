<?php

use App\Models\Deal;
use App\Models\DealNote;
use App\Models\Lead;
use App\Models\Tenant;
use App\Models\User;

beforeEach(fn () => $this->seed(\Database\Seeders\DatabaseSeeder::class));

it('belongs to deal', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $user->id]);
    $deal = Deal::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $user->id, 'lead_id' => $lead->id]);
    $note = DealNote::factory()->create(['tenant_id' => $tenant->id, 'deal_id' => $deal->id, 'user_id' => $user->id]);

    expect($note->deal)->toBeInstanceOf(Deal::class);
});

it('belongs to author (user)', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $user->id]);
    $deal = Deal::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $user->id, 'lead_id' => $lead->id]);
    $note = DealNote::factory()->create(['tenant_id' => $tenant->id, 'deal_id' => $deal->id, 'user_id' => $user->id]);

    expect($note->author)->toBeInstanceOf(User::class)
        ->and($note->author->id)->toBe($user->id);
});

it('belongs to tenant', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $user->id]);
    $deal = Deal::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $user->id, 'lead_id' => $lead->id]);
    $note = DealNote::factory()->create(['tenant_id' => $tenant->id, 'deal_id' => $deal->id, 'user_id' => $user->id]);

    expect($note->tenant)->toBeInstanceOf(Tenant::class);
});
