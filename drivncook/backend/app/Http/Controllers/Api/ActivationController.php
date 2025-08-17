<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;

class ActivationController extends Controller
{
    public function index(string $token)
    {
        $user = User::where('activation_token', $token)
            ->where('activation_token_expires_at', '>', now())
            ->first();

        if (!$user) {
            return response()->json(['message' => 'Token non valide ou expiré'], 404);
        }

        $user->is_activated = true;
        $user->activation_token = null;
        $user->activation_token_expires_at = null;
        $user->save();

        return response()->json(['message' => 'Account activated successfully']);
    }
}