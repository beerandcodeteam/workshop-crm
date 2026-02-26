<?php

use App\Models\User;
use Livewire\Livewire;

beforeEach(fn () => $this->seed(\Database\Seeders\DatabaseSeeder::class));

it('can view the registration page', function () {
    $this->get('/register')
        ->assertSuccessful()
        ->assertSee('Crie sua conta para começar');
});

it('can register with valid data', function () {
    Livewire::test('pages::auth.register')
        ->set('form.company_name', 'Minha Empresa')
        ->set('form.name', 'Lucas Silva')
        ->set('form.email', 'lucas@email.com')
        ->set('form.password', 'password123')
        ->set('form.password_confirmation', 'password123')
        ->call('register')
        ->assertHasNoErrors()
        ->assertRedirect(route('kanban.index'));

    expect(User::where('email', 'lucas@email.com')->exists())->toBeTrue();
});

it('creates a tenant with the company name', function () {
    Livewire::test('pages::auth.register')
        ->set('form.company_name', 'Empresa Teste')
        ->set('form.name', 'Lucas Silva')
        ->set('form.email', 'lucas@email.com')
        ->set('form.password', 'password123')
        ->set('form.password_confirmation', 'password123')
        ->call('register');

    $user = User::where('email', 'lucas@email.com')->first();

    expect($user->tenant->name)->toBe('Empresa Teste');
});

it('assigns Business Owner role and Active status', function () {
    Livewire::test('pages::auth.register')
        ->set('form.company_name', 'Empresa')
        ->set('form.name', 'Lucas')
        ->set('form.email', 'lucas@email.com')
        ->set('form.password', 'password123')
        ->set('form.password_confirmation', 'password123')
        ->call('register');

    $user = User::where('email', 'lucas@email.com')->first();

    expect($user->role->name)->toBe('Business Owner')
        ->and($user->userStatus->name)->toBe('Active');
});

it('is authenticated after registration', function () {
    Livewire::test('pages::auth.register')
        ->set('form.company_name', 'Empresa')
        ->set('form.name', 'Lucas')
        ->set('form.email', 'lucas@email.com')
        ->set('form.password', 'password123')
        ->set('form.password_confirmation', 'password123')
        ->call('register');

    $this->assertAuthenticated();
});

it('fails with duplicate email', function () {
    User::factory()->create(['email' => 'lucas@email.com']);

    Livewire::test('pages::auth.register')
        ->set('form.company_name', 'Empresa')
        ->set('form.name', 'Lucas')
        ->set('form.email', 'lucas@email.com')
        ->set('form.password', 'password123')
        ->set('form.password_confirmation', 'password123')
        ->call('register')
        ->assertHasErrors('form.email');
});

it('fails with short password', function () {
    Livewire::test('pages::auth.register')
        ->set('form.company_name', 'Empresa')
        ->set('form.name', 'Lucas')
        ->set('form.email', 'lucas@email.com')
        ->set('form.password', '123')
        ->set('form.password_confirmation', '123')
        ->call('register')
        ->assertHasErrors('form.password');
});

it('fails with password mismatch', function () {
    Livewire::test('pages::auth.register')
        ->set('form.company_name', 'Empresa')
        ->set('form.name', 'Lucas')
        ->set('form.email', 'lucas@email.com')
        ->set('form.password', 'password123')
        ->set('form.password_confirmation', 'different123')
        ->call('register')
        ->assertHasErrors('form.password');
});

it('fails with missing required fields', function () {
    Livewire::test('pages::auth.register')
        ->call('register')
        ->assertHasErrors(['form.company_name', 'form.name', 'form.email', 'form.password']);
});
