<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index()
    {
        return Warehouse::with('stockItems')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'postal_code' => 'required|string|max:10',  
            'city' => 'required|string|max:255',        
            'region' => 'required|string',
            'phone' => 'nullable|string|max:20',        
            'manager_name' => 'nullable|string|max:255', 
            'email' => 'nullable|email|max:255',        
            'capacity' => 'nullable|integer|min:0',     
            'kitchen_available' => 'boolean'            
        ]);

        return Warehouse::create($validated);
    }
    public function show($id)
    {
        return Warehouse::with('stockItems')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $warehouse = Warehouse::findOrFail($id);

        $validated = $request->validate([
            'name'    => 'sometimes|string|max:255',
            'address' => 'sometimes|string',
            'region'  => 'sometimes|string',
        ]);

        $warehouse->update($validated);
        return $warehouse;
    }

    public function destroy($id)
    {
        Warehouse::destroy($id);
        return response()->noContent();
    }
}

