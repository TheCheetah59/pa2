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
        ]);

        $token = Str::random(60);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_activated' => false,
            'activation_token' => $token,
            'activation_token_expires_at' => now()->addDay(),
        ]);

        $user->notify(new ActivationLink($token));

        return response()->json(['message' => 'User registered. Please check your email for activation link.'], 201);
    }

    /**
     * Activate a user using the given token.
     */
    public function activate(string $token)
    {
        $user = User::where('activation_token', $token)
            ->where('activation_token_expires_at', '>', now())
            ->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid or expired activation token'], 400);
        }

        $user->is_activated = true;
        $user->activation_token = null;
        $user->activation_token_expires_at = null;
        $user->save();

        return response()->json(['message' => 'Account activated successfully']);
    }

    /**
     * Connexion API - renvoie un token Sanctum
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->input('email'))->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return response()->json(['message' => 'Identifiants invalides'], 401);
        }

        if (!$user->is_activated) {
            return response()->json(['message' => 'Account not activated'], 403);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
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