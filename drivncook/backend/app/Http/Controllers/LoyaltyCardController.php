<?php

namespace App\Http\Controllers;

use App\Models\LoyaltyCard;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Collection;

class LoyaltyCardController extends Controller
{
    public function index(): Collection
    {
        return LoyaltyCard::with('customer')->get();
    }

    public function show($id): LoyaltyCard
    {
        return LoyaltyCard::with('customer')->findOrFail($id);
    }

    public function update(Request $request, $id): LoyaltyCard
    {
        $loyaltyCard = LoyaltyCard::findOrFail($id);

        $validated = $request->validate([
            'points'       => 'sometimes|integer|min:0',
            'last_update'  => 'sometimes|date',
        ]);

        $loyaltyCard->update($validated);

        return $loyaltyCard->load('customer');
    }
}
