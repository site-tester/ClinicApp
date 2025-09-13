<?php

namespace App\Services;

use App\Models\Payment;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayPalService
{
    private $clientId;
    private $clientSecret;
    private $baseUrl;
    private $accessToken;

    public function __construct()
    {
        $this->clientId = config('services.paypal.client_id');
        $this->clientSecret = config('services.paypal.client_secret');
        $this->baseUrl = config('services.paypal.sandbox')
            ? 'https://api.sandbox.paypal.com'
            : 'https://api.paypal.com';
    }

    /**
     * Get PayPal access token
     */
    private function getAccessToken(): string
    {
        if ($this->accessToken) {
            return $this->accessToken;
        }

        try {
            $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
                ->withHeaders(['Accept' => 'application/json'])
                ->asForm()
                ->post($this->baseUrl . '/v1/oauth2/token', [
                    'grant_type' => 'client_credentials'
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->accessToken = $data['access_token'];
                return $this->accessToken;
            }

            throw new Exception('Failed to get PayPal access token: ' . $response->body());
        } catch (Exception $e) {
            Log::error('PayPal access token error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create PayPal order
     */
    public function createOrder(Payment $payment): array
    {
        try {
            $accessToken = $this->getAccessToken();

            $orderData = [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'reference_id' => $payment->payment_reference,
                        'amount' => [
                            'currency_code' => $payment->currency,
                            'value' => number_format($payment->amount, 2, '.', '')
                        ],
                        'description' => $payment->description ?: 'FRYDT Clinic Payment',
                        'items' => $this->buildOrderItems($payment)
                    ]
                ],
                'application_context' => [
                    'return_url' => route('payment.success'),
                    'cancel_url' => route('payment.cancel'),
                    'brand_name' => 'FRYDT Lying-In Clinic',
                    'landing_page' => 'BILLING',
                    'user_action' => 'PAY_NOW'
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/v2/checkout/orders', $orderData);

            if ($response->successful()) {
                $orderResponse = $response->json();

                // Update payment with PayPal order ID
                $payment->update([
                    'paypal_order_id' => $orderResponse['id'],
                    'paypal_response' => $orderResponse
                ]);

                return $orderResponse;
            }

            throw new Exception('Failed to create PayPal order: ' . $response->body());
        } catch (Exception $e) {
            Log::error('PayPal create order error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Capture PayPal order
     */
    public function captureOrder(string $orderId): array
    {
        try {
            $accessToken = $this->getAccessToken();

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . "/v2/checkout/orders/{$orderId}/capture");

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Failed to capture PayPal order: ' . $response->body());
        } catch (Exception $e) {
            Log::error('PayPal capture order error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get order details
     */
    public function getOrderDetails(string $orderId): array
    {
        try {
            $accessToken = $this->getAccessToken();

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->get($this->baseUrl . "/v2/checkout/orders/{$orderId}");

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Failed to get PayPal order details: ' . $response->body());
        } catch (Exception $e) {
            Log::error('PayPal get order details error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Build order items for PayPal
     */
    private function buildOrderItems(Payment $payment): array
    {
        $items = [];
        $totalItemsValue = 0;

        foreach ($payment->items as $item) {
            $itemValue = number_format($item->unit_price, 2, '.', '');
            $totalItemsValue += $item->unit_price * $item->quantity;

            $items[] = [
                'name' => $item->service_name,
                'unit_amount' => [
                    'currency_code' => $payment->currency,
                    'value' => $itemValue
                ],
                'quantity' => (string) $item->quantity,
                'category' => 'DIGITAL_GOODS'
            ];
        }

        // Ensure total items value matches payment amount
        if (abs($totalItemsValue - $payment->amount) > 0.01) {
            Log::warning("PayPal order items total ({$totalItemsValue}) doesn't match payment amount ({$payment->amount})");
        }

        return $items;
    }
}
