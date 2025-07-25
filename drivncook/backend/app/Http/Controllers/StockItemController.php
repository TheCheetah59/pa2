<?php

namespace App\Http\Controllers;

use App\Models\StockItem;
use Illuminate\Http\Request;

class StockItemController extends Controller
{
    public function index()
    {
        return StockItem::with('warehouse')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id'   => 'required|exists:warehouses,id',
            'name'           => 'required|string|max:255',
            'unit_price'     => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'category'       => 'nullable|string|max:100',
        ]);

        return StockItem::create($validated);
    }

    public function show($id)
    {
        return StockItem::with('warehouse')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $stockItem = StockItem::findOrFail($id);

        $validated = $request->validate([
            'warehouse_id'   => 'sometimes|exists:warehouses,id',
            'name'           => 'sometimes|string|max:255',
            'unit_price'     => 'sometimes|numeric|min:0',
            'stock_quantity' => 'sometimes|integer|min:0',
            'category'       => 'nullable|string|max:100',
        ]);

        $stockItem->update($validated);
        return $stockItem;
    }

    public function destroy($id)
    {
        StockItem::destroy($id);
        return response()->noContent();
    }
}
