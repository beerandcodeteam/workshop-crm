<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvitationStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = ['Pending', 'Accepted', 'Revoked', 'Expired'];

        foreach ($statuses as $status) {
            DB::table('invitation_statuses')->updateOrInsert(
                ['name' => $status],
                ['name' => $status, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
