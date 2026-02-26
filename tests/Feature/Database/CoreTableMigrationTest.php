<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

it('all core tables exist', function () {
    expect(Schema::hasTable('tenants'))->toBeTrue();
    expect(Schema::hasTable('users'))->toBeTrue();
    expect(Schema::hasTable('leads'))->toBeTrue();
    expect(Schema::hasTable('deals'))->toBeTrue();
    expect(Schema::hasTable('deal_notes'))->toBeTrue();
    expect(Schema::hasTable('invitations'))->toBeTrue();
    expect(Schema::hasTable('whatsapp_connections'))->toBeTrue();
});

it('users table has tenant and role columns', function () {
    expect(Schema::hasColumns('users', ['tenant_id', 'role_id', 'user_status_id']))->toBeTrue();
});

it('leads table has correct columns', function () {
    expect(Schema::hasColumns('leads', ['tenant_id', 'user_id', 'name', 'email', 'phone']))->toBeTrue();
});

it('deals table has correct columns', function () {
    expect(Schema::hasColumns('deals', [
        'tenant_id', 'lead_id', 'user_id', 'pipeline_stage_id',
        'title', 'value', 'loss_reason', 'sort_order',
    ]))->toBeTrue();
});

it('foreign keys enforce referential integrity on leads', function () {
    DB::table('tenants')->insert(['id' => 1, 'name' => 'Test Tenant', 'created_at' => now(), 'updated_at' => now()]);

    expect(fn () => DB::table('leads')->insert([
        'tenant_id' => 999,
        'user_id' => 1,
        'name' => 'Test',
        'email' => 'test@example.com',
        'created_at' => now(),
        'updated_at' => now(),
    ]))->toThrow(\Illuminate\Database\QueryException::class);
});

it('unique constraint enforced on tenant_id + email for leads', function () {
    $this->seed(\Database\Seeders\DatabaseSeeder::class);

    $tenant = DB::table('tenants')->insertGetId(['name' => 'Tenant', 'created_at' => now(), 'updated_at' => now()]);
    $role = DB::table('roles')->where('name', 'Business Owner')->value('id');
    $status = DB::table('user_statuses')->where('name', 'Active')->value('id');

    $userId = DB::table('users')->insertGetId([
        'tenant_id' => $tenant,
        'role_id' => $role,
        'user_status_id' => $status,
        'name' => 'Owner',
        'email' => 'owner@test.com',
        'password' => bcrypt('password'),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('leads')->insert([
        'tenant_id' => $tenant,
        'user_id' => $userId,
        'name' => 'Lead',
        'email' => 'lead@test.com',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    expect(fn () => DB::table('leads')->insert([
        'tenant_id' => $tenant,
        'user_id' => $userId,
        'name' => 'Lead Duplicate',
        'email' => 'lead@test.com',
        'created_at' => now(),
        'updated_at' => now(),
    ]))->toThrow(\Illuminate\Database\QueryException::class);
});

it('cascade deletes work - deleting tenant removes users and leads', function () {
    $this->seed(\Database\Seeders\DatabaseSeeder::class);

    $tenant = DB::table('tenants')->insertGetId(['name' => 'Cascade Tenant', 'created_at' => now(), 'updated_at' => now()]);
    $role = DB::table('roles')->where('name', 'Business Owner')->value('id');
    $status = DB::table('user_statuses')->where('name', 'Active')->value('id');

    $userId = DB::table('users')->insertGetId([
        'tenant_id' => $tenant,
        'role_id' => $role,
        'user_status_id' => $status,
        'name' => 'Owner',
        'email' => 'cascade-owner@test.com',
        'password' => bcrypt('password'),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('leads')->insert([
        'tenant_id' => $tenant,
        'user_id' => $userId,
        'name' => 'Cascade Lead',
        'email' => 'cascade-lead@test.com',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('tenants')->where('id', $tenant)->delete();

    expect(DB::table('users')->where('id', $userId)->exists())->toBeFalse();
    expect(DB::table('leads')->where('tenant_id', $tenant)->exists())->toBeFalse();
});

it('whatsapp_connections tenant_id is unique', function () {
    $this->seed(\Database\Seeders\DatabaseSeeder::class);

    $tenant = DB::table('tenants')->insertGetId(['name' => 'WA Tenant', 'created_at' => now(), 'updated_at' => now()]);
    $statusId = DB::table('whatsapp_connection_statuses')->where('name', 'Disconnected')->value('id');

    DB::table('whatsapp_connections')->insert([
        'tenant_id' => $tenant,
        'whatsapp_connection_status_id' => $statusId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    expect(fn () => DB::table('whatsapp_connections')->insert([
        'tenant_id' => $tenant,
        'whatsapp_connection_status_id' => $statusId,
        'created_at' => now(),
        'updated_at' => now(),
    ]))->toThrow(\Illuminate\Database\QueryException::class);
});
