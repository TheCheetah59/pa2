<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
        ]);

        $this->call([
            CustomerSeeder::class,
        ]);

        $this->call([
            EventSeeder::class,
        ]);

        $this->call(    [
            MenuSeeder::class,
        ]);

        $this->call([
            DishSeeder::class,
        ]);

    }
}
