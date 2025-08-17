<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:client,franchise',
        ]);

        $token = Str::random(60);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'is_activated' => false,
            'activation_token' => $token,
            'activation_token_expires_at' => now()->addDay(),
        ]);

        $user->notify(new ActivationLink($token));

        return response()->json([
            'message' => 'User registered. Please check your email for activation link.',
        ], 201);
    }
    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Identifiants invalides'], 401);
        }

        if (!$user->is_activated) {
            return response()->json(['message' => 'Compte non activé'], 403);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'name'  => $user->name,
            'email' => $user->email,
            'role'  => $user->role,
        ]);
    }



    /**

     * Return authenticated user.
     */
    public function me(Request $request)
    {
        return response()->json($request->user());
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