<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReferralDiscountService
{
    public static function creditBalanceFor(?int $studentId): float
    {
        if (!$studentId) {
            return 0.0;
        }

        try {
            $adminUrl = env('ADMIN_URL', 'http://localhost:8100');
            $res = Http::timeout(5)->get("{$adminUrl}/api/referrals/student/{$studentId}/credit-balance");
            if ($res->successful()) {
                return (float) ($res->json()['balance'] ?? 0);
            }
        } catch (\Exception $e) {
            Log::error('Referral credit balance fetch failed: ' . $e->getMessage());
        }

        return 0.0;
    }

    public static function consumeCreditForOrder(?int $studentId, ?int $orderId, float $amount): float
    {
        if (!$studentId || !$orderId || $amount <= 0) {
            return 0.0;
        }

        try {
            $adminUrl = env('ADMIN_URL', 'http://localhost:8100');
            $res = Http::timeout(5)->post("{$adminUrl}/api/referrals/consume-credit", [
                'student_id' => $studentId,
                'order_id'   => $orderId,
                'amount'     => $amount,
            ]);
            if ($res->successful()) {
                return (float) ($res->json()['consumed'] ?? 0);
            }
        } catch (\Exception $e) {
            Log::error('Referral credit consume failed: ' . $e->getMessage());
        }

        return 0.0;
    }

    public const PERCENT = 5.0;

    public static function percentFor(?int $studentId): float
    {
        if (!$studentId) {
            return 0.0;
        }

        $hasPendingReferral = DB::table('referrals')
            ->where('referred_id', $studentId)
            ->where('status', 'pending')
            ->exists();

        if (!$hasPendingReferral) {
            return 0.0;
        }

        $hasPaidOrder = DB::table('orders')
            ->where('student_id', $studentId)
            ->where('payment_success', true)
            ->exists();

        if ($hasPaidOrder) {
            return 0.0;
        }

        return self::PERCENT;
    }

    public static function amountFor(?int $studentId, float $subtotal): float
    {
        $pct = self::percentFor($studentId);
        if ($pct <= 0 || $subtotal <= 0) {
            return 0.0;
        }

        return round($subtotal * $pct / 100, 2);
    }
}
