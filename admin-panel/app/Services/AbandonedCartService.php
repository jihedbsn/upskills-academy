<?php

namespace App\Services;

use App\Models\AbandonedCart;
use App\Models\AbandonedCartItem;
use App\Models\Cart;
use App\Models\EmailLog;
use App\Mail\AbandonedCartReminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AbandonedCartService
{
    public function index(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->subDays(90)->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        $stats = $this->getStatistics($dateFrom . ' 00:00:00', $dateTo . ' 23:59:59');

        $query = AbandonedCart::with(['student', 'items.product'])
            ->whereBetween('abandoned_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->orderBy('abandoned_at', 'desc');

        if ($request->input('status') === 'converted') {
            $query->where('converted', true);
        } elseif ($request->input('status') === 'not_converted') {
            $query->where('converted', false);
        }

        $abandonedCarts = $query->paginate(20);
        $chartData = $this->getChartData();

        return view('abandoned-carts.index', compact(
            'abandonedCarts',
            'stats',
            'dateFrom',
            'dateTo',
            'chartData'
        ));
    }

    public function show($id)
    {
        $abandonedCart = AbandonedCart::with(['student', 'items.product'])
            ->findOrFail($id);

        return view('abandoned-carts.show', compact('abandonedCart'));
    }

    private function getChartData()
    {
        $data = AbandonedCart::select(
            DB::raw('DATE(abandoned_at) as date'),
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN converted = 1 THEN 1 ELSE 0 END) as converted')
        )
        ->where('abandoned_at', '>=', now()->subDays(30))
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        return [
            'labels' => $data->pluck('date')->map(function ($date) {
                return date('M d', strtotime($date));
            }),
            'abandoned' => $data->pluck('total'),
            'converted' => $data->pluck('converted'),
        ];
    }

    public function export(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        $abandonedCarts = AbandonedCart::with(['student', 'items.product'])
            ->whereBetween('abandoned_at', [$dateFrom, $dateTo])
            ->orderBy('abandoned_at', 'desc')
            ->get();

        $csv = "Student Name,Email,Abandoned At,Items Count,Total Amount,Status,First Reminder,Second Reminder,Third Reminder,Converted At\n";

        foreach ($abandonedCarts as $cart) {
            $csv .= sprintf(
                '"%s","%s","%s",%d,$%.2f,"%s","%s","%s","%s","%s"' . "\n",
                $cart->student->first_name . ' ' . $cart->student->last_name,
                $cart->student->email,
                $cart->abandoned_at->format('Y-m-d H:i'),
                $cart->items_count,
                $cart->total_amount,
                $cart->converted ? 'Converted' : 'Not Converted',
                $cart->first_reminder_sent_at ? $cart->first_reminder_sent_at->format('Y-m-d H:i') : 'Not sent',
                $cart->second_reminder_sent_at ? $cart->second_reminder_sent_at->format('Y-m-d H:i') : 'Not sent',
                $cart->third_reminder_sent_at ? $cart->third_reminder_sent_at->format('Y-m-d H:i') : 'Not sent',
                $cart->converted_at ? $cart->converted_at->format('Y-m-d H:i') : 'N/A'
            );
        }

        $fileName = 'abandoned-carts-' . date('Y-m-d') . '.csv';

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$fileName}\"");
    }

    public function sendReminder($id)
    {
        $cart = AbandonedCart::with(['student', 'items.product'])->findOrFail($id);

        if ($cart->converted) {
            return back()->with('error', 'Cannot send reminder for converted cart.');
        }

        try {
            $reminderType = 'first';
            $discountCoupon = null;

            if (is_null($cart->first_reminder_sent_at)) {
                $reminderType = 'first';
            } elseif (is_null($cart->second_reminder_sent_at)) {
                $reminderType = 'second';
            } elseif (is_null($cart->third_reminder_sent_at)) {
                $reminderType = 'third';
                $discountCoupon = $this->generateDiscountCoupon();
            } else {
                return back()->with('info', 'All reminders have already been sent for this cart.');
            }

            Mail::to($cart->student->email)->send(
                new AbandonedCartReminder($cart, $reminderType, $discountCoupon)
            );

            EmailLog::logSent(
                $cart->student->email,
                'abandoned_cart',
                'Abandoned Cart Reminder (' . $reminderType . ')',
                $cart->student->id,
                ($cart->student->first_name ?? '') . ' ' . ($cart->student->last_name ?? ''),
                'AbandonedCart',
                $cart->id
            );

            if ($reminderType === 'first') {
                $cart->update(['first_reminder_sent_at' => now()]);
            } elseif ($reminderType === 'second') {
                $cart->update(['second_reminder_sent_at' => now()]);
            } else {
                $cart->update([
                    'third_reminder_sent_at' => now(),
                    'discount_coupon' => $discountCoupon,
                ]);
            }

            return back()->with('success', ucfirst($reminderType) . ' reminder sent successfully!');
        } catch (\Exception $e) {
            Log::error('Error sending manual reminder: ' . $e->getMessage());
            return back()->with('error', 'Failed to send reminder: ' . $e->getMessage());
        }
    }

    /**
     * Track abandoned carts from logged-in users who have items in cart.
     * This should be called periodically (e.g., via a scheduled job).
     */
    public function trackAbandonedCarts()
    {
        try {
            // Find all carts that are older than 1 hour
            $oldCarts = Cart::where('created_at', '<', now()->subHour())
                ->select('student_id')
                ->distinct()
                ->pluck('student_id');

            foreach ($oldCarts as $studentId) {
                Log::info("Processing student ID: {$studentId}");

                // Check if we already have an unconverted abandoned cart for this student
                $existingAbandonedCart = AbandonedCart::where('student_id', $studentId)
                    ->where('converted', false)
                    ->first();

                // If already tracked, skip
                if ($existingAbandonedCart) {
                    Log::info("Skipping student {$studentId}: already has unconverted cart ID {$existingAbandonedCart->id}");
                    continue;
                }

                // Get all cart items for this student
                $cartItems = Cart::where('student_id', $studentId)
                    ->where('created_at', '<', now()->subHour())
                    ->get();

                if ($cartItems->isEmpty()) {
                    Log::info("Skipping student {$studentId}: no old cart items found");
                    continue;
                }

                Log::info("Student {$studentId}: found {$cartItems->count()} cart items");

                // Calculate total amount from cart items (price is already stored in cart)
                $totalAmount = $cartItems->sum('price');
                Log::info("Student {$studentId}: total amount = {$totalAmount}");

                DB::beginTransaction();
                try {
                    $abandonedCart = AbandonedCart::create([
                        'student_id' => $studentId,
                        'total_amount' => $totalAmount,
                        'items_count' => $cartItems->count(),
                        'abandoned_at' => $cartItems->first()->created_at,
                    ]);

                    // Save cart items
                    foreach ($cartItems as $item) {
                        AbandonedCartItem::create([
                            'abandoned_cart_id' => $abandonedCart->id,
                            'product_id' => $item->product_id,
                            'package_id' => $item->package_id,
                            'book_id' => $item->book_id,
                            'price' => $item->price ?? 0,
                        ]);
                    }

                    DB::commit();
                    Log::info("Abandoned cart tracked for student ID: {$studentId}");
                } catch (\Exception $e) {
                    DB::rollback();
                    Log::error("Error creating abandoned cart: " . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            Log::error("Error tracking abandoned carts: " . $e->getMessage());
        }
    }

    /**
     * Mark cart as converted when user completes purchase.
     */
    public function markAsConverted($studentId)
    {
        try {
            $abandonedCart = AbandonedCart::where('student_id', $studentId)
                ->where('converted', false)
                ->latest()
                ->first();

            if ($abandonedCart) {
                $abandonedCart->markAsConverted();
                Log::info("Abandoned cart converted for student ID: {$studentId}");
                return true;
            }
        } catch (\Exception $e) {
            Log::error("Error marking cart as converted: " . $e->getMessage());
        }

        return false;
    }

    /**
     * Generate a discount coupon code.
     */
    public function generateDiscountCoupon()
    {
        return 'COMEBACK' . strtoupper(Str::random(6));
    }

    /**
     * Create a unique abandoned cart coupon for a specific student and cart items.
     * The coupon is valid for 24 hours and applies only to the products in the cart.
     *
     * @param AbandonedCart $cart
     * @return string The coupon code
     */
    public function createAbandonedCartCoupon(AbandonedCart $cart)
    {
        $code = $this->generateDiscountCoupon();

        try {
            // Create the coupon with 24hr expiry
            $couponId = DB::table('coupons')->insertGetId([
                'code' => $code,
                'nom' => 'Abandoned Cart Recovery - ' . $cart->student->first_name,
                'description' => 'Special 10% discount for returning customer. Valid for 24 hours only.',
                'valeur' => 10.00,
                'type' => 'percentage',
                'date_debut' => now()->format('Y-m-d H:i:s'),
                'date_fin' => now()->addHours(24)->format('Y-m-d H:i:s'),
                'usage_limit' => 1,
                'usage_count' => 0,
                'is_active' => 1,
                'is_stackable' => 0,
                'stack_priority' => 0,
                'customer_type' => 'all',
                'auto_apply' => 0,
                'montant_minimum' => 0,
                'max_discount_amount' => null,
                'is_public' => 0, // Not publicly visible
                'first_purchase_only' => 0,
                'cumulative_enabled' => 0,
                'allow_multiple_uses' => 0,
                'max_uses_per_user' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Link coupon to specific products in the cart
            foreach ($cart->items as $item) {
                if ($item->product_id) {
                    DB::table('coupon_detailles')->insert([
                        'coupon_id' => $couponId,
                        'product_id' => $item->product_id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            Log::info("Created abandoned cart coupon: {$code} for student ID: {$cart->student_id}, valid for 24 hours");

            return $code;
        } catch (\Exception $e) {
            Log::error("Failed to create abandoned cart coupon: " . $e->getMessage());
            // Fallback to generic code
            return 'COMEBACK10';
        }
    }

    /**
     * Get abandoned cart statistics.
     */
    public function getStatistics($dateFrom = null, $dateTo = null)
    {
        $baseQuery = AbandonedCart::query();

        if ($dateFrom) {
            $baseQuery->where('abandoned_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $baseQuery->where('abandoned_at', '<=', $dateTo);
        }

        $total = $baseQuery->count();
        $converted = (clone $baseQuery)->where('converted', true)->count();
        $totalRevenue = (clone $baseQuery)->where('converted', true)->sum('total_amount');
        $potentialRevenue = (clone $baseQuery)->where('converted', false)->sum('total_amount');

        return [
            'total_abandoned' => $total,
            'converted' => $converted,
            'not_converted' => $total - $converted,
            'conversion_rate' => $total > 0 ? round(($converted / $total) * 100, 2) : 0,
            'recovered_revenue' => $totalRevenue,
            'potential_revenue' => $potentialRevenue,
            'first_reminders_sent' => (clone $baseQuery)->whereNotNull('first_reminder_sent_at')->count(),
            'second_reminders_sent' => (clone $baseQuery)->whereNotNull('second_reminder_sent_at')->count(),
            'third_reminders_sent' => (clone $baseQuery)->whereNotNull('third_reminder_sent_at')->count(),
        ];
    }
}
