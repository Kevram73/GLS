<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MessagesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('messages')->insert([
            ['conversation_id' => 1, 'sender_id' => 1, 'content' => 'Bonjour !', 'sent_at' => now()],
            ['conversation_id' => 1, 'sender_id' => 2, 'content' => 'Salut !', 'sent_at' => now()],
        ]);
    }
}
