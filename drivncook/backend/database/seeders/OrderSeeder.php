<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\Menu;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $menus = Menu::all();

        Customer::all()->each(function ($customer) use ($menus) {
            for ($i = 0; $i < 2; $i++) {
                $order = Order::create([
                    'customer_id' => $customer->id,
                    'total_price' => 0,
                    'status' => 'en_attente',
                ]);

                $total = 0;

                $items = $menus->random(2);

                foreach ($items as $menu) {
                    $qty = rand(1, 3);
                    OrderItem::create([
                        'order_id' => $order->id,
                        'menu_id' => $menu->id,
                        'quantity' => $qty,
                        'price_unit' => $menu->price,
                    ]);
                    $total += $qty * $menu->price;
                }

                $order->update(['total_price' => $total]);
            }
        });
    }
}
