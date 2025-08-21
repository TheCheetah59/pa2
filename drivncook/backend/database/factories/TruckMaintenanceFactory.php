<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Truck;

class TruckMaintenanceFactory extends Factory
{
    public function definition(): array
    {
        $types = ['revision','panne','reparation','autre'];

        return [
            'truck_id'    => Truck::factory(),
            'date'        => $this->faker->date(),
            'description' => $this->faker->sentence(),
            'type'        => $this->faker->randomElement($types),   // ✅ pas besoin de "array:"
            'notes'       => $this->faker->optional()->paragraph(),
            'cost'        => $this->faker->randomFloat(2, 0, 1500),
        ];
    }
}
