<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class OrderController extends Controller
{
    public function index(): Collection
    {
        return Order::with(['customer', 'items.menu'])->get();
    }

    public function store(Request $request): Order
    {
        $validated = $request->validate([
            'customer_id'        => 'required|exists:customers,id',
            'status'             => 'nullable|string',
            'items'              => 'required|array|min:1',
            'items.*.menu_id'    => 'required|exists:menus,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.price_unit' => 'nullable|numeric|min:0',
        ]);

        return DB::transaction(function () use ($validated) {
            $order = Order::create([
                'customer_id' => $validated['customer_id'],
                'status'      => $validated['status'] ?? 'en_attente',
                'total_price' => 0,
            ]);

            $total = 0;

            foreach ($validated['items'] as $item) {
                $menu = Menu::findOrFail($item['menu_id']);
                $priceUnit = $item['price_unit'] ?? $menu->price;
                $lineTotal = $priceUnit * $item['quantity'];

                OrderItem::create([
                    'order_id'   => $order->id,
                    'menu_id'    => $item['menu_id'],
                    'quantity'   => $item['quantity'],
                    'price_unit' => $priceUnit,
                ]);

                $total += $lineTotal;
            }

            $order->update(['total_price' => $total]);

            return $order->load(['customer', 'items.menu']);
        });
    }

    public function show($id): Order
    {
        return Order::with(['customer', 'items.menu'])->findOrFail($id);
    }

    public function update(Request $request, $id): Order
    {
        $order = Order::with('items')->findOrFail($id);

        $validated = $request->validate([
            'status'             => 'sometimes|string',
            'items'              => 'sometimes|array|min:1',
            'items.*.menu_id'    => 'required_with:items|exists:menus,id',
            'items.*.quantity'   => 'required_with:items|integer|min:1',
            'items.*.price_unit' => 'nullable|numeric|min:0',
        ]);

        return DB::transaction(function () use ($order, $validated) {
            if (isset($validated['status'])) {
                $order->update(['status' => $validated['status']]);
            }

            $total = 0;

            if (!empty($validated['items'])) {
                // Supprimer les anciens items
                $order->items()->delete();

                foreach ($validated['items'] as $item) {
                    $menu = Menu::findOrFail($item['menu_id']);
                    $priceUnit = $item['price_unit'] ?? $menu->price;
                    $lineTotal = $priceUnit * $item['quantity'];

                    OrderItem::create([
                        'order_id'   => $order->id,
                        'menu_id'    => $item['menu_id'],
                        'quantity'   => $item['quantity'],
                        'price_unit' => $priceUnit,
                    ]);

                    $total += $lineTotal;
                }

                $order->update(['total_price' => $total]);
            }

            return $order->load(['customer', 'items.menu']);
        });
    }

    public function destroy($id): Response
    {
        Order::destroy($id);
        return response()->noContent();
    }
}
