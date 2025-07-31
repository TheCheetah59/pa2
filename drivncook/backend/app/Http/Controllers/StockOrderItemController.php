<?php

namespace App\Http\Controllers;

use App\Models\StockOrderItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\StockOrder;
use App\Models\StockItem;
use Illuminate\Http\JsonResponse; 
use Illuminate\Database\Eloquent\Collection;

class StockOrderItemController extends Controller
{
    public function index(): Collection
    {
        return StockOrderItem::with(['stockOrder', 'stockItem'])->get();
    }


    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'stock_order_id' => 'required|exists:stock_orders,id',
            'stock_item_id'  => 'required|exists:stock_items,id',
            'quantity'       => 'required|integer|min:1',
            'price_unit'     => 'required|numeric|min:0',
        ]);

        // Récupérer la commande avec ses items existants
        $stockOrder = StockOrder::with('items.stockItem')->findOrFail($validated['stock_order_id']);
        
        // Calculer les quantités actuelles
        $mandatoryQty = 0;
        $totalQty = 0;

        foreach ($stockOrder->items as $item) {
            $qty = $item->quantity;
            $totalQty += $qty;
            
            if ($item->stockItem->is_mandatory) {
                $mandatoryQty += $qty;
            }
        }

        // Récupérer le nouvel item à ajouter
        $newStockItem = StockItem::findOrFail($validated['stock_item_id']);
        
        // Calculer les nouvelles quantités après ajout
        $newTotalQty = $totalQty + $validated['quantity'];
        $newMandatoryQty = $mandatoryQty;
        
        if ($newStockItem->is_mandatory) {
            $newMandatoryQty += $validated['quantity'];
        }

        // Vérifier le ratio 80/20
        $mandatoryRatio = $newTotalQty > 0 ? ($newMandatoryQty / $newTotalQty) : 0;
        
        if ($mandatoryRatio < 0.8) {
            return response()->json([
                'message' => 'At least 80% of ordered quantity must be mandatory stock',
                'current_ratio' => round($mandatoryRatio * 100, 2) . '%',
                'required_ratio' => '80%'
            ], 422);
        }

        // Créer le nouvel item
        $stockOrderItem = StockOrderItem::create($validated);

        return response()->json($stockOrderItem, 201);
    }

    public function show($id): StockOrderItem
    {
        return StockOrderItem::with(['stockOrder', 'stockItem'])->findOrFail($id);
    }


    public function update(Request $request, $id): JsonResponse
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

        // Vérification du ratio 80/20
        $mandatoryRatio = $totalQty > 0 ? ($mandatoryQty / $totalQty) : 0;
        
        if ($mandatoryRatio < 0.8) {
            return response()->json([
                'message' => 'At least 80% of ordered quantity must be mandatory stock',
                'current_ratio' => round($mandatoryRatio * 100, 2) . '%',
                'required_ratio' => '80%'
            ], 422);
        }

        // Mise à jour de l'item
        $item->update($validated);
        
        // Recharger l'item avec ses relations si nécessaire
        $item->load('stockItem', 'stockOrder');
        
        return response()->json($item, 200);
    }

    public function destroy($id): Response
    {
        StockOrderItem::destroy($id);
        return response()->noContent();
    }
}
