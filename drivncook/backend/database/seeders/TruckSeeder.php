<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Truck;
use App\Models\Franchisee;

class TruckSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = ['ok', 'en_panne', 'en_maintenance'];

        Franchisee::all()->each(function ($franchisee) use ($statuses) {
            Truck::factory()->count(2)->create([
                'franchisee_id' => $franchisee->id,
                'registration' => strtoupper(fake()->bothify('??-####-??')),
                'status' => $statuses[array_rand($statuses)],
                'location' => fake()->city(),
            ]);
        });
    }
}
