<?php

use App\Models\Invitation;
use App\Models\Tenant;
use App\Models\User;
use Livewire\Livewire;

beforeEach(fn () => $this->seed(\Database\Seeders\DatabaseSeeder::class));

it('can view registration page with valid token', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    $invitation = Invitation::factory()->create([
        'tenant_id' => $tenant->id,
        'invited_by_user_id' => $owner->id,
    ]);

    $this->get(route('register.invited', $invitation->token))
        ->assertSuccessful()
        ->assertSee('Aceitar convite');
});

it('can register via invitation', function () {
    $tenant = Tenant::factory()->create(['name' => 'Empresa Teste']);
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    $invitation = Invitation::factory()->create([
        'tenant_id' => $tenant->id,
        'invited_by_user_id' => $owner->id,
        'email' => 'novo@vendedor.com',
    ]);

    Livewire::test('pages::auth.register-invited', ['token' => $invitation->token])
        ->set('form.name', 'Novo Vendedor')
        ->set('form.password', 'password123')
        ->set('form.password_confirmation', 'password123')
        ->call('register')
        ->assertHasNoErrors()
        ->assertRedirect(route('kanban.index'));

    $user = User::where('email', 'novo@vendedor.com')->first();

    expect($user)
        ->not->toBeNull()
        ->name->toBe('Novo Vendedor')
        ->tenant_id->toBe($tenant->id);

    expect($user->role->name)->toBe('Salesperson');
    expect($user->userStatus->name)->toBe('Active');
});

it('invitation status is updated to accepted after registration', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    $invitation = Invitation::factory()->create([
        'tenant_id' => $tenant->id,
        'invited_by_user_id' => $owner->id,
    ]);

    Livewire::test('pages::auth.register-invited', ['token' => $invitation->token])
        ->set('form.name', 'Vendedor')
        ->set('form.password', 'password123')
        ->set('form.password_confirmation', 'password123')
        ->call('register');

    $invitation->refresh();
    expect($invitation->invitationStatus->name)->toBe('Accepted');
});

it('user is authenticated after registration', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    $invitation = Invitation::factory()->create([
        'tenant_id' => $tenant->id,
        'invited_by_user_id' => $owner->id,
    ]);

    Livewire::test('pages::auth.register-invited', ['token' => $invitation->token])
        ->set('form.name', 'Vendedor')
        ->set('form.password', 'password123')
        ->set('form.password_confirmation', 'password123')
        ->call('register');

    $this->assertAuthenticated();
});

it('shows error for invalid token', function () {
    $this->get(route('register.invited', 'invalid-token-123'))
        ->assertSuccessful()
        ->assertSee('Convite inválido');
});

it('shows error for expired invitation', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    $invitation = Invitation::factory()->expired()->create([
        'tenant_id' => $tenant->id,
        'invited_by_user_id' => $owner->id,
    ]);

    $this->get(route('register.invited', $invitation->token))
        ->assertSuccessful()
        ->assertSee('Este convite expirou');
});

it('shows error for revoked invitation', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    $invitation = Invitation::factory()->revoked()->create([
        'tenant_id' => $tenant->id,
        'invited_by_user_id' => $owner->id,
    ]);

    $this->get(route('register.invited', $invitation->token))
        ->assertSuccessful()
        ->assertSee('Este convite já foi utilizado ou revogado');
});

it('shows error for already accepted invitation', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    $invitation = Invitation::factory()->accepted()->create([
        'tenant_id' => $tenant->id,
        'invited_by_user_id' => $owner->id,
    ]);

    $this->get(route('register.invited', $invitation->token))
        ->assertSuccessful()
        ->assertSee('Este convite já foi utilizado ou revogado');
});

it('validates required fields', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    $invitation = Invitation::factory()->create([
        'tenant_id' => $tenant->id,
        'invited_by_user_id' => $owner->id,
    ]);

    Livewire::test('pages::auth.register-invited', ['token' => $invitation->token])
        ->call('register')
        ->assertHasErrors(['form.name', 'form.password']);
});

it('validates password confirmation', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    $invitation = Invitation::factory()->create([
        'tenant_id' => $tenant->id,
        'invited_by_user_id' => $owner->id,
    ]);

    Livewire::test('pages::auth.register-invited', ['token' => $invitation->token])
        ->set('form.name', 'Vendedor')
        ->set('form.password', 'password123')
        ->set('form.password_confirmation', 'different123')
        ->call('register')
        ->assertHasErrors('form.password');
});

it('displays tenant name on the page', function () {
    $tenant = Tenant::factory()->create(['name' => 'Minha Empresa']);
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    $invitation = Invitation::factory()->create([
        'tenant_id' => $tenant->id,
        'invited_by_user_id' => $owner->id,
    ]);

    Livewire::test('pages::auth.register-invited', ['token' => $invitation->token])
        ->assertSee('Minha Empresa');
});
