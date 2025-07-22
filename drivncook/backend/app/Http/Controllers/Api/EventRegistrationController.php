<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Customer;

class EventRegistrationController extends Controller
{
    // ✅ Inscription d’un client à un événement
    public function register(Request $request, $eventId)
    {
        $customer = $request->user(); // supposé être un client authentifié via Sanctum
        $customer->events()->syncWithoutDetaching([$eventId]);

        return response()->json(['message' => 'Inscription enregistrée.']);
    }

    // ❌ Désinscription
    public function unregister(Request $request, $eventId)
    {
        $customer = $request->user();
        $customer->events()->detach($eventId);

        return response()->json(['message' => 'Désinscription effectuée.']);
    }

    // 👀 Liste des événements du client connecté
    public function myEvents(Request $request)
    {
        $customer = $request->user();
        return response()->json($customer->events()->get());
    }

    // (optionnel) Liste des clients inscrits à un événement
    public function eventParticipants($eventId)
    {
        $event = Event::findOrFail($eventId);
        return response()->json($event->customers);
    }
}
