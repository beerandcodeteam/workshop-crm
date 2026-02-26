<?php

use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

it('roles table has 2 records', function () {
    expect(DB::table('roles')->count())->toBe(2);
    expect(DB::table('roles')->pluck('name')->toArray())
        ->toContain('Business Owner')
        ->toContain('Salesperson');
});

it('user_statuses table has 2 records', function () {
    expect(DB::table('user_statuses')->count())->toBe(2);
    expect(DB::table('user_statuses')->pluck('name')->toArray())
        ->toContain('Active')
        ->toContain('Inactive');
});

it('pipeline_stages table has 7 records in correct sort order', function () {
    expect(DB::table('pipeline_stages')->count())->toBe(7);

    $stages = DB::table('pipeline_stages')->orderBy('sort_order')->get();

    expect($stages[0]->name)->toBe('New Lead')
        ->and($stages[0]->sort_order)->toBe(1)
        ->and($stages[0]->is_terminal)->toBeFalse();

    expect($stages[5]->name)->toBe('Won')
        ->and($stages[5]->sort_order)->toBe(6)
        ->and($stages[5]->is_terminal)->toBeTrue();

    expect($stages[6]->name)->toBe('Lost')
        ->and($stages[6]->sort_order)->toBe(7)
        ->and($stages[6]->is_terminal)->toBeTrue();
});

it('invitation_statuses table has 4 records', function () {
    expect(DB::table('invitation_statuses')->count())->toBe(4);
    expect(DB::table('invitation_statuses')->pluck('name')->toArray())
        ->toContain('Pending')
        ->toContain('Accepted')
        ->toContain('Revoked')
        ->toContain('Expired');
});

it('whatsapp_connection_statuses table has 2 records', function () {
    expect(DB::table('whatsapp_connection_statuses')->count())->toBe(2);
    expect(DB::table('whatsapp_connection_statuses')->pluck('name')->toArray())
        ->toContain('Connected')
        ->toContain('Disconnected');
});

it('seeders are idempotent - running twice does not duplicate', function () {
    $this->seed(DatabaseSeeder::class);

    expect(DB::table('roles')->count())->toBe(2);
    expect(DB::table('user_statuses')->count())->toBe(2);
    expect(DB::table('pipeline_stages')->count())->toBe(7);
    expect(DB::table('invitation_statuses')->count())->toBe(4);
    expect(DB::table('whatsapp_connection_statuses')->count())->toBe(2);
});
