<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VentesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('ventes')->insert([
            ['date' => now(), 'montant' => 50.00, 'point_of_sale_id' => 1, 'seller_id' => 1, 'is_paid' => 1],
        ]);
    }
}
