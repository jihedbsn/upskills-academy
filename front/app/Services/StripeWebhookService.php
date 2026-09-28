<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderSpecifique;
use App\Models\Installment;
use App\Models\ProductStudent;
use App\Services\StripeService;
use App\Services\InstallmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripeWebhookService
{
    protected $stripeService;
    protected $installmentService;

    public function __construct(StripeService $stripeService, InstallmentService $installmentService)
    {
        $this->stripeService = $stripeService;
        $this->installmentService = $installmentService;
    }

    private function extractMonthsFromPeriod(?string $period): int
    {
        if (empty($period)) {
            return 0;
        }

        $normalized = mb_strtolower(trim($period));
        $hasMonthUnit = preg_match('/month|months|mois|شهر|أشهر/u', $normalized) === 1;

        if (!$hasMonthUnit) {
            return 0;
        }

        if (!preg_match('/(\d+)/', $normalized, $matches)) {
            return 0;
        }

        return (int) $matches[1];
    }

    private function resolveCourseExpirationDate($product)
    {
        if (!$product) {
            return null;
        }

        $validityMonths = (int) ($product->validity_months ?? 0);

        if ($validityMonths < 1) {
            $validityMonths = $this->extractMonthsFromPeriod($product->period ?? null);
        }

        if ($validityMonths < 1) {
            return null;
        }

        return now()->addMonths($validityMonths);
    }

    /**
     * Handle Stripe webhook events
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        try {
            $event = $this->stripeService->constructWebhookEvent($payload, $signature);
        } catch (\Exception $e) {
            Log::error('Webhook signature verification failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Webhook signature verification failed'], 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handleCheckoutSessionCompleted($event->data->object);
                break;

            case 'payment_intent.succeeded':
                $this->handlePaymentIntentSucceeded($event->data->object);
                break;

            case 'payment_intent.payment_failed':
                $this->handlePaymentIntentFailed($event->data->object);
                break;

            case 'charge.refunded':
                $this->handleChargeRefunded($event->data->object);
                break;

            case 'charge.dispute.created':
                $this->handleDisputeCreated($event->data->object);
                break;

            default:
                Log::info('Unhandled webhook event type: ' . $event->type);
        }

        return response()->json(['status' => 'success'], 200);
    }

    /**
     * Handle checkout.session.completed event
     */
    protected function handleCheckoutSessionCompleted($session)
    {
        Log::info('Checkout session completed', [
            'session_id' => $session->id,
            'payment_status' => $session->payment_status,
            'amount_total' => $session->amount_total,
            'customer_email' => $session->customer_email,
        ]);

        // Check if this is an installment payment
        if (isset($session->metadata->type) && $session->metadata->type === 'installment_payment') {
            $this->handleInstallmentPayment($session);
            return;
        }

        // Update orders with session information and grant course access
        if (isset($session->metadata->student_id)) {
            $orders = Order::where('student_id', $session->metadata->student_id)
                ->where('stripe_session_id', $session->id)
                ->get();

            foreach ($orders as $order) {
                /** @var \App\Models\Order $order */
                // Update order
                $order->update([
                    'payment_success' => true,
                    'stripe_payment_intent' => $session->payment_intent,
                ]);

                // Grant course access if it's a course purchase
                if ($order->product_id) {
                    $this->grantCourseAccess($order->student_id, $order->product_id);
                }
            }
        }
    }

    /**
     * Handle installment payment from Stripe webhook
     */
    protected function handleInstallmentPayment($session)
    {
        $installmentId = $session->metadata->installment_id ?? null;
        $orderSpecifiqueId = $session->metadata->order_specifique_id ?? null;
        $studentId = $session->metadata->student_id ?? null;

        Log::info('Processing installment payment via webhook', [
            'session_id' => $session->id,
            'installment_id' => $installmentId,
            'order_specifique_id' => $orderSpecifiqueId,
            'student_id' => $studentId,
        ]);

        if (!$installmentId || !$orderSpecifiqueId) {
            Log::error('Missing installment metadata in webhook', [
                'session_id' => $session->id,
                'metadata' => (array) $session->metadata,
            ]);
            return;
        }

        try {
            $installment = Installment::find($installmentId);
            $orderSpecifique = OrderSpecifique::find($orderSpecifiqueId);

            if (!$installment || !$orderSpecifique) {
                Log::error('Installment or OrderSpecifique not found', [
                    'installment_id' => $installmentId,
                    'order_specifique_id' => $orderSpecifiqueId,
                ]);
                return;
            }

            // Skip if already paid (idempotency)
            if ($installment->status === 'paid') {
                Log::info('Installment already paid, skipping', ['installment_id' => $installmentId]);
                return;
            }

            // Process the installment payment via service
            $this->installmentService->processFirstInstallment($orderSpecifique, $installment);

            Log::info('Installment payment processed successfully via webhook', [
                'installment_id' => $installmentId,
                'order_specifique_id' => $orderSpecifiqueId,
                'student_id' => $studentId,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to process installment payment via webhook', [
                'session_id' => $session->id,
                'installment_id' => $installmentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Handle payment_intent.succeeded event
     */
    protected function handlePaymentIntentSucceeded($paymentIntent)
    {
        Log::info('Payment intent succeeded', [
            'payment_intent_id' => $paymentIntent->id,
            'amount' => $paymentIntent->amount,
            'currency' => $paymentIntent->currency,
        ]);

        // Update orders with payment intent and grant course access
        $orders = Order::where('stripe_payment_intent', $paymentIntent->id)->get();

        foreach ($orders as $order) {
            /** @var \App\Models\Order $order */
            $order->update(['payment_success' => true]);

            // Grant course access if it's a course purchase
            if ($order->product_id) {
                $this->grantCourseAccess($order->student_id, $order->product_id);
            }
        }
    }

    /**
     * Grant course access to student
     */
    protected function grantCourseAccess($studentId, $productId)
    {
        $product = \App\Models\Product::find($productId);
        $expirationDate = $this->resolveCourseExpirationDate($product);

        // Check if access already exists
        $existing = ProductStudent::where('student_id', $studentId)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            // Reactivate if exists
            $existing->update([
                'is_active' => true,
                'access_granted_at' => now(),
                'expiration_date' => $expirationDate,
                'is_expired' => false,
            ]);

            Log::info('Course access reactivated', [
                'student_id' => $studentId,
                'product_id' => $productId,
            ]);
        } else {
            // Create new access
            ProductStudent::create([
                'student_id' => $studentId,
                'product_id' => $productId,
                'date' => now(),
                'is_active' => true,
                'access_granted_at' => now(),
                'expiration_date' => $expirationDate,
                'is_expired' => false,
            ]);

            Log::info('Course access granted', [
                'student_id' => $studentId,
                'product_id' => $productId,
            ]);
        }
    }

    /**
     * Handle payment_intent.payment_failed event
     */
    protected function handlePaymentIntentFailed($paymentIntent)
    {
        Log::warning('Payment intent failed', [
            'payment_intent_id' => $paymentIntent->id,
            'amount' => $paymentIntent->amount,
            'last_payment_error' => $paymentIntent->last_payment_error,
        ]);

        // Mark orders as payment failed
        Order::where('stripe_payment_intent', $paymentIntent->id)
            ->update(['payment_success' => false]);

        // Send notification to admin
        // TODO: Implement admin notification
    }

    /**
     * Handle charge.refunded event
     */
    protected function handleChargeRefunded($charge)
    {
        Log::info('Charge refunded', [
            'charge_id' => $charge->id,
            'amount_refunded' => $charge->amount_refunded,
            'payment_intent' => $charge->payment_intent,
        ]);

        // Find orders and mark as refunded
        $orders = Order::where('stripe_payment_intent', $charge->payment_intent)->get();

        foreach ($orders as $order) {
            /** @var \App\Models\Order $order */
            $order->update([
                'payment_success' => false,
                'notes' => ($order->notes ?? '') . "\n\nRefunded: " . date('Y-m-d H:i:s'),
            ]);

            // Log refund
            Log::info('Order refunded', [
                'order_id' => $order->id,
                'student_id' => $order->student_id,
                'amount' => $order->price,
            ]);
        }

        // Send notification to student
        // TODO: Implement student notification
    }

    /**
     * Handle charge.dispute.created event
     */
    protected function handleDisputeCreated($dispute)
    {
        Log::warning('Dispute created', [
            'dispute_id' => $dispute->id,
            'charge_id' => $dispute->charge,
            'amount' => $dispute->amount,
            'reason' => $dispute->reason,
        ]);

        // Send notification to admin
        // TODO: Implement admin notification about dispute
    }
}
