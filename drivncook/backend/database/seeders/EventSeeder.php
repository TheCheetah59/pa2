<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        Event::create([
            'title' => 'Atelier dégustation',
            'description' => 'Venez découvrir nos nouveaux plats !',
            'event_date' => Carbon::now()->addDays(2),
        ]);

        Event::create([
            'title' => 'Soirée fidélité',
            'description' => 'Réservée aux membres fidèles',
            'event_date' => Carbon::now()->addDays(5),
        ]);
    }
}

