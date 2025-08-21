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
     * Inscription CLIENT avec login immédiat (session)
     */
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:customers,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $customer = Customer::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        Auth::guard('customer')->login($customer);
        $request->session()->regenerate();

        return response()->json([
            'customer' => $customer->only(['id','name','email']),
        ], 201);
    }


    /**
     * Connexion CLIENT via session (Sanctum + cookies)
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::guard('customer')->attempt($credentials, true)) {
            return response()->json(['message' => 'Identifiants invalides'], 401);
        }

        $request->session()->regenerate();

        $customer = Auth::guard('customer')->user();

        return response()->json([
            'customer' => $customer->only(['id','name','email']),
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
