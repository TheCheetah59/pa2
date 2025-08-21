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
    // POST /api/auth/register
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Password::defaults(), 'confirmed'],
            'role'     => ['nullable', 'in:client,franchise,admin'],
        ]);

        // Cast défensif -> garantit UTF‑8 à l’encodage JSON
        $name  = (string) ($data['name'] ?? '');
        $email = (string) ($data['email'] ?? '');
        $role  = (string) ($data['role'] ?? 'client');

        $user = User::create([
            'name'              => $name,
            'email'             => $email,
            'password'          => Hash::make($data['password']),
            'role'              => $role,
            'email_verified_at' => null,
        ]);

        event(new Registered($user));

        // Réponse minimale (pas d’Eloquent brut), forcer charset UTF‑8
        return response()->json([
            'message' => 'Inscription réussie. Vérifie ta boîte mail pour activer ton compte.',
            'user' => [
                'id'    => (int) $user->id,
                'name'  => (string) ($user->name ?? ''),
                'email' => (string) $user->email,
                'role'  => (string) ($user->role ?? 'client'),
            ],
        ], 201, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
    }

    // POST /api/auth/login -> 204 (aucun JSON, donc aucun risque UTF‑8)
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return response()->json(['message' => 'Identifiants invalides.'], 422);
        }

        $request->session()->regenerate();

        /** @var \App\Models\User $user */
        $user = $request->user();

        if (! $user->hasVerifiedEmail()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json(['message' => 'Veuillez vérifier votre email avant de vous connecter.'], 423);
        }

        return response()->noContent(); // 204
    }

    // POST /api/auth/logout -> 204
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->noContent();
    }

    // GET /api/auth/me -> payload minimal, casté
    public function me(Request $request)
    {
        $u = $request->user();

        return response()->json([
            'id'    => (int) $u->id,
            'name'  => (string) ($u->name ?? ''),
            'email' => (string) $u->email,
            'role'  => (string) ($u->role ?? 'client'),
        ], 200, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
    }
}
