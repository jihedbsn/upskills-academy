<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PointsService
{
    /**
     * Points to dollar conversion rate
     * 20 points = $1 discount
     */
    const POINTS_PER_DOLLAR = 20;

    /**
     * Dollars to points earning rate
     * $1 spent = 1 point earned
     */
    const POINTS_EARNED_PER_DOLLAR = 1;

    /**
     * Calculate the discount amount for given points.
     */
    public static function calculateDiscount(int $points): float
    {
        return round($points / self::POINTS_PER_DOLLAR, 2);
    }

    /**
     * Calculate points earned for a purchase amount.
     */
    public static function calculatePointsEarned(float $amount): int
    {
        return (int) floor($amount * self::POINTS_EARNED_PER_DOLLAR);
    }

    /**
     * Calculate how many points are needed for a specific discount.
     */
    public static function pointsNeededForDiscount(float $discount): int
    {
        return (int) ceil($discount * self::POINTS_PER_DOLLAR);
    }

    /**
     * Get customer points balance.
     *
     * @param int $studentId
     * @return array
     */
    public function getPointsBalance(int $studentId): array
    {
        $points = DB::table('customer_points')
            ->where('student_id', $studentId)
            ->first();

        if (!$points) {
            return [
                'total_points' => 0,
                'available_points' => 0,
                'used_points' => 0,
                'available_discount' => 0,
            ];
        }

        return [
            'total_points' => $points->total_points,
            'available_points' => $points->available_points,
            'used_points' => $points->used_points,
            'available_discount' => self::calculateDiscount($points->available_points),
        ];
    }

    /**
     * Get customer points history.
     *
     * @param int $studentId
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function getPointsHistory(int $studentId, int $limit = 20)
    {
        return DB::table('points_transactions')
            ->where('student_id', $studentId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Calculate the maximum discount a customer can get.
     *
     * @param int $studentId
     * @param float|null $maxDiscount Optional limit on discount amount
     * @return array
     */
    public function calculateMaxDiscount(int $studentId, float $maxDiscount = null): array
    {
        $balance = $this->getPointsBalance($studentId);
        $availableDiscount = $balance['available_discount'];

        if ($maxDiscount !== null && $availableDiscount > $maxDiscount) {
            $availableDiscount = $maxDiscount;
        }

        $pointsToUse = self::pointsNeededForDiscount($availableDiscount);

        return [
            'available_points' => $balance['available_points'],
            'max_discount' => $availableDiscount,
            'points_to_use' => min($pointsToUse, $balance['available_points']),
        ];
    }

    /**
     * Award points to a customer for a purchase.
     *
     * @param int $studentId
     * @param float $purchaseAmount
     * @param int|null $orderId
     * @param string|null $description
     * @return int Points earned
     */
    public function awardPointsForPurchase(int $studentId, float $purchaseAmount, ?int $orderId = null, ?string $description = null): int
    {
        $pointsEarned = self::calculatePointsEarned($purchaseAmount);

        if ($pointsEarned <= 0) {
            return 0;
        }

        return DB::transaction(function () use ($studentId, $pointsEarned, $purchaseAmount, $orderId, $description) {
            // Get or create customer points record
            $customerPoints = DB::table('customer_points')
                ->where('student_id', $studentId)
                ->first();

            if (!$customerPoints) {
                DB::table('customer_points')->insert([
                    'student_id' => $studentId,
                    'total_points' => $pointsEarned,
                    'available_points' => $pointsEarned,
                    'used_points' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $newBalance = $pointsEarned;
            } else {
                $newBalance = $customerPoints->available_points + $pointsEarned;
                DB::table('customer_points')
                    ->where('student_id', $studentId)
                    ->update([
                        'total_points' => $customerPoints->total_points + $pointsEarned,
                        'available_points' => $newBalance,
                        'updated_at' => now(),
                    ]);
            }

            // Record transaction
            DB::table('points_transactions')->insert([
                'student_id' => $studentId,
                'order_id' => $orderId,
                'type' => 'earn',
                'points' => $pointsEarned,
                'amount' => $purchaseAmount,
                'description' => $description ?? "Points earned for \${$purchaseAmount} purchase",
                'balance_after' => $newBalance,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::info("Awarded {$pointsEarned} points to student {$studentId} for \${$purchaseAmount} purchase");

            return $pointsEarned;
        });
    }

    /**
     * Redeem points for a discount.
     *
     * @param int $studentId
     * @param int $pointsToRedeem
     * @param int|null $orderId
     * @param string|null $description
     * @return array ['success' => bool, 'discount' => float, 'message' => string]
     */
    public function redeemPoints(int $studentId, int $pointsToRedeem, ?int $orderId = null, ?string $description = null): array
    {
        $customerPoints = DB::table('customer_points')
            ->where('student_id', $studentId)
            ->first();

        if (!$customerPoints) {
            return [
                'success' => false,
                'discount' => 0,
                'message' => 'No points available',
            ];
        }

        if ($pointsToRedeem <= 0) {
            return [
                'success' => false,
                'discount' => 0,
                'message' => 'Invalid points amount',
            ];
        }

        if ($pointsToRedeem > $customerPoints->available_points) {
            return [
                'success' => false,
                'discount' => 0,
                'message' => 'Insufficient points balance',
            ];
        }

        $discount = self::calculateDiscount($pointsToRedeem);

        return DB::transaction(function () use ($customerPoints, $studentId, $pointsToRedeem, $discount, $orderId, $description) {
            $newBalance = $customerPoints->available_points - $pointsToRedeem;

            DB::table('customer_points')
                ->where('student_id', $studentId)
                ->update([
                    'available_points' => $newBalance,
                    'used_points' => $customerPoints->used_points + $pointsToRedeem,
                    'updated_at' => now(),
                ]);

            DB::table('points_transactions')->insert([
                'student_id' => $studentId,
                'order_id' => $orderId,
                'type' => 'redeem',
                'points' => -$pointsToRedeem,
                'amount' => $discount,
                'description' => $description ?? "Redeemed {$pointsToRedeem} points for \${$discount} discount",
                'balance_after' => $newBalance,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::info("Student {$studentId} redeemed {$pointsToRedeem} points for \${$discount} discount");

            return [
                'success' => true,
                'discount' => $discount,
                'message' => "Successfully redeemed {$pointsToRedeem} points for \${$discount} discount",
            ];
        });
    }

    /**
     * Admin manual adjustment for student points.
     *
     * @param int $studentId
     * @param int $points Positive to add, negative to deduct
     * @param string $reason
     * @return array{success:bool,message:string,balance:int}
     */
    public function adjustPoints(int $studentId, int $points, string $reason): array
    {
        if ($points === 0) {
            return [
                'success' => false,
                'message' => 'Points adjustment cannot be zero.',
                'balance' => 0,
            ];
        }

        return DB::transaction(function () use ($studentId, $points, $reason) {
            $customerPoints = DB::table('customer_points')
                ->where('student_id', $studentId)
                ->first();

            if (!$customerPoints) {
                $available = max(0, $points);
                $used = $points < 0 ? abs($points) : 0;

                DB::table('customer_points')->insert([
                    'student_id' => $studentId,
                    'total_points' => max(0, $points),
                    'available_points' => $available,
                    'used_points' => $used,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $newBalance = $available;
            } else {
                $newBalance = max(0, $customerPoints->available_points + $points);
                $newUsed = $customerPoints->used_points + ($points < 0 ? abs($points) : 0);
                $newTotal = $customerPoints->total_points + ($points > 0 ? $points : 0);

                DB::table('customer_points')
                    ->where('student_id', $studentId)
                    ->update([
                        'total_points' => max(0, $newTotal),
                        'available_points' => $newBalance,
                        'used_points' => max(0, $newUsed),
                        'updated_at' => now(),
                    ]);
            }

            DB::table('points_transactions')->insert([
                'student_id' => $studentId,
                'order_id' => null,
                'type' => 'adjust',
                'points' => $points,
                'amount' => 0,
                'description' => 'Admin adjustment: ' . $reason,
                'balance_after' => $newBalance,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::info("Admin adjusted points for student {$studentId}", [
                'points' => $points,
                'reason' => $reason,
                'new_balance' => $newBalance,
            ]);

            return [
                'success' => true,
                'message' => 'Points adjusted successfully.',
                'balance' => (int) $newBalance,
            ];
        });
    }
}
