<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * POST /api/auth/register
     * Inscription + envoi de l'email de vérification (Laravel native).
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Password::defaults(), 'confirmed'],
            'role'     => ['nullable', 'in:client,franchise,admin'],
        ]);

        $user = User::create([
            'name'              => $data['name'],
            'email'             => $data['email'],
            'password'          => Hash::make($data['password']),
            'role'              => $data['role'] ?? 'client',
            'email_verified_at' => null,
        ]);

        // Déclenche l'envoi de l'e-mail de vérification
        event(new Registered($user));

        // On ne connecte PAS automatiquement : le front affiche un message et redirige
        return response()->json([
            'message' => "Compte créé. Un email de vérification vous a été envoyé."
        ], 201);
    }

    /**
     * POST /api/auth/login
     * Authentification Sanctum (cookies). Refuse si email non vérifié.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Vérification des identifiants
        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return response()->json([
                'message' => 'Identifiants invalides.'
            ], 422);
        }

        // Regénère l'ID de session (sécurité)
        $request->session()->regenerate();

        /** @var \App\Models\User $user */
        $user = $request->user();

        // Bloque si l'email n'est pas vérifié
        if (! $user->hasVerifiedEmail()) {
            // Déconnecte proprement si pas vérifié
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'message' => 'Veuillez vérifier votre email avant de vous connecter.'
            ], 423);
        }

        return response()->json([
            'message' => 'Connexion réussie.',
            'user'    => $user,
        ]);
    }

    /**
     * POST /api/auth/logout
     * Déconnexion Sanctum (invalide la session et le token CSRF).
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Déconnecté.'
        ]);
    }

    /**
     * GET /api/auth/me
     * Retourne l'utilisateur courant (protégé par auth:sanctum).
     */
    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}