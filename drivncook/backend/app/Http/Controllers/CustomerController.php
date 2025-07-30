<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    // POST /api/customers → inscription
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'password' => 'required|string|min:6|confirmed', // nécessite password_confirmation
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $customer = Customer::create($validated);

        return response()->json($customer, 201);
    }

    // GET /api/customers/{id}
    public function show($id): JsonResponse
    {
        $customer = Auth::guard('customer')->user();

        if ($customer->id != $id) {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        return response()->json($customer);
    }
        // PUT /api/customers/{id}
    public function update(Request $request, $id): JsonResponse
    {
        $authenticatedCustomer = $request->user();

        if (!$authenticatedCustomer || $authenticatedCustomer->id != $id) {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:customers,email,' . $id,
            'password' => 'sometimes|string|min:6|confirmed',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $authenticatedCustomer->update($validated);

        return response()->json($authenticatedCustomer->fresh());
    }

    // DELETE /api/customers/{id}
    public function destroy(Request $request, $id): JsonResponse
    {
        $authenticatedCustomer = $request->user();

        if (!$authenticatedCustomer || $authenticatedCustomer->id != $id) {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $authenticatedCustomer->delete();

        return response()->json(['message' => 'Compte supprimé'], 200);
    }
}
