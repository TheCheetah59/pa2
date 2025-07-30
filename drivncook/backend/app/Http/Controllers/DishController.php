<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dish;

class DishController extends Controller
{
    // GET /api/dishes?lang=fr
    public function index(Request $request)
    {
        $lang = $request->query('lang', 'fr');

        $dishes = Dish::where('available', true)->get()->map(function ($dish) use ($lang) {
            return [
                'id' => $dish->id,
                'name' => $dish->getTranslated('name', $lang),
                'description' => $dish->getTranslated('description', $lang),
                'price' => $dish->price,
                'image_url' => $dish->image_url,
            ];
        });

        return response()->json($dishes);
    }

    // GET /api/dishes/{id}?lang=fr
    public function show(Request $request, $id)
    {
        $dish = Dish::findOrFail($id);
        $lang = $request->query('lang', 'fr');

        return response()->json([
            'id' => $dish->id,
            'name' => $dish->getTranslated('name', $lang),
            'description' => $dish->getTranslated('description', $lang),
            'price' => $dish->price,
            'image_url' => $dish->image_url,
        ]);
    }

    // POST /api/dishes (admin)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'name_fr' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description_fr' => 'nullable|string|max:1000',
            'description_en' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'image_url' => 'nullable|string',
            'available' => 'boolean',
        ]);

        $dish = Dish::create($validated);

        return response()->json($dish, 201);
    }

    // PUT /api/dishes/{id} (admin)
    public function update(Request $request, $id)
    {
        $dish = Dish::findOrFail($id);

        $validated = $request->validate([
            'menu_id' => 'sometimes|exists:menus,id',
            'name_fr' => 'sometimes|string|max:255',
            'name_en' => 'sometimes|string|max:255',
            'description_fr' => 'nullable|string|max:1000',
            'description_en' => 'nullable|string|max:1000',
            'price' => 'sometimes|numeric|min:0',
            'image_url' => 'nullable|string',
            'available' => 'boolean',
        ]);

        $dish->update($validated);

        return response()->json($dish);
    }

    // DELETE /api/dishes/{id} (admin)
    public function destroy($id)
    {
        $dish = Dish::findOrFail($id);
        $dish->delete();

        return response()->json(['message' => 'Plat supprimé avec succès.'], 200);
    }
}
