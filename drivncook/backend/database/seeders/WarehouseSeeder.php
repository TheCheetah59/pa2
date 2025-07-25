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
                'region' => $region,
            ]);
        }
    }
}
