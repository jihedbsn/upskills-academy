<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Coupon;
use App\Models\Student;
use App\Models\CouponUsageLog;
use App\Models\ActiveCartCoupon;
use App\Models\PartnerCommission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class CouponService
{
    public function getValidCouponsForUser(Student $user, bool $publicOnly = true): \Illuminate\Database\Eloquent\Collection
    {
        $query = Coupon::active()
            ->where('date_debut', '<=', value: now())
            ->where('date_fin', '>=', now());

        if ($publicOnly) {
            $query->where('is_public', true);
        }

        return $query->get()->filter(function ($coupon) use ($user) {
            return $coupon->canBeUsedByUser($user);
        });
    }

    // Get coupons available for a specific customer type
    public function getCouponsForCustomerType(Student $user): Collection
    {
        $customerType = $user->getCustomerType();

        return Coupon::active()
            ->where('is_public', true)
            ->where(function ($q) use ($customerType) {
                $q->where('customer_type', 'all')
                    ->orWhere('customer_type', $customerType);
            })
            ->where('date_debut', '<=', now())
            ->where('date_fin', '>=', now())
            ->get()
            ->filter(function ($coupon) use ($user) {
                return $coupon->canBeUsedByUser($user);
            });
    }

     // Validate coupon for specific user with detailed feedback
    public function validateCouponForUser(string $code, Student $user): array
    {
        $coupon = Coupon::where('code', strtoupper($code))->first();

        if (!$coupon) {
            return [
                'valid' => false,
                'message' => __('coupon.code_not_found'),
                'coupon' => null
            ];
        }

        $validation = $coupon->getValidationResult($user);

        return [
            'valid' => $validation['can_use'],
            'message' => $validation['can_use']
                ? __('coupon.valid')
                : __('coupon.cannot_be_used') . ': ' . implode(' ', $validation['reasons']),
            'reasons' => $validation['reasons'],
            'coupon' => $coupon,
            'user_type' => $validation['user_type'],
            'user_stats' => $validation['user_stats']
        ];
    }

    /**
     * Apply coupon with automatic customer type validation
     */
    public function applyCouponToCart(string $code, Student $user, array $cartData): array
    {
        $validation = $this->validateCouponForUser($code, $user);

        if (!$validation['valid']) {
            return [
                'success' => false,
                'message' => $validation['message'],
                'details' => $validation
            ];
        }

        $coupon = $validation['coupon'];

        // Additional cart validation (minimum amount, items, etc.)
        if ($coupon->min_purchase_amount && $cartData['total'] < $coupon->min_purchase_amount) {
            return [
                'success' => false,
                'message' => __('coupon.minimum_purchase_required', [
                    'amount' => '$' . number_format($coupon->min_purchase_amount, 2),
                    'current' => '$' . number_format($cartData['total'], 2)
                ])
            ];
        }

        if ($coupon->min_items && $cartData['item_count'] < $coupon->min_items) {
            return [
                'success' => false,
                'message' => __('coupon.minimum_items_required', [
                    'count' => $coupon->min_items,
                    'current' => $cartData['item_count']
                ])
            ];
        }

        // Check minimum cart items requirement (default is 1 if not set)
        $minCartItems = $coupon->min_cart_items ?? 1;
        if ($minCartItems > 0 && $cartData['item_count'] < $minCartItems) {
            return [
                'success' => false,
                'message' => __('coupon.minimum_cart_items_required', [
                    'count' => $minCartItems,
                    'current' => $cartData['item_count']
                ])
            ];
        }

        // Check course types if specified
        if ($coupon->course_types && !empty($coupon->course_types)) {
            $hasMatchingType = false;
            foreach ($cartData['course_types'] ?? [] as $type) {
                if (in_array($type, $coupon->course_types)) {
                    $hasMatchingType = true;
                    break;
                }
            }

            if (!$hasMatchingType) {
                return [
                    'success' => false,
                    'message' => __('coupon.not_applicable_courses')
                ];
            }
        }

        // Check if coupon is restricted to specific products
        $couponProducts = $coupon->detailles()->pluck('product_id')->toArray();
        if (!empty($couponProducts)) {
            $cartProductIds = $cartData['product_ids'] ?? [];
            $hasMatchingProduct = false;

            foreach ($cartProductIds as $productId) {
                if (in_array($productId, $couponProducts)) {
                    $hasMatchingProduct = true;
                    break;
                }
            }

            if (!$hasMatchingProduct) {
                return [
                    'success' => false,
                    'message' => __('messages.coupon_not_applicable_products')
                ];
            }
        }

        // Calculate discount
        $discountAmount = $this->calculateDiscount($coupon, $cartData);

        // Store in active cart coupons
        $this->storeActiveCartCoupon($user, $coupon, $cartData, $discountAmount);

        return [
            'success' => true,
            'message' => __('coupon.applied_successfully'),
            'coupon' => $coupon,
            'discount_amount' => $discountAmount,
            'user_type' => $validation['user_type'],
            'original_total' => $cartData['total'],
            'final_total' => max(0, $cartData['total'] - $discountAmount)
        ];
    }

    /**
     * Calculate discount amount based on coupon type
     */
    private function calculateDiscount(Coupon $coupon, array $cartData): float
    {
        $discountAmount = 0;

        if ($coupon->type === 'percentage') {
            $discountAmount = ($cartData['total'] * $coupon->valeur) / 100;

            // Apply maximum discount limit if set
            if ($coupon->max_discount_amount && $discountAmount > $coupon->max_discount_amount) {
                $discountAmount = $coupon->max_discount_amount;
            }
        } else {
            // Fixed amount discount
            $discountAmount = min($coupon->valeur, $cartData['total']);
        }

        return round($discountAmount, 2);
    }

    /**
     * Get auto-applicable coupons for current cart
     */
    public function getAutoCouponsForCart(Student $user, array $cartData): array
    {
        $cartDataWithType = array_merge($cartData, [
            'customer_type' => $user->getCustomerType()
        ]);

        // Find auto-applicable coupons
        $autoCoupons = Coupon::active()
            ->where('auto_apply', true)
            ->where('is_public', true)
            ->where('date_debut', '<=', now())
            ->where('date_fin', '>=', now())
            ->get()
            ->filter(function ($coupon) use ($user, $cartDataWithType) {
                return $coupon->canBeUsedByUser($user) &&
                    $coupon->checkAutoApplyConditions($cartDataWithType);
            });

        // Calculate discount for each to find the best one
        $couponsWithDiscount = $autoCoupons->map(function ($coupon) use ($cartData) {
            $discount = $this->calculateDiscount($coupon, $cartData);
            return [
                'coupon' => $coupon,
                'discount_amount' => $discount,
                'final_total' => $cartData['total'] - $discount
            ];
        })->sortByDesc('discount_amount');

        return [
            'auto_coupons' => $autoCoupons->values(),
            'best_coupon' => $couponsWithDiscount->first()
        ];
    }

    /**
     * Find the best applicable coupon for the cart
     * Checks all public coupons and returns the one with highest discount
     */
    public function findBestCouponForCart(Student $user, array $cartData): ?array
    {
        // Get all valid public coupons
        $validCoupons = Coupon::active()
            ->where('is_public', true)
            ->where('date_debut', '<=', now())
            ->where('date_fin', '>=', now())
            ->get()
            ->filter(function ($coupon) use ($user) {
                return $coupon->canBeUsedByUser($user);
            });

        if ($validCoupons->isEmpty()) {
            return null;
        }

        $bestCoupon = null;
        $bestDiscount = 0;

        foreach ($validCoupons as $coupon) {
            // Try to apply the coupon
            $result = $this->applyCouponToCart($coupon->code, $user, $cartData);

            if ($result['success'] && $result['discount_amount'] > $bestDiscount) {
                $bestDiscount = $result['discount_amount'];
                $bestCoupon = $result;
            }
        }

        return $bestCoupon;
    }

    /**
     * Find the best auto-apply coupon for the cart
     * Only checks coupons marked as auto_apply = true
     */
    public function findBestAutoApplyCoupon(Student $user, array $cartData): ?array
    {
        // Get all valid auto-apply coupons
        $autoApplyCoupons = Coupon::active()
            ->where('is_public', true)
            ->where('auto_apply', true)
            ->where('date_debut', '<=', now())
            ->where('date_fin', '>=', now())
            ->get()
            ->filter(function ($coupon) use ($user) {
                return $coupon->canBeUsedByUser($user);
            });

        if ($autoApplyCoupons->isEmpty()) {
            return null;
        }

        $bestCoupon = null;
        $bestDiscount = 0;

        foreach ($autoApplyCoupons as $coupon) {
            // Try to apply the coupon
            $result = $this->applyCouponToCart($coupon->code, $user, $cartData);

            if ($result['success'] && $result['discount_amount'] > $bestDiscount) {
                $bestDiscount = $result['discount_amount'];
                $bestCoupon = $result;
            }
        }

        return $bestCoupon;
    }

    /**
     * Record coupon usage after successful order
     */
    public function recordCouponUsage(Coupon $coupon, Student $user, string $orderId, float $discountAmount, ?float $originalPrice = null, ?float $finalPrice = null): void
    {
        try {
            DB::beginTransaction();

            // Calculate prices if not provided
            if ($originalPrice === null || $finalPrice === null) {
                $order = \App\Models\Order::find($orderId);
                if ($order) {
                    $finalPrice = $order->price;
                    $originalPrice = $finalPrice + $discountAmount;
                }
            }

            // Record in usage logs
            $usageLog = CouponUsageLog::create([
                'coupon_id' => $coupon->id,
                'student_id' => $user->id,
                'order_id' => $orderId,
                'original_price' => $originalPrice,
                'final_price' => $finalPrice,
                'discount_amount' => $discountAmount,
                'used_at' => now()
            ]);

            // Note: CouponUsageLog already tracks order-level usage, so we don't need recordOrderUsage
            // which has foreign key constraint issues with the coupon_order_usage table
            // $coupon->recordOrderUsage($user->id, $orderId, $discountAmount);

            // Increment usage count
            $coupon->usage_count = $coupon->usage_count + 1;
            $coupon->save();

            // Track affiliate commission if applicable
            if ($coupon->affiliate_partner_id && $coupon->commission_rate) {
                // Commission should be calculated on order total, not discount amount
                $orderTotal = $originalPrice ?? 0;
                $commissionAmount = ($orderTotal * $coupon->commission_rate) / 100;

                // Create commission record with proper field names
                PartnerCommission::create([
                    'partner_id' => $coupon->affiliate_partner_id,
                    'coupon_usage_id' => $usageLog->id,
                    'order_id' => $orderId,
                    'order_total' => $orderTotal,
                    'commission_rate' => $coupon->commission_rate,
                    'commission_amount' => $commissionAmount,
                    'discount_amount' => $discountAmount,
                    'base_amount' => $commissionAmount,
                    'tax_amount' => 0.00,
                    'fee_amount' => 0.00,
                    'net_commission' => $commissionAmount, // Net commission after fees/taxes
                    'status' => 'pending',
                    'calculated_at' => now(),
                    'currency' => 'USD' // Default currency
                ]);

                // Update affiliate partner stats
                $coupon->affiliatePartner->increment('total_referrals');
                $coupon->affiliatePartner->increment('pending_earnings', $commissionAmount);
            }

            // Remove from active cart coupons
            ActiveCartCoupon::where('coupon_id', $coupon->id)
                ->where('student_id', $user->id)
                ->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Store active cart coupon
     */
    private function storeActiveCartCoupon(Student $user, Coupon $coupon, array $cartData, float $discountAmount): void
    {
        // Remove any existing cart coupon for this user
        ActiveCartCoupon::where('student_id', $user->id)->delete();

        // Store new active cart coupon
        ActiveCartCoupon::create([
            'student_id' => $user->id,
            'coupon_id' => $coupon->id,
            'session_id' => $cartData['session_id'] ?? session()->getId(),
            'cart_total' => $cartData['total'],
            'discount_amount' => $discountAmount,
            'original_cart_total' => $cartData['total'],
            'applied_at' => now(),
            'expires_at' => now()->addHours(24) // Expire after 24 hours
        ]);
    }

    /**
     * Remove coupon from cart
     */
    public function removeCouponFromCart(Student $user): bool
    {
        try {
            ActiveCartCoupon::where('student_id', $user->id)->delete();
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to remove coupon from cart', [
                'error' => $e->getMessage(),
                'student_id' => $user->id
            ]);
            return false;
        }
    }

    /**
     * Get active coupon in user's cart
     */
    public function getActiveCartCoupon(Student $user): ?array
    {
        $activeCartCoupon = ActiveCartCoupon::where('student_id', $user->id)
            ->with('coupon')
            ->first();

        if (!$activeCartCoupon || !$activeCartCoupon->coupon) {
            return null;
        }

        $coupon = $activeCartCoupon->coupon;

        // Validate coupon is still valid
        if (!$coupon->isValid() || !$coupon->canBeUsedByUser($user)) {
            // Remove invalid coupon from cart
            $activeCartCoupon->delete();
            return null;
        }

        return [
            'coupon' => $coupon,
            'applied_at' => $activeCartCoupon->applied_at,
            'code' => $coupon->code
        ];
    }

    /**
     * Check if user can stack multiple coupons (for future enhancement)
     */
    public function canStackCoupons(array $couponCodes, Student $user, array $cartData): array
    {
        $validCoupons = [];
        $totalDiscount = 0;
        $errors = [];

        foreach ($couponCodes as $code) {
            $validation = $this->validateCouponForUser($code, $user);

            if ($validation['valid']) {
                $coupon = $validation['coupon'];

                // Check if coupon is stackable
                if (!$coupon->is_stackable && count($validCoupons) > 0) {
                    $errors[] = "Coupon '{$code}' cannot be stacked with other coupons.";
                    continue;
                }

                // Check stacking compatibility with existing coupons
                $canStack = true;
                foreach ($validCoupons as $existingCoupon) {
                    if (!$coupon->canBeStackedWith($existingCoupon['coupon'])) {
                        $canStack = false;
                        $errors[] = "Coupon '{$code}' cannot be stacked with '{$existingCoupon['coupon']->code}'.";
                        break;
                    }
                }

                if ($canStack) {
                    $discount = $this->calculateDiscount($coupon, $cartData);
                    $validCoupons[] = [
                        'coupon' => $coupon,
                        'discount' => $discount
                    ];
                    $totalDiscount += $discount;
                }
            } else {
                $errors[] = $validation['message'];
            }
        }

        return [
            'can_stack' => count($validCoupons) > 1,
            'valid_coupons' => $validCoupons,
            'total_discount' => $totalDiscount,
            'final_total' => max(0, $cartData['total'] - $totalDiscount),
            'errors' => $errors
        ];
    }

    /**
     * Suggest best coupon for cart
     */
    public function suggestBestCoupon(Student $user, array $cartData): ?array
    {
        $availableCoupons = $this->getValidCouponsForUser($user, true);

        if ($availableCoupons->isEmpty()) {
            return null;
        }

        $bestCoupon = null;
        $maxDiscount = 0;

        /** @var Coupon $coupon */
        foreach ($availableCoupons as $coupon) {
            // Check if coupon meets cart requirements
            if ($coupon->min_purchase_amount && $cartData['total'] < $coupon->min_purchase_amount) {
                continue;
            }

            if ($coupon->min_items && $cartData['item_count'] < $coupon->min_items) {
                continue;
            }

            $discount = $this->calculateDiscount($coupon, $cartData);

            if ($discount > $maxDiscount) {
                $maxDiscount = $discount;
                $bestCoupon = $coupon;
            }
        }

        if ($bestCoupon) {
            return [
                'coupon' => $bestCoupon,
                'discount_amount' => $maxDiscount,
                'final_total' => $cartData['total'] - $maxDiscount,
                'savings_percentage' => ($maxDiscount / $cartData['total']) * 100
            ];
        }

        return null;
    }

}
