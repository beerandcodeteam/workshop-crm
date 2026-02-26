<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PipelineStageSeeder extends Seeder
{
    public function run(): void
    {
        $stages = [
            ['name' => 'New Lead', 'sort_order' => 1, 'is_terminal' => false],
            ['name' => 'Contacted', 'sort_order' => 2, 'is_terminal' => false],
            ['name' => 'Qualified', 'sort_order' => 3, 'is_terminal' => false],
            ['name' => 'Proposal Sent', 'sort_order' => 4, 'is_terminal' => false],
            ['name' => 'Negotiation', 'sort_order' => 5, 'is_terminal' => false],
            ['name' => 'Won', 'sort_order' => 6, 'is_terminal' => true],
            ['name' => 'Lost', 'sort_order' => 7, 'is_terminal' => true],
        ];

        foreach ($stages as $stage) {
            DB::table('pipeline_stages')->updateOrInsert(
                ['name' => $stage['name']],
                [...$stage, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
