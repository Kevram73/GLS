<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConversationsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('conversations')->insert([
            ['participants' => json_encode([1, 2]), 'created_at' => now()],
        ]);
    }
}
