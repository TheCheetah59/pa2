<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerAuthController extends Controller
{
    // POST /api/customer/login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // IMPORTANT : utiliser le guard web (provider 'users')
        if (! Auth::guard('web')->attempt($credentials, $request->boolean('remember'))) {
            return response()->json(['message' => 'Identifiants invalides'], 401);
        }

        $request->session()->regenerate();

        /** @var \App\Models\User $user */
        $user = $request->user(); // via guard web

        // Email non vérifié ?
        if (! $user->hasVerifiedEmail()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return response()->json(['message' => 'Veuillez vérifier votre email'], 423);
        }

        // Vérifier qu'il s'agit bien d'un client
        if (! in_array($user->role, ['client'])) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return response()->json(['message' => 'Rôle non autorisé pour cette route'], 403);
        }

        return response()->noContent(); // 204
    }

    // POST /api/customer/logout
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->noContent();
    }

    // GET /api/customer/me
    public function me(Request $request)
    {
        $u = $request->user();
        return response()->json([
            'id' => (int) $u->id,
            'name' => (string) ($u->name ?? ''),
            'email' => (string) $u->email,
            'role' => (string) ($u->role ?? 'client'),
        ], 200, ['Content-Type' => 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }
}
