<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Connexion API - renvoie un token Sanctum
     */
    public function login(Request $request)
    {
        // Validation rapide (tu peux affiner selon tes besoins)
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Recherche manuelle de l'utilisateur
        $user = User::where('email', $request->input('email'))->first();

        // Vérification du mot de passe - CORRIGÉ ICI
        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return response()->json(['message' => 'Identifiants invalides'], 401);
        }

        // Création d'un token Sanctum
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }

    /**
     * Déconnexion - supprime le token courant
     */
    public function logout(Request $request)

    {
        // Suppression du token courant si présent
        $token = $request->user()->currentAccessToken();
        if ($token && method_exists($token, 'delete')) {
            $token->delete();
        }

        return response()->json(['message' => 'Déconnexion réussie']);
    }
}