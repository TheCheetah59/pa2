<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dish;

class DishController extends Controller
{
    // GET /api/dishes
    public function index()
    {
        return Dish::all();
    }

    // GET /api/dishes/{id}
    public function show($id)
    {
        return Dish::findOrFail($id);
    }

    // POST /api/dishes
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'category' => 'nullable|string|max:100',
        ]);

        $dish = Dish::create($validated);

        return response()->json($dish, 201);
    }

    // PUT /api/dishes/{id}
    public function update(Request $request, $id)
    {
        $dish = Dish::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'sometimes|numeric|min:0',
            'category' => 'nullable|string|max:100',
        ]);

        $dish->update($validated);

        return response()->json($dish);
    }

    // DELETE /api/dishes/{id}
    public function destroy($id)
    {
        $dish = Dish::findOrFail($id);
        $dish->delete();

        return response()->json(['message' => 'Plat supprimé'], 204);
    }
}
