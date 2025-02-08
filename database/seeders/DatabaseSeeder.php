<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            TypeUsersTableSeeder::class,
            UsersTableSeeder::class,
            PointOfSalesTableSeeder::class,
            JournalsTableSeeder::class,
            ConversationsTableSeeder::class,
            // MessageStatusesTableSeeder::class,
            // MessagesTableSeeder::class,
            VentesTableSeeder::class,
            NotificationsTableSeeder::class,
        ]);
    }
}
