<?php

use App\Models\Tenant;
use App\Models\WhatsappConnection;
use App\Models\WhatsappConnectionStatus;

beforeEach(fn () => $this->seed(\Database\Seeders\DatabaseSeeder::class));

it('belongs to tenant', function () {
    $tenant = Tenant::factory()->create();
    $connection = WhatsappConnection::factory()->create(['tenant_id' => $tenant->id]);

    expect($connection->tenant)->toBeInstanceOf(Tenant::class);
});

it('belongs to whatsappConnectionStatus', function () {
    $connection = WhatsappConnection::factory()->create();

    expect($connection->whatsappConnectionStatus)->toBeInstanceOf(WhatsappConnectionStatus::class);
});

it('one per tenant enforced', function () {
    $tenant = Tenant::factory()->create();
    WhatsappConnection::factory()->create(['tenant_id' => $tenant->id]);

    expect(fn () => WhatsappConnection::factory()->create(['tenant_id' => $tenant->id]))
        ->toThrow(\Illuminate\Database\QueryException::class);
});
