<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\StripeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class PaymentController extends Controller
{
    public function __construct(private StripeService $stripe)
    {
    }

    public function paymentIntent(Order $order): JsonResponse
    {
        Gate::authorize('pay-order', $order);

        if ($order->payment_status === Order::PAYMENT_PAID) {
            return response()->json(['message' => 'Commande déjà payée'], 409);
        }

        $intent = $this->stripe->createOrRetrievePaymentIntent($order);

        return response()->json(['client_secret' => $intent['client_secret']]);
    }

    public function confirmPayment(Order $order): JsonResponse
    {
        Gate::authorize('pay-order', $order);

        if ($order->payment_status === Order::PAYMENT_PAID || !$order->payment_intent_id) {
            return response()->json(['message' => 'Action non valide'], 409);
        }

        $intent = $this->stripe->retrievePaymentIntent($order->payment_intent_id);
        $amount = (int) round($order->total_price * 100);

        if (($intent['amount'] ?? 0) !== $amount || ($intent['currency'] ?? '') !== 'eur') {
            return response()->json(['message' => 'Montant invalide'], 409);
        }

        if (($intent['status'] ?? '') === 'succeeded') {
            $order->update(['payment_status' => Order::PAYMENT_PAID]);
            return response()->json(['status' => 'paid']);
        }

        return response()->json(['message' => 'Paiement non confirmé'], 409);
    }
}