<?php

namespace Database\Factories;

use App\Models\PipelineStage;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<PipelineStage> */
class PipelineStageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'sort_order' => fake()->unique()->numberBetween(1, 100),
            'is_terminal' => false,
        ];
    }

    public function terminal(): static
    {
        return $this->state(['is_terminal' => true]);
    }
}
