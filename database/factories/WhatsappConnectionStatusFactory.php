<?php

namespace Database\Factories;

use App\Models\WhatsappConnectionStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<WhatsappConnectionStatus> */
class WhatsappConnectionStatusFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
        ];
    }
}
