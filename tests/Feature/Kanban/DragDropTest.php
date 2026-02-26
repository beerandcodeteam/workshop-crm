<?php

use App\Models\Deal;
use App\Models\Lead;
use App\Models\PipelineStage;
use App\Models\Tenant;
use App\Models\User;
use Livewire\Livewire;

beforeEach(fn () => $this->seed(\Database\Seeders\DatabaseSeeder::class));

it('calling handleSort updates deal pipeline_stage_id and sort_order', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $owner->id]);

    $newLeadStage = PipelineStage::where('name', 'New Lead')->first();
    $contactedStage = PipelineStage::where('name', 'Contacted')->first();

    $deal = Deal::factory()->create([
        'tenant_id' => $tenant->id,
        'user_id' => $owner->id,
        'lead_id' => $lead->id,
        'pipeline_stage_id' => $newLeadStage->id,
        'sort_order' => 0,
    ]);

    Livewire::actingAs($owner)
        ->test('pages::kanban.index')
        ->call('handleSort', $contactedStage->id, $deal->id, 0)
        ->assertHasNoErrors();

    $deal->refresh();
    expect($deal->pipeline_stage_id)->toBe($contactedStage->id);
    expect($deal->sort_order)->toBe(0);
});

it('moving deal to Lost stage requires loss_reason', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $owner->id]);

    $newLeadStage = PipelineStage::where('name', 'New Lead')->first();
    $lostStage = PipelineStage::where('name', 'Lost')->first();

    $deal = Deal::factory()->create([
        'tenant_id' => $tenant->id,
        'user_id' => $owner->id,
        'lead_id' => $lead->id,
        'pipeline_stage_id' => $newLeadStage->id,
    ]);

    Livewire::actingAs($owner)
        ->test('pages::kanban.index')
        ->call('handleSort', $lostStage->id, $deal->id, 0)
        ->assertSet('showLossReasonModal', true)
        ->assertSet('pendingDealId', $deal->id);

    $deal->refresh();
    expect($deal->pipeline_stage_id)->toBe($newLeadStage->id);
});

it('can confirm loss reason and move to Lost stage', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $owner->id]);

    $newLeadStage = PipelineStage::where('name', 'New Lead')->first();
    $lostStage = PipelineStage::where('name', 'Lost')->first();

    $deal = Deal::factory()->create([
        'tenant_id' => $tenant->id,
        'user_id' => $owner->id,
        'lead_id' => $lead->id,
        'pipeline_stage_id' => $newLeadStage->id,
    ]);

    Livewire::actingAs($owner)
        ->test('pages::kanban.index')
        ->call('handleSort', $lostStage->id, $deal->id, 0)
        ->set('lossReason', 'Cliente optou pela concorrência')
        ->call('confirmLossReason')
        ->assertHasNoErrors();

    $deal->refresh();
    expect($deal->pipeline_stage_id)->toBe($lostStage->id);
    expect($deal->loss_reason)->toBe('Cliente optou pela concorrência');
});

it('loss reason validation fails when empty', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $owner->id]);

    $newLeadStage = PipelineStage::where('name', 'New Lead')->first();
    $lostStage = PipelineStage::where('name', 'Lost')->first();

    $deal = Deal::factory()->create([
        'tenant_id' => $tenant->id,
        'user_id' => $owner->id,
        'lead_id' => $lead->id,
        'pipeline_stage_id' => $newLeadStage->id,
    ]);

    Livewire::actingAs($owner)
        ->test('pages::kanban.index')
        ->call('handleSort', $lostStage->id, $deal->id, 0)
        ->set('lossReason', '')
        ->call('confirmLossReason')
        ->assertHasErrors('lossReason');
});

it('salesperson can move their own deals', function () {
    $tenant = Tenant::factory()->create();
    User::factory()->businessOwner()->for($tenant)->create();
    $salesperson = User::factory()->salesperson()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $salesperson->id]);

    $newLeadStage = PipelineStage::where('name', 'New Lead')->first();
    $contactedStage = PipelineStage::where('name', 'Contacted')->first();

    $deal = Deal::factory()->create([
        'tenant_id' => $tenant->id,
        'user_id' => $salesperson->id,
        'lead_id' => $lead->id,
        'pipeline_stage_id' => $newLeadStage->id,
    ]);

    Livewire::actingAs($salesperson)
        ->test('pages::kanban.index')
        ->call('handleSort', $contactedStage->id, $deal->id, 0)
        ->assertHasNoErrors();

    $deal->refresh();
    expect($deal->pipeline_stage_id)->toBe($contactedStage->id);
});

it('salesperson cannot move another user deals', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    $salesperson = User::factory()->salesperson()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $owner->id]);

    $newLeadStage = PipelineStage::where('name', 'New Lead')->first();
    $contactedStage = PipelineStage::where('name', 'Contacted')->first();

    $deal = Deal::factory()->create([
        'tenant_id' => $tenant->id,
        'user_id' => $owner->id,
        'lead_id' => $lead->id,
        'pipeline_stage_id' => $newLeadStage->id,
    ]);

    Livewire::actingAs($salesperson)
        ->test('pages::kanban.index')
        ->call('handleSort', $contactedStage->id, $deal->id, 0)
        ->assertForbidden();
});

it('business owner can move any deal', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    $salesperson = User::factory()->salesperson()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $salesperson->id]);

    $newLeadStage = PipelineStage::where('name', 'New Lead')->first();
    $contactedStage = PipelineStage::where('name', 'Contacted')->first();

    $deal = Deal::factory()->create([
        'tenant_id' => $tenant->id,
        'user_id' => $salesperson->id,
        'lead_id' => $lead->id,
        'pipeline_stage_id' => $newLeadStage->id,
    ]);

    Livewire::actingAs($owner)
        ->test('pages::kanban.index')
        ->call('handleSort', $contactedStage->id, $deal->id, 0)
        ->assertHasNoErrors();

    $deal->refresh();
    expect($deal->pipeline_stage_id)->toBe($contactedStage->id);
});

it('sort order is recalculated correctly within a column', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $owner->id]);

    $contactedStage = PipelineStage::where('name', 'Contacted')->first();

    $deal1 = Deal::factory()->create([
        'tenant_id' => $tenant->id,
        'user_id' => $owner->id,
        'lead_id' => $lead->id,
        'pipeline_stage_id' => $contactedStage->id,
        'sort_order' => 0,
    ]);

    $deal2 = Deal::factory()->create([
        'tenant_id' => $tenant->id,
        'user_id' => $owner->id,
        'lead_id' => $lead->id,
        'pipeline_stage_id' => $contactedStage->id,
        'sort_order' => 1,
    ]);

    $newLeadStage = PipelineStage::where('name', 'New Lead')->first();
    $deal3 = Deal::factory()->create([
        'tenant_id' => $tenant->id,
        'user_id' => $owner->id,
        'lead_id' => $lead->id,
        'pipeline_stage_id' => $newLeadStage->id,
        'sort_order' => 0,
    ]);

    Livewire::actingAs($owner)
        ->test('pages::kanban.index')
        ->call('handleSort', $contactedStage->id, $deal3->id, 1);

    $deal1->refresh();
    $deal2->refresh();
    $deal3->refresh();

    expect($deal3->pipeline_stage_id)->toBe($contactedStage->id);
    expect($deal3->sort_order)->toBe(1);
    expect($deal1->sort_order)->toBe(0);
    expect($deal2->sort_order)->toBe(2);
});
