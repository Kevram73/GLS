<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JournalsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('journals')->insert([
            ['title' => 'Journal Matin', 'price' => 5.00, 'is_active' => 1],
            ['title' => 'Journal Soir', 'price' => 6.50, 'is_active' => 1],
        ]);
    }
}
