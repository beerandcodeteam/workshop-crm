<?php

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\HtmlString;

it('app layout requires authentication for protected routes', function () {
    $this->get('/kanban')->assertRedirect('/login');
});

it('app layout renders sidebar navigation for authenticated user', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->for($tenant)->create();

    $this->actingAs($user);

    $html = view('layouts.app', [
        'slot' => new HtmlString('<p>Conteúdo principal</p>'),
    ])->render();

    expect($html)
        ->toContain('Dashboard')
        ->toContain('Kanban')
        ->toContain('Equipe')
        ->toContain('Conteúdo principal');
});

it('app layout displays authenticated user name', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->for($tenant)->create(['name' => 'João Silva']);

    $this->actingAs($user);

    $html = view('layouts.app', [
        'slot' => new HtmlString('Conteúdo'),
    ])->render();

    expect($html)->toContain('João Silva');
});
