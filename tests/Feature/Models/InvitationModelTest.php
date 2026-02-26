<?php

use App\Models\Invitation;
use App\Models\InvitationStatus;
use App\Models\Tenant;
use App\Models\User;

beforeEach(fn () => $this->seed(\Database\Seeders\DatabaseSeeder::class));

it('belongs to tenant', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->for($tenant)->create();
    $invitation = Invitation::factory()->create(['tenant_id' => $tenant->id, 'invited_by_user_id' => $user->id]);

    expect($invitation->tenant)->toBeInstanceOf(Tenant::class);
});

it('belongs to invitedBy (user)', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->for($tenant)->create();
    $invitation = Invitation::factory()->create(['tenant_id' => $tenant->id, 'invited_by_user_id' => $user->id]);

    expect($invitation->invitedBy)->toBeInstanceOf(User::class)
        ->and($invitation->invitedBy->id)->toBe($user->id);
});

it('belongs to invitationStatus', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->for($tenant)->create();
    $invitation = Invitation::factory()->create(['tenant_id' => $tenant->id, 'invited_by_user_id' => $user->id]);

    expect($invitation->invitationStatus)->toBeInstanceOf(InvitationStatus::class);
});

it('expires_at is cast to datetime', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->for($tenant)->create();
    $invitation = Invitation::factory()->create(['tenant_id' => $tenant->id, 'invited_by_user_id' => $user->id]);

    expect($invitation->expires_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
});
