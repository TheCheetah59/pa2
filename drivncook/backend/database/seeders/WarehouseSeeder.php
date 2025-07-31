<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Warehouse;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        $regions = ['Nord', 'Sud', 'Est', 'Ouest'];

        foreach ($regions as $region) {
            Warehouse::create([
                'name' => 'Entrepôt ' . $region,
                'address' => fake()->address(),
                'postal_code' => fake()->postcode(),
                'city' => fake()->city(),
                'region' => $region,
                'phone' => fake()->phoneNumber(),
                'email' => fake()->unique()->safeEmail(),
                'capacity' => fake()->numberBetween(50, 200),
                'kitchen_available' => fake()->boolean(),
            ]);
        }
    }
}