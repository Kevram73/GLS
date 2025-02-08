<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PointOfSalesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('point_of_sales')->insert([
            ['name' => 'Magasin Central', 'address' => 'Rue 1', 'city' => 'Paris', 'is_active' => 1],
            ['name' => 'Boutique Nord', 'address' => 'Rue 2', 'city' => 'Lyon', 'is_active' => 1],
        ]);
    }
}
