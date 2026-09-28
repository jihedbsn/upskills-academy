<?php

namespace App\Helpers;

use App\Models\ActiveCartCoupon;
use App\Models\CouponUsageLog;
use App\Models\PartnerCommission;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

trait CouponUsageHelper
{
    /**
     * Log coupon usage and create commissions after successful order
     *
     * @param Order $order
     * @param float $originalTotal
     * @param float $finalTotal
     * @param int $cartItemsCount
     * @param array $trackingData Additional tracking data (UTM, device, etc.)
     * @return array Statistics about logged usage
     */
    protected function logCouponUsage(
        Order $order,
        float $originalTotal,
        float $finalTotal,
        int $cartItemsCount,
        array $trackingData = []
    ): array {
        $sessionId = session()->getId();
        $studentId = Auth::id();

        // Get all active cart coupons
        $activeCoupons = ActiveCartCoupon::where(function($q) use ($sessionId, $studentId) {
            $q->where('session_id', $sessionId);
            if ($studentId) {
                $q->orWhere('student_id', $studentId);
            }
        })
        ->with('coupon')
        ->get();

        if ($activeCoupons->isEmpty()) {
            return [
                'logged_count' => 0,
                'commissions_created' => 0,
                'total_discount' => 0
            ];
        }

        $loggedCount = 0;
        $commissionsCreated = 0;
        $totalDiscount = 0;
        $couponIds = $activeCoupons->pluck('coupon_id')->toArray();
        $isStacked = $activeCoupons->count() > 1;

        foreach ($activeCoupons as $activeCartCoupon) {
            $coupon = $activeCartCoupon->coupon;

            // Create usage log
            $usageLog = CouponUsageLog::create([
                'coupon_id' => $coupon->id,
                'student_id' => $studentId,
                'order_id' => $order->id,
                'discount_amount' => $activeCartCoupon->discount_amount,
                'original_price' => $originalTotal,
                'final_price' => $finalTotal,
                'cart_items_count' => $cartItemsCount,
                'is_stacked' => $isStacked,
                'stacked_with_coupon_ids' => $isStacked ? json_encode($couponIds) : null,
                'total_stacked_discount' => $isStacked ? $activeCoupons->sum('discount_amount') : null,
                'utm_source' => $trackingData['utm_source'] ?? null,
                'utm_medium' => $trackingData['utm_medium'] ?? null,
                'utm_campaign' => $trackingData['utm_campaign'] ?? null,
                'referrer_url' => $trackingData['referrer_url'] ?? request()->headers->get('referer'),
                'user_agent' => request()->userAgent(),
                'device_type' => $this->detectDeviceType(),
                'ip_address' => request()->ip(),
                'country_code' => $trackingData['country_code'] ?? null,
                'city' => $trackingData['city'] ?? null,
                'browser' => $this->detectBrowser(),
                'os' => $this->detectOS(),
                'application_method' => 'manual', // Can be 'manual' or 'auto'
                'was_auto_applied' => $coupon->auto_apply ? true : false,
                'auto_apply_trigger' => $coupon->auto_apply ? 'cart_conditions_met' : null,
            ]);

            $loggedCount++;
            $totalDiscount += $activeCartCoupon->discount_amount;

            // Increment coupon usage count
            $coupon->increment('usage_count');

            // Create commission if affiliate partner
            if ($coupon->affiliate_partner_id && $coupon->commission_rate > 0) {
                $commissionAmount = ($finalTotal * $coupon->commission_rate) / 100;

                PartnerCommission::create([
                    'affiliate_partner_id' => $coupon->affiliate_partner_id,
                    'coupon_usage_log_id' => $usageLog->id,
                    'order_id' => $order->id,
                    'student_id' => $studentId,
                    'order_amount' => $finalTotal,
                    'commission_rate' => $coupon->commission_rate,
                    'commission_amount' => $commissionAmount,
                    'currency' => 'USD',
                    'status' => 'pending',
                    'created_at' => Carbon::now()
                ]);

                // Update partner's total commission earned
                $partner = $coupon->affiliatePartner;
                if ($partner) {
                    $partner->total_commission_earned += $commissionAmount;
                    $partner->total_sales += $finalTotal;
                    $partner->save();
                }

                $commissionsCreated++;
            }
        }

        // Cleanup: Delete active cart coupons after logging
        ActiveCartCoupon::where(function($q) use ($sessionId, $studentId) {
            $q->where('session_id', $sessionId);
            if ($studentId) {
                $q->orWhere('student_id', $studentId);
            }
        })->delete();

        return [
            'logged_count' => $loggedCount,
            'commissions_created' => $commissionsCreated,
            'total_discount' => $totalDiscount
        ];
    }

