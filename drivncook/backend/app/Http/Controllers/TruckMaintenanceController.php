<?php

namespace App\Http\Controllers;

use App\Models\TruckMaintenance;
use Illuminate\Http\Request;

class TruckMaintenanceController extends Controller
{
    public function index()
    {
        return TruckMaintenance::with('truck')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'truck_id'   => 'required|exists:trucks,id',
            'date'       => 'required|date',
            'description'=> 'required|string',
        ]);

        return TruckMaintenance::create($validated);
    }

    public function show($id)
    {
        return TruckMaintenance::with('truck')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $maintenance = TruckMaintenance::findOrFail($id);

        $validated = $request->validate([
            'truck_id'   => 'sometimes|exists:trucks,id',
            'date'       => 'sometimes|date',
            'description'=> 'sometimes|string',
        ]);

        $maintenance->update($validated);
        return $maintenance;
    }

    public function destroy($id)
    {
        TruckMaintenance::destroy($id);
        return response()->noContent();
    }
}
