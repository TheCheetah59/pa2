<?php

namespace App\Http\Controllers;

use App\Models\StockOrder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Collection;

class StockOrderController extends Controller
{
    public function index(): Collection
    {
        return StockOrder::with(['franchisee', 'warehouse', 'items.stockItem'])->get();
    }

    public function store(Request $request): StockOrder
    {
        $validated = $request->validate([
            'franchisee_id' => 'required|exists:franchisees,id',
            'warehouse_id'  => 'required|exists:warehouses,id',
            'total_price'   => 'required|numeric|min:0',
        ]);

        return StockOrder::create($validated);
    }

    public function show($id): StockOrder
    {
        return StockOrder::with(['franchisee', 'warehouse', 'items.stockItem'])->findOrFail($id);
    }

    public function update(Request $request, $id): StockOrder
    {
        $stockOrder = StockOrder::findOrFail($id);

        $validated = $request->validate([
            'franchisee_id' => 'sometimes|exists:franchisees,id',
            'warehouse_id'  => 'sometimes|exists:warehouses,id',
            'total_price'   => 'sometimes|numeric|min:0',
        ]);

        $stockOrder->update($validated);
        return $stockOrder;
    }

    public function destroy($id): Response
    {
        StockOrder::destroy($id);
        return response()->noContent();
    }
}
