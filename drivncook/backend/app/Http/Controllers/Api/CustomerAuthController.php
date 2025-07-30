<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use App\Models\Customer;
use Laravel\Sanctum\PersonalAccessToken;


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

        // Recherche du client par e-mail
        $customer = Customer::where('email', $request->input('email'))->first();

        // Vérification du mot de passe
        if (!$customer || !Hash::check($request->input('password'), $customer->password)) {
            return response()->json(['message' => 'Identifiants invalides'], 401);
        }

        // Création du token API
        $token = $customer->createToken('customer-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'customer' => $customer,
        ]);
    }

    /**
     * Déconnexion API du client
     */
    public function logout(Request $request): JsonResponse
    {
        /** @var PersonalAccessToken|null $token */
        $token = $request->user()?->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {
            $token->delete();

            return response()->json([
                'message' => 'Déconnexion réussie'
            ]);
        }

        return response()->json([
            'error' => 'Aucun token actif trouvé'
        ], 401);
    }

}
