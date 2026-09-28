<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Stripe\PaymentIntent;
use Stripe\Refund;
use Stripe\Exception\ApiErrorException;
use Illuminate\Support\Facades\Log;

class StripeService
{
    protected $stripeKey;
    protected $currency;

    public function __construct()
    {
        $this->stripeKey = config('stripe.secret');
        $this->currency = config('stripe.currency', 'usd');

        // Set Stripe API key
        Stripe::setApiKey($this->stripeKey);

        // Set API version if specified
        if ($apiVersion = config('stripe.api_version')) {
            Stripe::setApiVersion($apiVersion);
        }
    }

    /**
     * Create a Checkout Session for payment
     *
     * @param array $items Cart items (prices should already be in the target currency)
     * @param array $metadata Additional metadata
     * @param string $successUrl Success redirect URL
     * @param string $cancelUrl Cancel redirect URL
     * @param string|null $currency Currency code (e.g., 'usd', 'eur', 'sar')
     * @return StripeSession
     */
    public function createCheckoutSession(array $items, array $metadata = [], string $successUrl, string $cancelUrl, ?string $currency = null)
    {
        try {
            $lineItems = [];
            $useCurrency = strtolower($currency ?? $this->currency);

            foreach ($items as $item) {
                $productData = [
                    'name' => $item['name'],
                ];

                // Only add description if it's not empty
                if (!empty($item['description'])) {
                    $productData['description'] = $item['description'];
                }

                // Only add images if provided
                if (!empty($item['image'])) {
                    $productData['images'] = [$item['image']];
                }

                $lineItems[] = [
                    'price_data' => [
                        'currency' => $useCurrency,
                        'product_data' => $productData,
                        'unit_amount' => (int) round($item['price'] * 100), // Convert to cents
                    ],
                    'quantity' => $item['quantity'] ?? 1,
                ];
            }

            $sessionParams = [
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
                'metadata' => $metadata,
                'customer_email' => $metadata['customer_email'] ?? null,
            ];

            // Add billing address collection
            $sessionParams['billing_address_collection'] = 'required';

            // Add shipping address collection if needed
            if (isset($metadata['collect_shipping']) && $metadata['collect_shipping']) {
                $sessionParams['shipping_address_collection'] = [
                    'allowed_countries' => ['US', 'CA', 'GB', 'AU', 'SE', 'NO', 'DK', 'FI'], // Add more countries as needed
                ];
            }

            return StripeSession::create($sessionParams);
        } catch (ApiErrorException $e) {
            Log::error('Stripe Checkout Session creation failed', [
                'error' => $e->getMessage(),
                'items' => $items,
            ]);
            throw $e;
        }
    }

    /**
     * Create a Payment Intent
     *
     * @param float $amount
     * @param array $metadata
     * @return PaymentIntent
     */
    public function createPaymentIntent(float $amount, array $metadata = [])
    {
        try {
            return PaymentIntent::create([
                'amount' => (int) ($amount * 100), // Convert to cents
                'currency' => $this->currency,
                'metadata' => $metadata,
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);
        } catch (ApiErrorException $e) {
            Log::error('Stripe Payment Intent creation failed', [
                'error' => $e->getMessage(),
                'amount' => $amount,
            ]);
            throw $e;
        }
    }

    /**
     * Retrieve a Checkout Session
     *
     * @param string $sessionId
     * @return StripeSession
     */
    public function retrieveSession(string $sessionId)
    {
        try {
            return StripeSession::retrieve($sessionId);
        } catch (ApiErrorException $e) {
            Log::error('Stripe Session retrieval failed', [
                'error' => $e->getMessage(),
                'session_id' => $sessionId,
            ]);
            throw $e;
        }
    }

    /**
     * Retrieve a Payment Intent
     *
     * @param string $paymentIntentId
     * @return PaymentIntent
     */
    public function retrievePaymentIntent(string $paymentIntentId)
    {
        try {
            return PaymentIntent::retrieve($paymentIntentId);
        } catch (ApiErrorException $e) {
            Log::error('Stripe Payment Intent retrieval failed', [
                'error' => $e->getMessage(),
                'payment_intent_id' => $paymentIntentId,
            ]);
            throw $e;
        }
    }

    /**
     * Create a refund
     *
     * @param string $paymentIntentId
     * @param float|null $amount Optional partial refund amount
     * @param array $metadata
     * @return Refund
     */
    public function createRefund(string $paymentIntentId, ?float $amount = null, array $metadata = [])
    {
        try {
            $refundData = [
                'payment_intent' => $paymentIntentId,
                'metadata' => $metadata,
            ];

            if ($amount !== null) {
                $refundData['amount'] = (int) ($amount * 100); // Convert to cents
            }

            return Refund::create($refundData);
        } catch (ApiErrorException $e) {
            Log::error('Stripe Refund creation failed', [
                'error' => $e->getMessage(),
                'payment_intent_id' => $paymentIntentId,
            ]);
            throw $e;
        }
    }

    /**
     * Verify webhook signature
     *
     * @param string $payload
     * @param string $signature
     * @return \Stripe\Event
     */
    public function constructWebhookEvent(string $payload, string $signature)
    {
        $webhookSecret = config('stripe.webhook_secret');

        try {
            return \Stripe\Webhook::constructEvent(
                $payload,
                $signature,
                $webhookSecret
            );
        } catch (\UnexpectedValueException $e) {
            Log::error('Invalid webhook payload', ['error' => $e->getMessage()]);
            throw $e;
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Invalid webhook signature', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Format amount for display
     *
     * @param int $amountInCents
     * @return float
     */
    public function formatAmount(int $amountInCents): float
    {
        return $amountInCents / 100;
    }

    /**
     * Get currency symbol
     *
     * @return string
     */
    public function getCurrencySymbol(): string
    {
        $symbols = [
            'usd' => '$',
            'eur' => '€',
            'gbp' => '£',
            'sek' => 'kr',
            'nok' => 'kr',
            'dkk' => 'kr',
        ];

        return $symbols[$this->currency] ?? $this->currency;
    }
}
