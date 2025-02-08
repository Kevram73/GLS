<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'nom' => 'Admin',
            'prenom' => 'Super',
            'num_phone' => '0123456789',
            'type_user_id' => 1,
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'actif' => 1,
        ]);

    }
}
