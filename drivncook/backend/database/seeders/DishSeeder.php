<?php 

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Dish;

class DishSeeder extends Seeder
{
    public function run(): void
    {
        Dish::truncate(); // Réinitialise la table (facultatif en prod)

        $dishes = [
            [
                'menu_id' => 1,
                'name_fr' => 'Croque-Monsieur',
                'name_en' => 'Ham and Cheese Toast',
                'description_fr' => 'Pain de mie grillé, jambon et fromage fondu.',
                'description_en' => 'Grilled white bread with ham and melted cheese.',
                'price' => 6.50,
                'image_url' => 'https://example.com/images/croque.jpg',
                'available' => true,
            ],
            [
                'menu_id' => 1,
                'name_fr' => 'Salade César',
                'name_en' => 'Caesar Salad',
                'description_fr' => 'Salade romaine, croûtons, parmesan et sauce César.',
                'description_en' => 'Romaine lettuce, croutons, parmesan, Caesar dressing.',
                'price' => 7.90,
                'image_url' => 'https://example.com/images/cesar.jpg',
                'available' => true,
            ],
            [
                'menu_id' => 1,
                'name_fr' => 'Tacos Poulet',
                'name_en' => 'Chicken Tacos',
                'description_fr' => 'Tortillas de maïs garnies de poulet, légumes et sauce.',
                'description_en' => 'Corn tortillas filled with chicken, veggies and sauce.',
                'price' => 8.20,
                'image_url' => 'https://example.com/images/tacos.jpg',
                'available' => true,
            ],
        ];

        foreach ($dishes as $dish) {
            Dish::create($dish);
        }
    }
}
