<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Franchisee;
use Illuminate\Support\Str;

class FranchiseeSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            Franchisee::create([
                'name' => fake()->company(),
                'email' => fake()->unique()->safeEmail(),
                'phone' => fake()->phoneNumber(),
                'address' => fake()->address(),
                'city' => fake()->city(),
                'postal_code' => fake()->postcode(),
                'country' => 'France',
                'siret_number' => fake()->numerify('##############'),
                'franchise_code' => strtoupper(Str::random(8)),
                'entry_fee_paid' => true,
                'sales_percentage' => 4.00,
            ]);
        }
    }
}
