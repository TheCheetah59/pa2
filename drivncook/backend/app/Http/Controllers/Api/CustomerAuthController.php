<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use App\Models\Customer;

class CustomerAuthController extends Controller
{
    /**
     * Connexion CLIENT via session (Sanctum + cookies)
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $customer = Customer::where('email', $credentials['email'])->first();

        if (!$customer || !Hash::check($credentials['password'], $customer->password)) {
            return response()->json(['message' => 'Identifiants invalides'], 401);
        }

        // Auth par le guard "customer" (session)
        if (!Auth::guard('customer')->attempt($credentials, true)) {
            return response()->json(['message' => 'Échec de connexion'], 401);
        }

        $request->session()->regenerate();

        return response()->json([
            'customer' => $customer->only(['id','name','email']),
            'message'  => 'Connecté',
        ]);
    }

    /**
     * Déconnexion CLIENT (session)
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Déconnexion réussie']);
    }
}
