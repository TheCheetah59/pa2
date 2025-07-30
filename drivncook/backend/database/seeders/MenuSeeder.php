<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 0; $i < 6; $i++) {
            Menu::create([
                'name' => ucfirst(fake()->words(2, true)),
                'description' => fake()->sentence(),
                'is_active' => true,
            ]);
        }
    }
}
