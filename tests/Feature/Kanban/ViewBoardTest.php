<?php

use App\Models\Deal;
use App\Models\Lead;
use App\Models\PipelineStage;
use App\Models\Tenant;
use App\Models\User;
use Livewire\Livewire;

beforeEach(fn () => $this->seed(\Database\Seeders\DatabaseSeeder::class));

it('business owner can view kanban board with all tenant deals', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    $salesperson = User::factory()->salesperson()->for($tenant)->create();

    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $salesperson->id]);
    $deal1 = Deal::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $salesperson->id, 'lead_id' => $lead->id, 'title' => 'Negócio do vendedor']);
    $deal2 = Deal::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $owner->id, 'lead_id' => $lead->id, 'title' => 'Negócio do dono']);

    Livewire::actingAs($owner)
        ->test('pages::kanban.index')
        ->assertSee('Negócio do vendedor')
        ->assertSee('Negócio do dono');
});

it('salesperson can only see their assigned deals', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    $salesperson = User::factory()->salesperson()->for($tenant)->create();

    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $salesperson->id]);
    Deal::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $salesperson->id, 'lead_id' => $lead->id, 'title' => 'Meu negócio']);
    Deal::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $owner->id, 'lead_id' => $lead->id, 'title' => 'Negócio do dono']);

    Livewire::actingAs($salesperson)
        ->test('pages::kanban.index')
        ->assertSee('Meu negócio')
        ->assertDontSee('Negócio do dono');
});

it('salesperson cannot see deals from other users', function () {
    $tenant = Tenant::factory()->create();
    $sp1 = User::factory()->salesperson()->for($tenant)->create();
    $sp2 = User::factory()->salesperson()->for($tenant)->create();

    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $sp2->id]);
    Deal::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $sp2->id, 'lead_id' => $lead->id, 'title' => 'Negócio do outro']);

    Livewire::actingAs($sp1)
        ->test('pages::kanban.index')
        ->assertDontSee('Negócio do outro');
});

it('deals are grouped by pipeline stage columns', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $owner->id]);

    $newLeadStage = PipelineStage::where('name', 'New Lead')->first();
    $contactedStage = PipelineStage::where('name', 'Contacted')->first();

    Deal::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $owner->id, 'lead_id' => $lead->id, 'pipeline_stage_id' => $newLeadStage->id, 'title' => 'Negócio Novo']);
    Deal::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $owner->id, 'lead_id' => $lead->id, 'pipeline_stage_id' => $contactedStage->id, 'title' => 'Negócio Contactado']);

    $component = Livewire::actingAs($owner)->test('pages::kanban.index');

    $dealsByStage = $component->get('dealsByStage');
    expect($dealsByStage[$newLeadStage->id])->toHaveCount(1);
    expect($dealsByStage[$contactedStage->id])->toHaveCount(1);
});

it('deal card displays title, value, lead name, owner name', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create(['name' => 'João Owner']);
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $owner->id, 'name' => 'Maria Lead']);
    Deal::factory()->create([
        'tenant_id' => $tenant->id,
        'user_id' => $owner->id,
        'lead_id' => $lead->id,
        'title' => 'Proposta Comercial',
        'value' => 5000.50,
    ]);

    Livewire::actingAs($owner)
        ->test('pages::kanban.index')
        ->assertSee('Proposta Comercial')
        ->assertSee('5.000,50')
        ->assertSee('Maria Lead')
        ->assertSee('João Owner');
});

it('pipeline stages are rendered in correct sort order', function () {
    $owner = User::factory()->businessOwner()->create();

    $component = Livewire::actingAs($owner)->test('pages::kanban.index');

    $stages = $component->get('stages');
    $stageNames = $stages->pluck('name')->toArray();

    expect($stageNames)->toBe(['New Lead', 'Contacted', 'Qualified', 'Proposal Sent', 'Negotiation', 'Won', 'Lost']);
});

it('empty board renders all columns with no cards', function () {
    $owner = User::factory()->businessOwner()->create();

    Livewire::actingAs($owner)
        ->test('pages::kanban.index')
        ->assertSee('New Lead')
        ->assertSee('Contacted')
        ->assertSee('Qualified')
        ->assertSee('Proposal Sent')
        ->assertSee('Negotiation')
        ->assertSee('Won')
        ->assertSee('Lost');
});

it('kanban page requires authentication', function () {
    $this->get('/kanban')
        ->assertRedirect('/login');
});
