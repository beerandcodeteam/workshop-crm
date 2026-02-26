<?php

use App\Models\Deal;
use App\Models\Lead;
use App\Models\PipelineStage;
use App\Models\Tenant;
use App\Models\User;
use Livewire\Livewire;

beforeEach(fn () => $this->seed(\Database\Seeders\DatabaseSeeder::class));

it('can create a new deal for an existing lead', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $owner->id, 'email' => 'existente@lead.com']);

    Livewire::actingAs($owner)
        ->test('pages::kanban.index')
        ->set('form.email', 'existente@lead.com')
        ->call('searchLead')
        ->assertSet('leadFound', true)
        ->set('form.deal_title', 'Segundo Negócio')
        ->set('form.deal_value', '3000.00')
        ->call('createLead')
        ->assertHasNoErrors();

    expect(Deal::where('title', 'Segundo Negócio')->where('lead_id', $lead->id)->exists())->toBeTrue();
});

it('existing lead can have multiple deals', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    $lead = Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $owner->id, 'email' => 'multi@lead.com']);
    Deal::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $owner->id, 'lead_id' => $lead->id, 'title' => 'Primeiro']);

    Livewire::actingAs($owner)
        ->test('pages::kanban.index')
        ->set('form.email', 'multi@lead.com')
        ->call('searchLead')
        ->set('form.deal_title', 'Segundo')
        ->set('form.deal_value', '2000.00')
        ->call('createLead')
        ->assertHasNoErrors();

    expect(Deal::where('lead_id', $lead->id)->count())->toBe(2);
});

it('new deal for existing lead is in New Lead stage', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $owner->id, 'email' => 'lead@email.com']);

    Livewire::actingAs($owner)
        ->test('pages::kanban.index')
        ->set('form.email', 'lead@email.com')
        ->call('searchLead')
        ->set('form.deal_title', 'Novo Negócio')
        ->set('form.deal_value', '1000.00')
        ->call('createLead');

    $deal = Deal::where('title', 'Novo Negócio')->first();
    $newLeadStage = PipelineStage::where('name', 'New Lead')->first();

    expect($deal->pipeline_stage_id)->toBe($newLeadStage->id);
});

it('new deal for existing lead is assigned to current user', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    $salesperson = User::factory()->salesperson()->for($tenant)->create();
    Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $owner->id, 'email' => 'lead@email.com']);

    Livewire::actingAs($salesperson)
        ->test('pages::kanban.index')
        ->set('form.email', 'lead@email.com')
        ->call('searchLead')
        ->set('form.deal_title', 'Deal SP')
        ->set('form.deal_value', '800.00')
        ->call('createLead');

    $deal = Deal::where('title', 'Deal SP')->first();
    expect($deal->user_id)->toBe($salesperson->id);
});

it('lead data is not duplicated when creating deal for existing lead', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $owner->id, 'email' => 'unico@lead.com']);

    Livewire::actingAs($owner)
        ->test('pages::kanban.index')
        ->set('form.email', 'unico@lead.com')
        ->call('searchLead')
        ->set('form.deal_title', 'Mais um')
        ->set('form.deal_value', '500.00')
        ->call('createLead');

    expect(Lead::where('email', 'unico@lead.com')->count())->toBe(1);
});
