<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;

class StripeService
{
    protected string $baseUrl = 'https://api.stripe.com/v1';

    protected function client()
    {
        return Http::withBasicAuth(config('services.stripe.secret'), '');
    }

    public function createOrRetrievePaymentIntent(Order $order): array
    {
        if ($order->payment_intent_id) {
            $existing = $this->retrievePaymentIntent($order->payment_intent_id);
            if (($existing['status'] ?? '') !== 'succeeded' && ($existing['status'] ?? '') !== 'canceled') {
                return $existing;
            }
        }

        $amount = (int) round($order->total_price * 100);

        $response = $this->client()->asForm()->post($this->baseUrl.'/payment_intents', [
            'amount' => $amount,
            'currency' => 'eur',
            'metadata[order_id]' => $order->id,
        ])->json();

        $order->update([
            'payment_intent_id' => $response['id'] ?? null,
            'payment_status' => Order::PAYMENT_PROCESSING,
        ]);

        return $response;
    }

    public function retrievePaymentIntent(string $id): array
    {
        return $this->client()->get($this->baseUrl.'/payment_intents/'.$id)->json();
    }
}