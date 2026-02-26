<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = ['Active', 'Inactive'];

        foreach ($statuses as $status) {
            DB::table('user_statuses')->updateOrInsert(
                ['name' => $status],
                ['name' => $status, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
