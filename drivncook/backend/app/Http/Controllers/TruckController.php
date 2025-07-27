<?php

namespace App\Http\Controllers;

use App\Models\Truck;
use Illuminate\Http\Request;

class TruckController extends Controller
{
    public function index()
    {
        return Truck::with('franchisee')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'franchisee_id' => 'required|exists:franchisees,id',
            'registration'  => 'required|string|unique:trucks,registration',
            'status'        => 'required|in:ok,en_panne,en_maintenance',
            'location'      => 'nullable|string',
        ]);

        return Truck::create($validated);
    }

    public function show($id)
    {
        return Truck::with('franchisee', 'maintenances')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $truck = Truck::findOrFail($id);

        $validated = $request->validate([
            'franchisee_id' => 'sometimes|exists:franchisees,id',
            'registration'  => 'sometimes|string|unique:trucks,registration,' . $id,
            'status'        => 'sometimes|in:ok,en_panne,en_maintenance',
            'location'      => 'sometimes|nullable|string',
        ]);

        $truck->update($validated);
        return $truck;
    }

    public function destroy($id)
    {
        $truck = Truck::findOrFail($id);
        $truck->delete();
        return response()->noContent();
    }
}