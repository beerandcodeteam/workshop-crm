<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\Tenant;
use App\Models\UserStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/** @extends Factory<\App\Models\User> */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'tenant_id' => Tenant::factory(),
            'role_id' => fn () => Role::where('name', 'Business Owner')->first()?->id ?? Role::factory()->businessOwner()->create()->id,
            'user_status_id' => fn () => UserStatus::where('name', 'Active')->first()?->id ?? UserStatus::factory()->active()->create()->id,
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function businessOwner(): static
    {
        return $this->state(fn () => [
            'role_id' => fn () => Role::where('name', 'Business Owner')->first()?->id ?? Role::factory()->businessOwner()->create()->id,
        ]);
    }

    public function salesperson(): static
    {
        return $this->state(fn () => [
            'role_id' => fn () => Role::where('name', 'Salesperson')->first()?->id ?? Role::factory()->salesperson()->create()->id,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'user_status_id' => fn () => UserStatus::where('name', 'Active')->first()?->id ?? UserStatus::factory()->active()->create()->id,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => [
            'user_status_id' => fn () => UserStatus::where('name', 'Inactive')->first()?->id ?? UserStatus::factory()->inactive()->create()->id,
        ]);
    }
}
