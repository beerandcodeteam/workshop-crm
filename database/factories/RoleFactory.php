<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Role> */
class RoleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
        ];
    }

    public function businessOwner(): static
    {
        return $this->state(['name' => 'Business Owner']);
    }

    public function salesperson(): static
    {
        return $this->state(['name' => 'Salesperson']);
    }
}
