<?php

namespace Database\Factories;

use App\Models\Invitation;
use App\Models\InvitationStatus;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Invitation> */
class InvitationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'invited_by_user_id' => User::factory(),
            'invitation_status_id' => fn () => InvitationStatus::where('name', 'Pending')->first()?->id ?? InvitationStatus::factory()->create(['name' => 'Pending'])->id,
            'email' => fake()->unique()->safeEmail(),
            'token' => Str::random(64),
            'expires_at' => now()->addHours(72),
        ];
    }

    public function expired(): static
    {
        return $this->state([
            'expires_at' => now()->subHour(),
        ]);
    }

    public function accepted(): static
    {
        return $this->state(fn () => [
            'invitation_status_id' => fn () => InvitationStatus::where('name', 'Accepted')->first()?->id ?? InvitationStatus::factory()->create(['name' => 'Accepted'])->id,
        ]);
    }

    public function revoked(): static
    {
        return $this->state(fn () => [
            'invitation_status_id' => fn () => InvitationStatus::where('name', 'Revoked')->first()?->id ?? InvitationStatus::factory()->create(['name' => 'Revoked'])->id,
        ]);
    }
}
