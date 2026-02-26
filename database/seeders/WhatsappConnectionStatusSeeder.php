<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WhatsappConnectionStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = ['Connected', 'Disconnected'];

        foreach ($statuses as $status) {
            DB::table('whatsapp_connection_statuses')->updateOrInsert(
                ['name' => $status],
                ['name' => $status, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
