<?php

namespace Database\Factories;

use App\Models\Franchisee;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FranchiseeFactory extends Factory
{
    protected $model = Franchisee::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'city' => $this->faker->city,
            'postal_code' => $this->faker->postcode,
            'country' => 'France',
            'siret_number' => $this->faker->numerify('##############'), // 14 chiffres
            'franchise_code' => strtoupper(Str::random(8)), // Code aléatoire unique
            'entry_fee_paid' => true,
            'sales_percentage' => 4.00
        ];
    }
}
