<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeUsersTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('type_users')->insert([
            ['name' => 'Admin'],
            ['name' => 'Commercial'],
            ['name' => 'Client'],
            ['name' => 'Manager'],
            
        ]);
    }
}
