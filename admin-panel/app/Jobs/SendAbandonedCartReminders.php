<?php

namespace App\Jobs;

use App\Mail\AbandonedCartReminder;
use App\Models\AbandonedCart;
use App\Services\AbandonedCartService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\EmailLog;

class SendAbandonedCartReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle()
    {
        Log::info('Starting abandoned cart reminder job');

        $abandonedCartService = new AbandonedCartService();

        // Get all unconverted abandoned carts
        $abandonedCarts = AbandonedCart::where('converted', false)
            ->with(['student', 'items.product'])
            ->get();

        foreach ($abandonedCarts as $cart) {
            try {
                // First reminder - 1 hour after abandonment
                if ($cart->shouldSendFirstReminder()) {
                    Mail::to($cart->student->email)->send(
                        new AbandonedCartReminder($cart, 'first')
                    );
                    $cart->update(['first_reminder_sent_at' => now()]);
                    Log::info("First reminder sent for abandoned cart ID: {$cart->id}");

                    EmailLog::logSent(
                        $cart->student->email,
                        'abandoned_cart',
                        'Abandoned Cart Reminder (1st)',
                        $cart->student->id,
                        ($cart->student->first_name ?? '') . ' ' . ($cart->student->last_name ?? ''),
                        'AbandonedCart',
                        $cart->id
                    );
                }

                // Second reminder - 24 hours after abandonment
                if ($cart->shouldSendSecondReminder()) {
                    Mail::to($cart->student->email)->send(
                        new AbandonedCartReminder($cart, 'second')
                    );
                    $cart->update(['second_reminder_sent_at' => now()]);
                    Log::info("Second reminder sent for abandoned cart ID: {$cart->id}");

                    EmailLog::logSent(
                        $cart->student->email,
                        'abandoned_cart',
                        'Abandoned Cart Reminder (2nd)',
                        $cart->student->id,
                        ($cart->student->first_name ?? '') . ' ' . ($cart->student->last_name ?? ''),
                        'AbandonedCart',
                        $cart->id
                    );
                }

                // Third reminder - 3 days after abandonment (with discount)
                if ($cart->shouldSendThirdReminder()) {
                    // Use the standard COMEBACK10 coupon (created by carts:ensure-coupon command)
                    $discountCoupon = 'COMEBACK10';

                    Mail::to($cart->student->email)->send(
                        new AbandonedCartReminder($cart, 'third', $discountCoupon)
                    );

                    $cart->update([
                        'third_reminder_sent_at' => now(),
                        'discount_coupon' => $discountCoupon,
                    ]);

                    Log::info("Third reminder sent with discount coupon {$discountCoupon} for abandoned cart ID: {$cart->id}");

                    EmailLog::logSent(
                        $cart->student->email,
                        'abandoned_cart',
                        'Abandoned Cart Reminder (3rd - Discount)',
                        $cart->student->id,
                        ($cart->student->first_name ?? '') . ' ' . ($cart->student->last_name ?? ''),
                        'AbandonedCart',
                        $cart->id
                    );
                }
            } catch (\Exception $e) {
                Log::error("Error sending reminder for cart ID {$cart->id}: " . $e->getMessage());

                EmailLog::logFailed(
                    $cart->student->email ?? 'unknown',
                    'abandoned_cart',
                    'Abandoned Cart Reminder',
                    $e->getMessage(),
                    $cart->student->id ?? null,
                    ($cart->student->first_name ?? '') . ' ' . ($cart->student->last_name ?? ''),
                    'AbandonedCart',
                    $cart->id
                );
            }
        }

        Log::info('Abandoned cart reminder job completed');
    }
}