    /**
     * Cleanup expired cart coupons
     */
    protected function cleanupExpiredCartCoupons(): int
    {
        return ActiveCartCoupon::where('expires_at', '<=', Carbon::now())->delete();
    }

    /**
     * Recalculate cart discount after cart changes
     *
     * @param float $newCartTotal
     * @return array Updated discount information
     */
    protected function recalculateCartDiscount(float $newCartTotal): array
    {
        $sessionId = session()->getId();
        $studentId = Auth::id();

        $activeCoupons = ActiveCartCoupon::where(function($q) use ($sessionId, $studentId) {
            $q->where('session_id', $sessionId);
            if ($studentId) {
                $q->orWhere('student_id', $studentId);
            }
        })
        ->where(function($q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>', Carbon::now());
        })
        ->with('coupon')
        ->get();

        $totalDiscount = 0;
        $updatedCoupons = [];

        foreach ($activeCoupons as $activeCartCoupon) {
            $newDiscount = $activeCartCoupon->coupon->calculateDiscount($newCartTotal);

            // Update if discount changed significantly (more than 1 cent)
            if (abs($newDiscount - $activeCartCoupon->discount_amount) > 0.01) {
                $activeCartCoupon->update([
                    'cart_total' => $newCartTotal,
                    'discount_amount' => $newDiscount
                ]);
            }

            $totalDiscount += $newDiscount;
            $updatedCoupons[] = [
                'code' => $activeCartCoupon->coupon->code,
                'discount' => $newDiscount
            ];
        }

        return [
            'total_discount' => $totalDiscount,
            'final_total' => max(0, $newCartTotal - $totalDiscount),
            'coupons' => $updatedCoupons
        ];
    }

    /**
     * Detect device type from user agent
     */
    private function detectDeviceType(): string
    {
        $userAgent = request()->userAgent();

        if (preg_match('/mobile|android|iphone|ipad|ipod/i', $userAgent)) {
            return 'mobile';
        } elseif (preg_match('/tablet|ipad/i', $userAgent)) {
            return 'tablet';
        }

        return 'desktop';
    }

    /**
     * Detect browser from user agent
     */
    private function detectBrowser(): string
    {
        $userAgent = request()->userAgent();

        if (preg_match('/Chrome/i', $userAgent)) return 'Chrome';
        if (preg_match('/Safari/i', $userAgent)) return 'Safari';
        if (preg_match('/Firefox/i', $userAgent)) return 'Firefox';
        if (preg_match('/Edge/i', $userAgent)) return 'Edge';
        if (preg_match('/MSIE|Trident/i', $userAgent)) return 'Internet Explorer';

        return 'Unknown';
    }

    /**
     * Detect operating system from user agent
     */
    private function detectOS(): string
    {
        $userAgent = request()->userAgent();

        if (preg_match('/Windows/i', $userAgent)) return 'Windows';
        if (preg_match('/Mac|Macintosh/i', $userAgent)) return 'MacOS';
        if (preg_match('/Linux/i', $userAgent)) return 'Linux';
        if (preg_match('/Android/i', $userAgent)) return 'Android';
        if (preg_match('/iPhone|iPad|iPod/i', $userAgent)) return 'iOS';

        return 'Unknown';
    }

    /**
     * Get active coupons summary for display
     *
     * @return array
     */
    protected function getActiveCartCouponsSummary(): array
    {
        $sessionId = session()->getId();
        $studentId = Auth::id();

        $activeCoupons = ActiveCartCoupon::where(function($q) use ($sessionId, $studentId) {
            $q->where('session_id', $sessionId);
            if ($studentId) {
                $q->orWhere('student_id', $studentId);
            }
        })
        ->where(function($q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>', Carbon::now());
        })
        ->with('coupon')
        ->orderBy('created_at')
        ->get();

        $totalDiscount = $activeCoupons->sum('discount_amount');

        return [
            'coupons' => $activeCoupons->map(function($active) {
                return [
                    'id' => $active->coupon->id,
                    'code' => $active->coupon->code,
                    'nom' => $active->coupon->nom,
                    'discount_amount' => $active->discount_amount,
                    'formatted_discount' => '$' . number_format($active->discount_amount, 2),
                    'is_stackable' => $active->coupon->is_stackable,
                    'applied_at' => $active->applied_at->format('Y-m-d H:i:s')
                ];
            }),
            'total_discount' => $totalDiscount,
            'formatted_total_discount' => '$' . number_format($totalDiscount, 2),
            'count' => $activeCoupons->count()
        ];
    }
}
