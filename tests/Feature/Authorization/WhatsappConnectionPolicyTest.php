<?php

use App\Models\Tenant;
use App\Models\User;
use App\Models\WhatsappConnection;

beforeEach(fn () => $this->seed(\Database\Seeders\DatabaseSeeder::class));

it('business owner can manage whatsapp connection', function () {
    $tenant = Tenant::factory()->create();
    $owner = User::factory()->businessOwner()->for($tenant)->create();
    $connection = WhatsappConnection::factory()->create(['tenant_id' => $tenant->id]);

    expect($owner->can('manage', $connection))->toBeTrue();
});

it('salesperson cannot manage whatsapp connection', function () {
    $tenant = Tenant::factory()->create();
    $salesperson = User::factory()->salesperson()->for($tenant)->create();
    $connection = WhatsappConnection::factory()->create(['tenant_id' => $tenant->id]);

    expect($salesperson->can('manage', $connection))->toBeFalse();
});
