<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Collection;

class OrderItemController extends Controller
{
    public function index(): Collection
    {
        return OrderItem::with(['order', 'menu'])->get();
    }

    public function store(Request $request): OrderItem
    {
        $validated = $request->validate([
            'order_id'   => 'required|exists:orders,id',
            'menu_id'    => 'required|exists:menus,id',
            'quantity'   => 'required|integer|min:1',
            'price_unit' => 'required|numeric|min:0',
        ]);

        return OrderItem::create($validated);
    }

    public function show($id): OrderItem
    {
        return OrderItem::with(['order', 'menu'])->findOrFail($id);
    }

    public function update(Request $request, $id): OrderItem
    {
        $orderItem = OrderItem::findOrFail($id);

        $validated = $request->validate([
            'menu_id'    => 'sometimes|exists:menus,id',
            'quantity'   => 'sometimes|integer|min:1',
            'price_unit' => 'sometimes|numeric|min:0',
        ]);

        $orderItem->update($validated);
        return $orderItem->load(['order', 'menu']);
    }

    public function destroy($id): Response
    {
        OrderItem::destroy($id);
        return response()->noContent();
    }
}
