<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * POST /api/auth/register
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Password::defaults(), 'confirmed'],
            'role'     => ['nullable', 'in:client,franchisee,admin'],
        ]);

        // Cast défensif (évite les soucis d’encodage)
        $name  = (string) ($data['name'] ?? '');
        $email = (string) ($data['email'] ?? '');
        $role  = (string) ($data['role'] ?? 'client');

        /** @var \App\Models\User $user */
        $user = User::create([
            'name'              => $name,
            'email'             => $email,
            'password'          => Hash::make($data['password']), // OK si pas de cast "hashed" sur User
            'role'              => $role,
            'email_verified_at' => null,
        ]);

        // Déclenche la notification de vérification si User implements MustVerifyEmail
        event(new Registered($user));

        return response()->json(
            [
                'message' => 'Inscription réussie. Vérifie ta boîte mail pour activer ton compte.',
                'user' => [
                    'id'    => (int) $user->id,
                    'name'  => (string) ($user->name ?? ''),
                    'email' => (string) $user->email,
                    'role'  => (string) ($user->role ?? 'client'),
                ],
            ],
            201,
            ['Content-Type' => 'application/json; charset=utf-8'],
            JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
        );
    }

    /**
     * POST /api/auth/login  → 204 si ok
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Normalise l'email (évite les échecs silencieux liés aux espaces/majuscules)
        $email = Str::lower(trim($credentials['email']));
        $pass  = $credentials['password'];

        // 1) Tentative standard sur le guard "web" (provider "users")
        if (Auth::guard('web')->attempt(['email' => $email, 'password' => $pass], $request->boolean('remember'))) {
            $request->session()->regenerate();

            /** @var \App\Models\User $user */
            $user = $request->user();

            if (! $user->hasVerifiedEmail()) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return response()->json(['message' => 'Veuillez vérifier votre email avant de vous connecter.'], 423);
            }

            return response()->noContent(); // 204
        }

        // 2) Fallback explicite : on vérifie manuellement le hash et on connecte
        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();
        if ($user && Hash::check($pass, $user->password)) {
            Auth::guard('web')->login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            if (! $user->hasVerifiedEmail()) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return response()->json(['message' => 'Veuillez vérifier votre email avant de vous connecter.'], 423);
            }

            return response()->noContent(); // 204
        }

        return response()->json(['message' => 'Identifiants invalides.'], 422);
    }

    /**
     * POST /api/auth/logout  → 204
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->noContent();
    }

    /**
     * GET /api/auth/me
     */
    public function me(Request $request)
    {
        $u = $request->user();

        return response()->json(
            [
                'id'    => (int) $u->id,
                'name'  => (string) ($u->name ?? ''),
                'email' => (string) $u->email,
                'role'  => (string) ($u->role ?? 'client'),
            ],
            200,
            ['Content-Type' => 'application/json; charset=utf-8'],
            JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
        );
    }
}
