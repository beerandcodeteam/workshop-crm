<?php

namespace Database\Factories;

use App\Models\InvitationStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<InvitationStatus> */
class InvitationStatusFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
        ];
    }
}
