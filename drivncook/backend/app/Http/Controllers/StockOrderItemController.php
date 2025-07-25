<?php

namespace App\Http\Controllers;

use App\Models\StockOrderItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Collection;

class StockOrderItemController extends Controller
{
    public function index(): Collection
    {
        return StockOrderItem::with(['stockOrder', 'stockItem'])->get();
    }

    public function store(Request $request): StockOrderItem
    {
        $validated = $request->validate([
            'stock_order_id' => 'required|exists:stock_orders,id',
            'stock_item_id'  => 'required|exists:stock_items,id',
            'quantity'       => 'required|integer|min:1',
            'price_unit'     => 'required|numeric|min:0',
        ]);

        return StockOrderItem::create($validated);
    }

    public function show($id): StockOrderItem
    {
        return StockOrderItem::with(['stockOrder', 'stockItem'])->findOrFail($id);
    }

    public function update(Request $request, $id): StockOrderItem
    {
        $item = StockOrderItem::findOrFail($id);

        $validated = $request->validate([
            'stock_order_id' => 'sometimes|exists:stock_orders,id',
            'stock_item_id'  => 'sometimes|exists:stock_items,id',
            'quantity'       => 'sometimes|integer|min:1',
            'price_unit'     => 'sometimes|numeric|min:0',
        ]);

        $item->update($validated);
        return $item;
    }

    public function destroy($id): Response
    {
        StockOrderItem::destroy($id);
        return response()->noContent();
    }
}
