<?php

use App\Models\Deal;
use App\Models\Lead;
use App\Models\PipelineStage;
use App\Models\Tenant;
use App\Models\User;
use Livewire\Livewire;

beforeEach(fn () => $this->seed(\Database\Seeders\DatabaseSeeder::class));

it('can search for existing lead by email', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $owner->id, 'email' => 'lead@email.com', 'name' => 'Lead Existente']);

    Livewire::actingAs($owner)
        ->test('pages::kanban.index')
        ->set('form.email', 'lead@email.com')
        ->call('searchLead')
        ->assertSet('leadFound', true)
        ->assertSee('Lead Existente');
});

it('creating a new lead creates both Lead and Deal records', function () {
    $owner = User::factory()->businessOwner()->create();

    Livewire::actingAs($owner)
        ->test('pages::kanban.index')
        ->set('form.email', 'novo@lead.com')
        ->call('searchLead')
        ->set('form.name', 'Novo Lead')
        ->set('form.phone', '11999999999')
        ->set('form.deal_title', 'Primeiro Negócio')
        ->set('form.deal_value', '1500.00')
        ->call('createLead')
        ->assertHasNoErrors();

    expect(Lead::where('email', 'novo@lead.com')->exists())->toBeTrue();
    expect(Deal::where('title', 'Primeiro Negócio')->exists())->toBeTrue();
});

it('new deal appears in New Lead pipeline stage', function () {
    $owner = User::factory()->businessOwner()->create();

    Livewire::actingAs($owner)
        ->test('pages::kanban.index')
        ->set('form.email', 'novo@lead.com')
        ->call('searchLead')
        ->set('form.name', 'Novo Lead')
        ->set('form.deal_title', 'Negócio Teste')
        ->set('form.deal_value', '500.00')
        ->call('createLead');

    $deal = Deal::where('title', 'Negócio Teste')->first();
    $newLeadStage = PipelineStage::where('name', 'New Lead')->first();

    expect($deal->pipeline_stage_id)->toBe($newLeadStage->id);
});

it('new deal is assigned to the current user', function () {
    $owner = User::factory()->businessOwner()->create();

    Livewire::actingAs($owner)
        ->test('pages::kanban.index')
        ->set('form.email', 'novo@lead.com')
        ->call('searchLead')
        ->set('form.name', 'Novo Lead')
        ->set('form.deal_title', 'Meu Negócio')
        ->set('form.deal_value', '1000.00')
        ->call('createLead');

    $deal = Deal::where('title', 'Meu Negócio')->first();
    expect($deal->user_id)->toBe($owner->id);
});

it('lead email is unique per tenant', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    Lead::factory()->create(['tenant_id' => $tenant->id, 'user_id' => $owner->id, 'email' => 'duplicado@email.com']);

    Livewire::actingAs($owner)
        ->test('pages::kanban.index')
        ->set('form.email', 'duplicado@email.com')
        ->call('searchLead')
        ->assertSet('leadFound', true);
});

it('same email in different tenants is allowed', function () {
    $tenant1 = Tenant::factory()->create();
    $tenant2 = Tenant::factory()->create();
    $owner1 = User::factory()->businessOwner()->for($tenant1)->create();
    $owner2 = User::factory()->businessOwner()->for($tenant2)->create();

    Lead::factory()->create(['tenant_id' => $tenant1->id, 'user_id' => $owner1->id, 'email' => 'shared@email.com']);

    Livewire::actingAs($owner2)
        ->test('pages::kanban.index')
        ->set('form.email', 'shared@email.com')
        ->call('searchLead')
        ->assertSet('leadFound', false)
        ->set('form.name', 'Lead Tenant 2')
        ->set('form.deal_title', 'Negócio')
        ->set('form.deal_value', '500.00')
        ->call('createLead')
        ->assertHasNoErrors();

    expect(Lead::withoutGlobalScopes()->where('email', 'shared@email.com')->count())->toBe(2);
});

it('lead fields validated for new lead', function () {
    $owner = User::factory()->businessOwner()->create();

    Livewire::actingAs($owner)
        ->test('pages::kanban.index')
        ->set('form.email', 'novo@lead.com')
        ->call('searchLead')
        ->set('form.name', '')
        ->set('form.deal_title', '')
        ->set('form.deal_value', '')
        ->call('createLead')
        ->assertHasErrors(['form.name', 'form.deal_title', 'form.deal_value']);
});

it('deal fields validated', function () {
    $owner = User::factory()->businessOwner()->create();

    Livewire::actingAs($owner)
        ->test('pages::kanban.index')
        ->set('form.email', 'novo@lead.com')
        ->call('searchLead')
        ->set('form.name', 'Lead')
        ->set('form.deal_title', '')
        ->set('form.deal_value', '-10')
        ->call('createLead')
        ->assertHasErrors(['form.deal_title', 'form.deal_value']);
});
