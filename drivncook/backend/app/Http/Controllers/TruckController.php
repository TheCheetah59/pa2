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
            'plate_number' => 'required|string|unique:trucks,plate_number',  // ← Corrigé
            'model' => 'required|string',                                    // ← Ajouté
            'current_location' => 'nullable|string',                         // ← Corrigé
            'status' => 'required|in:ok,en_panne,en_maintenance',
            'last_service_date' => 'nullable|date',                         // ← Ajouté
            'next_service_due' => 'nullable|date',                          // ← Ajouté
            'notes' => 'nullable|string',                                   // ← Ajouté
        ]);

        $truck = Truck::create($validated);
        return response()->json($truck, 201); 
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
            'plate_number' => 'sometimes|string|unique:trucks,plate_number,' . $id,  
            'model' => 'sometimes|string',                                          
            'current_location' => 'sometimes|nullable|string',                       
            'status' => 'sometimes|in:ok,en_panne,en_maintenance',
            'last_service_date' => 'sometimes|nullable|date',                       
            'next_service_due' => 'sometimes|nullable|date',                        
            'notes' => 'sometimes|nullable|string',                                 
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