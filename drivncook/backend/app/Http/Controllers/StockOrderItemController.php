<?php

namespace App\Http\Controllers;

use App\Models\StockOrderItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\StockOrder;
use App\Models\StockItem;
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

                // Enforce mandatory stock ratio (80% mandatory / 20% free)
        $stockOrder = StockOrder::with('items.stockItem')->findOrFail($validated['stock_order_id']);
        $mandatoryQty = 0;
        $totalQty = 0;

        foreach ($stockOrder->items as $item) {
            $qty = $item->quantity;
            $totalQty += $qty;
            if ($item->stockItem->is_mandatory) {
                $mandatoryQty += $qty;
            }
        }

        $newStockItem = StockItem::findOrFail($validated['stock_item_id']);
        $totalQty += $validated['quantity'];
        if ($newStockItem->is_mandatory) {
            $mandatoryQty += $validated['quantity'];
        }

        if ($mandatoryQty / $totalQty < 0.8) {
            return response()->json(['message' => 'At least 80% of ordered quantity must be mandatory stock'], 422);
        }


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

                // Determine the order and stock item after potential updates
        $stockOrderId = $validated['stock_order_id'] ?? $item->stock_order_id;
        $stockItemId  = $validated['stock_item_id'] ?? $item->stock_item_id;

        $stockOrder = StockOrder::with('items.stockItem')->findOrFail($stockOrderId);
        $mandatoryQty = 0;
        $totalQty = 0;

        foreach ($stockOrder->items as $orderItem) {
            if ($orderItem->id === $item->id) {
                // Use new quantity if provided
                $qty = $validated['quantity'] ?? $orderItem->quantity;
                $itemMandatory = ($validated['stock_item_id'] ?? $orderItem->stock_item_id);
                $stockItemObj = StockItem::findOrFail($itemMandatory);
            } else {
                $qty = $orderItem->quantity;
                $stockItemObj = $orderItem->stockItem;
            }

            $totalQty += $qty;
            if ($stockItemObj->is_mandatory) {
                $mandatoryQty += $qty;
            }
        }

        if ($mandatoryQty / $totalQty < 0.8) {
            return response()->json(['message' => 'At least 80% of ordered quantity must be mandatory stock'], 422);
        }

        $item->update($validated);
        return $item;
    }

    public function destroy($id): Response
    {
        StockOrderItem::destroy($id);
        return response()->noContent();
    }
}
