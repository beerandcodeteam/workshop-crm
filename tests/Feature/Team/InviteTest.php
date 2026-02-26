<?php

use App\Models\Invitation;
use App\Models\Tenant;
use App\Models\User;
use App\Notifications\InvitationSentNotification;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

beforeEach(fn () => $this->seed(\Database\Seeders\DatabaseSeeder::class));

it('business owner can see invite button', function () {
    $owner = User::factory()->businessOwner()->create();

    Livewire::actingAs($owner)
        ->test('pages::team.index')
        ->assertSee('Convidar');
});

it('business owner can send invitation', function () {
    Notification::fake();

    $owner = User::factory()->businessOwner()->create();

    Livewire::actingAs($owner)
        ->test('pages::team.index')
        ->set('form.email', 'novo@vendedor.com')
        ->call('invite')
        ->assertHasNoErrors();

    expect(Invitation::where('email', 'novo@vendedor.com')->exists())->toBeTrue();

    Notification::assertSentOnDemand(InvitationSentNotification::class);
});

it('invitation is created with correct data', function () {
    Notification::fake();

    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();

    Livewire::actingAs($owner)
        ->test('pages::team.index')
        ->set('form.email', 'vendedor@email.com')
        ->call('invite')
        ->assertHasNoErrors();

    $invitation = Invitation::withoutGlobalScopes()->where('email', 'vendedor@email.com')->first();

    expect($invitation)
        ->tenant_id->toBe($tenant->id)
        ->invited_by_user_id->toBe($owner->id)
        ->token->not->toBeEmpty()
        ->expires_at->not->toBeNull();

    expect($invitation->invitationStatus->name)->toBe('Pending');
});

it('salesperson cannot send invitation', function () {
    $salesperson = User::factory()->salesperson()->create();

    Livewire::actingAs($salesperson)
        ->test('pages::team.index')
        ->assertForbidden();
});

it('cannot invite already registered email in same tenant', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    User::factory()->salesperson()->for($tenant)->create(['email' => 'existe@email.com']);

    Livewire::actingAs($owner)
        ->test('pages::team.index')
        ->set('form.email', 'existe@email.com')
        ->call('invite')
        ->assertHasErrors('form.email');
});

it('business owner can revoke pending invitation', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    $invitation = Invitation::factory()->create([
        'tenant_id' => $tenant->id,
        'invited_by_user_id' => $owner->id,
    ]);

    Livewire::actingAs($owner)
        ->test('pages::team.index')
        ->call('revoke', $invitation->id)
        ->assertHasNoErrors();

    $invitation->refresh();
    expect($invitation->invitationStatus->name)->toBe('Revoked');
});

it('validates email is required', function () {
    $owner = User::factory()->businessOwner()->create();

    Livewire::actingAs($owner)
        ->test('pages::team.index')
        ->set('form.email', '')
        ->call('invite')
        ->assertHasErrors('form.email');
});

it('validates email format', function () {
    $owner = User::factory()->businessOwner()->create();

    Livewire::actingAs($owner)
        ->test('pages::team.index')
        ->set('form.email', 'not-an-email')
        ->call('invite')
        ->assertHasErrors('form.email');
});

it('shows pending invitations', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    Invitation::factory()->create([
        'tenant_id' => $tenant->id,
        'invited_by_user_id' => $owner->id,
        'email' => 'pendente@email.com',
    ]);

    Livewire::actingAs($owner)
        ->test('pages::team.index')
        ->assertSee('pendente@email.com')
        ->assertSee('Convites pendentes');
});
