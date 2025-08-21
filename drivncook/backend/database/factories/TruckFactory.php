<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Franchisee;

class TruckFactory extends Factory
{
    public function definition(): array
    {
        $statuses = ['en_service','en_panne','entretien'];

        return [
            'franchisee_id'     => Franchisee::factory(),            // crée un franchisé si besoin
            'plate_number'      => strtoupper($this->faker->bothify('??-###-??')), // unique en DB
            'model'             => $this->faker->randomElement(['Citroën Jumper','Peugeot Boxer','Renault Master']),
            'current_location'  => $this->faker->city(),
            'status'            => $this->faker->randomElement($statuses),
            'last_service_date' => $this->faker->optional()->date(),
            'next_service_due'  => $this->faker->optional()->dateTimeBetween('now', '+6 months'),
            'notes'             => $this->faker->optional()->sentence(),
        ];
    }
}
