<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerFeedback;
use Illuminate\Support\Facades\Auth;

class CustomerFeedbackController extends Controller
{
    // GET /api/feedback
    public function index()
    {
        return CustomerFeedback::with('customer')->latest()->get();
    }

    // POST /api/feedback
    public function store(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        $feedback = CustomerFeedback::create([
            'customer_id' => $customer->id,
            'message' => $validated['message'],
            'rating' => $validated['rating'] ?? null,
        ]);

        return response()->json($feedback, 201);
    }
}
