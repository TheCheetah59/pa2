<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    // POST /api/contact
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string',
        ]);

        Log::info("Contact message from {$validated['email']} ({$validated['name']}): {$validated['message']}");

        return response()->json(['message' => 'Message reçu'], 200);
    }
}