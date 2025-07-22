<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use App\Models\Customer;

class CustomerAuthController extends Controller
{
    /**
     * Connexion API pour les clients
     */
    public function login(Request $request): JsonResponse
    {
        // Validation simple
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Recherche du client
        $customer = Customer::where('email', $request->input('email'))->first();

        if (!$customer || !Hash::check($request->input('password'), $customer->input('password'))) {
            return response()->json(['message' => 'Identifiants invalides'], 401);
        }

        // Création du token Sanctum
        $token = $customer->createToken('customer-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'customer' => $customer,
        ]);
    }

    /**
     * Déconnexion API
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Déconnexion réussie']);
    }
}


