<?php

namespace Database\Factories;

use App\Models\UserStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<UserStatus> */
class UserStatusFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
        ];
    }

    public function active(): static
    {
        return $this->state(['name' => 'Active']);
    }

    public function inactive(): static
    {
        return $this->state(['name' => 'Inactive']);
    }
}
