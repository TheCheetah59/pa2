<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of users pending activation.
     */
    public function index()
    {
        return User::where('is_activated', false)->get();
    }

    /**
     * Activate the given user account.
     */
    public function activate(User $user)
    {
        $user->is_activated = true;
        $user->save();

        return response()->json(['message' => 'User activated successfully', 'user' => $user]);
    }

    /**
     * Suspend the given user account.
     */
    public function suspend(User $user)
    {
        $user->is_activated = false;
        $user->save();

        return response()->json(['message' => 'User suspended successfully', 'user' => $user]);
    }
}