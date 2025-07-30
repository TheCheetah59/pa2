<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    // GET /api/events
    public function index()
    {
        return Event::latest()->get();
    }

    // GET /api/events/{id}
    public function show($id)
    {
        return Event::findOrFail($id);
    }

    // POST /api/events
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'location' => 'nullable|string|max:255',
        ]);

        $event = Event::create($validated);

        return response()->json($event, 201);
    }

    // PUT /api/events/{id}
    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:1000',
            'start_date' => 'sometimes|date|after_or_equal:today',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
            'location' => 'nullable|string|max:255',
        ]);

        $event->update($validated);

        return response()->json($event);
    }

    // DELETE /api/events/{id}
    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $event->delete();

        return response()->json(['message' => 'Événement supprimé'], 204);
    }
}
