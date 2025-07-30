<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Log;

class NewsletterController extends Controller
{
    // POST /api/newsletters/send
    public function send()
    {
        // Simuler l’envoi à tous les clients
        $customers = Customer::all();

        foreach ($customers as $customer) {
            Log::info("Newsletter envoyée à : {$customer->email}");
            // Tu pourrais ici appeler un vrai service d’emailing
        }

        return response()->json(['message' => 'Newsletters envoyées (simulation)'], 200);
    }
}
