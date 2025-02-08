<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MessageStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('message_statuses')->insert([
            [
                'message_id' => 1, // ID du message
                'recipient_id' => 2, // ID du destinataire
                'is_read' => 1, // Message lu
                'read_at' => Carbon::now()->subMinutes(10),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'message_id' => 2,
                'recipient_id' => 1,
                'is_read' => 0, // Non lu
                'read_at' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
