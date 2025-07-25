<?php

namespace App\Http\Controllers;

use App\Models\Franchisee;
use Illuminate\Http\Request;

class FranchiseeController extends Controller
{
    public function index() { return Franchisee::all(); }

    public function store(Request $request) {
        $validated = $request->validate([]);
        return Franchisee::create($validated);
    }

    public function show($id) { return Franchisee::findOrFail($id); }

    public function update(Request $request, $id) {
        $item = Franchisee::findOrFail($id);
        $item->update($request->all());
        return $item;
    }

    public function destroy($id) {
        Franchisee::destroy($id);
        return response()->noContent();
    }
}

