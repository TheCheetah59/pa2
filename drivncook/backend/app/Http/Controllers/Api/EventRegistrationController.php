<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;

class EventRegistrationController extends Controller
{
    // Inscription d’un client à un événement
    public function register(Request $request, $eventId)
    {
        $customer = $request->user(); // supposé être un client authentifié via Sanctum
        $customer->events()->syncWithoutDetaching([$eventId]);

        return response()->json(['message' => 'Inscription effectuer.']);
    }

    //  Désinscription
    public function unregister(Request $request, $eventId)
    {
        $customer = $request->user();
        $customer->events()->detach($eventId);

        return response()->json(['message' => 'Désinscription effectuer.']);
    }

    //  Liste des événements du client connecté
    public function myEvents(Request $request)
    {
        /** @var \App\Models\Customer $customer */
        $customer = Auth::guard('customer')->user();

        if (!$customer) {
            return response()->json(['message' => 'Accès réservé aux clients'], 403);
        }

        return response()->json($customer->events);
    }

    // (optionnel) Liste des clients inscrits à un événement
    public function eventParticipants($eventId)
    {
        $event = Event::findOrFail($eventId);
        return response()->json($event->customers);
    }
}
