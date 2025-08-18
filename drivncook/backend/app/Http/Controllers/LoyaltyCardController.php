<?php

namespace App\Http\Controllers;

use App\Models\LoyaltyCard;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class LoyaltyCardController extends Controller
{
    public function index(): Collection
    {
        return LoyaltyCard::with('customer')->get();
    }

    public function show(): ?LoyaltyCard
    {
        $customer = Auth::guard('customer')->user();
        return $customer?->loyaltyCard()->with('customer')->first();
    }

    public function update(Request $request): LoyaltyCard
    {
        $customer = Auth::guard('customer')->user();
        $loyaltyCard = $customer->loyaltyCard()->firstOrFail();

        $validated = $request->validate([
            'points'       => 'sometimes|integer|min:0',
            'last_update'  => 'sometimes|date',
        ]);

        $loyaltyCard->update($validated);

        return $loyaltyCard->load('customer');
    }
}
