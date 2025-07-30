<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        Event::truncate(); // Réinitialise la table

        $events = [
            [
                'title' => 'Atelier dégustation',
                'description' => 'Venez découvrir nos nouveaux plats !',
                'start_date' => Carbon::create(2025, 8, 1),
                'end_date' => Carbon::create(2025, 8, 1),
                'location' => 'Entrepôt Paris 12',
            ],
            [
                'title' => 'Journée client fidèle',
                'description' => 'Offres spéciales pour les clients ayant une carte de fidélité.',
                'start_date' => Carbon::create(2025, 8, 15),
                'end_date' => Carbon::create(2025, 8, 15),
                'location' => 'Camion Gare de Lyon',
            ],
            [
                'title' => 'Lancement menu d’été',
                'description' => 'Nouveaux plats disponibles en édition limitée.',
                'start_date' => Carbon::create(2025, 7, 28),
                'end_date' => Carbon::create(2025, 8, 31),
                'location' => 'Tous les camions',
            ],
        ];

        foreach ($events as $event) {
            Event::create($event);
        }
    }
}
