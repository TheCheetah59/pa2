<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerOrder;
use Illuminate\Support\Facades\Auth;

class CustomerOrderController extends Controller
{
    // GET /api/customer-orders
    public function index()
    {
        $customer = Auth::guard('customer')->user();

        return $customer->orders()->with('items.dish')->latest()->get();
    }

    // GET /api/customer-orders/{id}
    public function show($id)
    {
        $customer = Auth::guard('customer')->user();

        $order = CustomerOrder::with('items.dish')
            ->where('customer_id', $customer->id)
            ->findOrFail($id);

        return response()->json($order);
    }

    // POST /api/customer-orders
    public function store(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.dish_id' => 'required|exists:dishes,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $order = CustomerOrder::create([
            'customer_id' => $customer->id,
            'status' => 'en_attente', // autre possible : 'confirmée'
        ]);

        foreach ($validated['items'] as $item) {
            $order->items()->create([
                'dish_id' => $item['dish_id'],
                'quantity' => $item['quantity'],
            ]);
        }

        return response()->json($order->load('items.dish'), 201);
    }
}
