<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NotificationsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('notifications')->insert([
            [
                'user_id' => 1, // ID de l'utilisateur
                'title' => 'Bienvenue sur la plateforme',
                'content' => 'Votre compte a été créé avec succès.',
                'is_read' => 0, // Non lu
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => 1,
                'title' => 'Nouvelle vente enregistrée',
                'content' => 'Vous avez une nouvelle vente dans votre point de vente.',
                'is_read' => 1, // Lu
                'created_at' => Carbon::now()->subDay(),
                'updated_at' => Carbon::now()->subDay(),
            ],
        ]);
    }
}
