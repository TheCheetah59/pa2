<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Collection;

class MenuController extends Controller
{
    public function index(): Collection
    {
        return Menu::all();
    }

    public function store(Request $request): Menu
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'is_active'   => 'boolean',
        ]);

        return Menu::create($validated);
    }

    public function show($id): Menu
    {
        return Menu::findOrFail($id);
    }

    public function update(Request $request, $id): Menu
    {
        $menu = Menu::findOrFail($id);

        $validated = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'sometimes|numeric|min:0',
            'is_active'   => 'boolean',
        ]);

        $menu->update($validated);
        return $menu;
    }

    public function destroy($id): Response
    {
        Menu::destroy($id);
        return response()->noContent();
    }



}
