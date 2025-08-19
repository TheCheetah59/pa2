<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\User;
use App\Notifications\ActivationLink;

class AuthController extends Controller
{
    /**
     * Register a new user and send activation link.
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|string|in:client,franchise',
        ]);

        $token = Str::random(60);

        $user = User::create([
            'name'                        => $data['name'],
            'email'                       => $data['email'],
            'password'                    => Hash::make($data['password']),
            'role'                        => $data['role'],
            'is_activated'                => false,
            'activation_token'            => $token,
            'activation_token_expires_at' => now()->addDay(),
        ]);

        $user->notify(new ActivationLink($token));

        return response()->json([
            'message' => 'User registered. Please check your email for activation link.',
        ], 201);
    }

    /**
     * Session-based login (Sanctum + cookies).
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Identifiants invalides'], 401);
        }

        if (!$user->is_activated) {
            return response()->json(['message' => 'Compte non activé'], 403);
        }

        // Authentification par session
        if (!Auth::attempt($credentials, true)) {
            return response()->json(['message' => 'Échec de connexion'], 401);
        }

        $request->session()->regenerate();

        return response()->json([
            'user'  => $user->only(['id','name','email','role','is_activated']),
            'message' => 'Connecté',
        ]);
    }

    /**
     * Utilisateur authentifié (via session Sanctum).
     */
    public function me(Request $request)
    {
        return response()->json(Auth::user());
    }

    /**
     * Admin courant si authentifié et activé.
     */
    public function admin(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        if (!$user->is_activated) {
            return response()->json(['message' => 'Account inactive.'], 403);
        }

        return response()->json($user);
    }

    /**
     * Déconnexion (invalidation de session + CSRF).
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Déconnexion réussie']);
    }
}
