<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Franchisee;
use Illuminate\Support\Facades\Hash;

class FranchiseeSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            Franchisee::create([
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'phone' => fake()->phoneNumber(),
                'address' => fake()->address(),
                'joined_at' => now()->subMonths(rand(1, 24)),
                'active' => true,
                'password' => Hash::make('franchise123'),
            ]);
        }
    }
}
