<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class EventRegistrationController extends Controller
{
    // POST /api/events/{event}/register
    public function register(Event $event)
    {
        $customer = Auth::guard('customer')->user();

        $event->customers()->syncWithoutDetaching([$customer->id]);

        return response()->json(['message' => 'Inscription confirmée']);
    }

    // DELETE /api/events/{event}/unregister
    public function unregister(Event $event)
    {
        $customer = Auth::guard('customer')->user();

        $event->customers()->detach($customer->id);

        return response()->json(['message' => 'Inscription annulée']);
    }

    // GET /api/my-events
    public function myEvents()
    {
        $customer = Auth::guard('customer')->user();

        return $customer->events()->with('pivot')->get();
    }

    // GET /api/events/{event}/participants
    public function eventParticipants(Event $event)
    {
        return $event->customers()->get();
    }
}
