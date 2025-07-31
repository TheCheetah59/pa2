<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StockItem;
use App\Models\Warehouse;

class StockItemSeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Plat', 'Boisson', 'Ingrédient'];

        Warehouse::all()->each(function ($warehouse) use ($categories) {
            for ($i = 0; $i < 10; $i++) {
                StockItem::create([
                    'warehouse_id' => $warehouse->id,
                    'name' => fake()->word(),
                    'unit_price' => fake()->randomFloat(2, 1, 20),
                    'stock_quantity' => fake()->numberBetween(10, 200),
                    'category' => $categories[array_rand($categories)],
                    'is_mandatory' => fake()->boolean(80),
                ]);
            }
        });
    }
}
