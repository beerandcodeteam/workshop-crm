<?php

namespace Database\Factories;

use App\Models\Deal;
use App\Models\Lead;
use App\Models\PipelineStage;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Deal> */
class DealFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'lead_id' => Lead::factory(),
            'user_id' => User::factory(),
            'pipeline_stage_id' => fn () => PipelineStage::where('name', 'New Lead')->first()?->id ?? PipelineStage::factory()->create()->id,
            'title' => fake()->sentence(3),
            'value' => fake()->randomFloat(2, 100, 50000),
            'loss_reason' => null,
            'sort_order' => 0,
        ];
    }

    public function won(): static
    {
        return $this->state(fn () => [
            'pipeline_stage_id' => fn () => PipelineStage::where('name', 'Won')->first()?->id ?? PipelineStage::factory()->terminal()->create(['name' => 'Won'])->id,
        ]);
    }

    public function lost(string $reason = 'Cliente desistiu'): static
    {
        return $this->state(fn () => [
            'pipeline_stage_id' => fn () => PipelineStage::where('name', 'Lost')->first()?->id ?? PipelineStage::factory()->terminal()->create(['name' => 'Lost'])->id,
            'loss_reason' => $reason,
        ]);
    }
}
