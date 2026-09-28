<?php

namespace App\Services;

use App\Models\ActiveCartCoupon;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CouponFrontendService
{
    public function index()
    {
        $customerType = $this->determineCustomerType();

        $coupons = Coupon::where('is_public', true)
            ->where('is_active', true)
            ->where('code', 'NOT LIKE', 'COMEBACK%')
            ->where(function ($q) {
                $q->whereNull('date_debut')->orWhere('date_debut', '<=', Carbon::now());
            })
            ->where(function ($q) {
                $q->whereNull('date_fin')->orWhere('date_fin', '>=', Carbon::now());
            })
            ->where(function ($q) use ($customerType) {
                $q->where('customer_type', 'all')->orWhere('customer_type', $customerType);
            })
            ->where(function ($q) {
                $q->whereNull('usage_limit')->orWhereRaw('usage_count < usage_limit');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        if (Auth::check()) {
            $student = Auth::user();
            $coupons = $coupons->filter(function ($coupon) use ($student) {
                return $coupon->canBeUsedByUser($student);
            });
        }

        return view('coupons.index', compact('coupons', 'customerType'));
    }

    public function show(Coupon $coupon)
    {
        if (!$coupon->is_public || !$coupon->is_active) {
            abort(404);
        }

        if (!$coupon->isValid()) {
            abort(404, 'This coupon is no longer available.');
        }

        $customerType = $this->determineCustomerType();
        if ($coupon->customer_type !== 'all' && $coupon->customer_type !== $customerType) {
            abort(403, 'This coupon is not available for your account type.');
        }

        return view('coupons.show', compact('coupon'));
    }

    public function apply(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50',
        ]);

        try {
            DB::beginTransaction();

            $code = strtoupper(trim($request->code));
            $coupon = Coupon::where('code', $code)->first();

            if (!$coupon) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid coupon code.',
                ], 404);
            }

            if (!$coupon->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This coupon is not currently valid or has expired.',
                ], 422);
            }

            $cartData = $this->getCartData();

            if ($cartData['total'] == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your cart is empty.',
                ], 422);
            }

            if (!$coupon->canBeAppliedTo($cartData['total'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'This coupon requires a minimum purchase of $' . number_format($coupon->montant_minimum, 2),
                ], 422);
            }

            $customerType = $this->determineCustomerType();
            if (!$coupon->isEligibleForCustomer($customerType)) {
                return response()->json([
                    'success' => false,
                    'message' => "This coupon is only available for {$coupon->customer_type} customers.",
                ], 422);
            }

            if (Auth::check() && !$coupon->canStudentUse(Auth::id())) {
                $maxUses = $coupon->max_uses_per_user;
                return response()->json([
                    'success' => false,
                    'message' => "You have already used this coupon the maximum number of times ({$maxUses}).",
                ], 422);
            }

            if ($coupon->min_items && $cartData['items_count'] < $coupon->min_items) {
                return response()->json([
                    'success' => false,
                    'message' => "This coupon requires at least {$coupon->min_items} items in your cart.",
                ], 422);
            }

            $sessionId = session()->getId();

            $existingActive = ActiveCartCoupon::where(function ($q) use ($sessionId) {
                $q->where('session_id', $sessionId);
                if (Auth::check()) {
                    $q->orWhere('student_id', Auth::id());
                }
            })
                ->where('coupon_id', $coupon->id)
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', Carbon::now());
                })
                ->first();

            if ($existingActive) {
                return response()->json([
                    'success' => false,
                    'message' => 'This coupon is already applied to your cart.',
                ], 422);
            }

            $existingCoupons = ActiveCartCoupon::where(function ($q) use ($sessionId) {
                $q->where('session_id', $sessionId);
                if (Auth::check()) {
                    $q->orWhere('student_id', Auth::id());
                }
            })
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', Carbon::now());
                })
                ->with('coupon')
                ->get();

            if ($existingCoupons->isNotEmpty()) {
                foreach ($existingCoupons as $existing) {
                    if (!$coupon->canBeStackedWith($existing->coupon)) {
                        return response()->json([
                            'success' => false,
                            'message' => "This coupon cannot be stacked with '{$existing->coupon->code}'. Please remove it first.",
                        ], 422);
                    }
                }
            }

            $discountAmount = $coupon->calculateDiscount($cartData['total']);

            ActiveCartCoupon::create([
                'session_id' => $sessionId,
                'student_id' => Auth::id(),
                'coupon_id' => $coupon->id,
                'cart_total' => $cartData['total'],
                'discount_amount' => $discountAmount,
                'original_cart_total' => $cartData['total'],
                'applied_at' => Carbon::now(),
                'expires_at' => Carbon::now()->addHours(24),
            ]);

            $allDiscounts = ActiveCartCoupon::where(function ($q) use ($sessionId) {
                $q->where('session_id', $sessionId);
                if (Auth::check()) {
                    $q->orWhere('student_id', Auth::id());
                }
            })
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', Carbon::now());
                })
                ->sum('discount_amount');

            $finalTotal = max(0, $cartData['total'] - $allDiscounts);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Coupon '{$coupon->code}' applied successfully!",
                'data' => [
                    'coupon' => [
                        'id' => $coupon->id,
                        'code' => $coupon->code,
                        'nom' => $coupon->nom,
                        'discount_amount' => $discountAmount,
                        'formatted_discount' => '$' . number_format($discountAmount, 2),
                    ],
                    'cart' => [
                        'original_total' => $cartData['total'],
                        'formatted_original_total' => '$' . number_format($cartData['total'], 2),
                        'total_discount' => $allDiscounts,
                        'formatted_total_discount' => '$' . number_format($allDiscounts, 2),
                        'final_total' => $finalTotal,
                        'formatted_final_total' => '$' . number_format($finalTotal, 2),
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to apply coupon: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function remove(Request $request, $couponId)
    {
        try {
            $sessionId = session()->getId();

            $activeCartCoupon = ActiveCartCoupon::where('coupon_id', $couponId)
                ->where(function ($q) use ($sessionId) {
                    $q->where('session_id', $sessionId);
                    if (Auth::check()) {
                        $q->orWhere('student_id', Auth::id());
                    }
                })
                ->first();

            if (!$activeCartCoupon) {
                return response()->json([
                    'success' => false,
                    'message' => 'Coupon not found in cart.',
                ], 404);
            }

            $couponCode = $activeCartCoupon->coupon->code;
            $activeCartCoupon->delete();

            $cartData = $this->getCartData();
            $remainingDiscounts = ActiveCartCoupon::where(function ($q) use ($sessionId) {
                $q->where('session_id', $sessionId);
                if (Auth::check()) {
                    $q->orWhere('student_id', Auth::id());
                }
            })
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', Carbon::now());
                })
                ->sum('discount_amount');

            $finalTotal = max(0, $cartData['total'] - $remainingDiscounts);

            return response()->json([
                'success' => true,
                'message' => "Coupon '{$couponCode}' removed successfully.",
                'data' => [
                    'cart' => [
                        'original_total' => $cartData['total'],
                        'formatted_original_total' => '$' . number_format($cartData['total'], 2),
                        'total_discount' => $remainingDiscounts,
                        'formatted_total_discount' => '$' . number_format($remainingDiscounts, 2),
                        'final_total' => $finalTotal,
                        'formatted_final_total' => '$' . number_format($finalTotal, 2),
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove coupon: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getActiveCoupons()
    {
        $sessionId = session()->getId();

        $activeCoupons = ActiveCartCoupon::where(function ($q) use ($sessionId) {
            $q->where('session_id', $sessionId);
            if (Auth::check()) {
                $q->orWhere('student_id', Auth::id());
            }
        })
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', Carbon::now());
            })
            ->with('coupon')
            ->orderBy('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $activeCoupons->map(function ($active) {
                return [
                    'id' => $active->coupon->id,
                    'code' => $active->coupon->code,
                    'nom' => $active->coupon->nom,
                    'discount_amount' => $active->discount_amount,
                    'formatted_discount' => '$' . number_format($active->discount_amount, 2),
                    'applied_at' => $active->applied_at->format('Y-m-d H:i:s'),
                    'expires_at' => $active->expires_at ? $active->expires_at->format('Y-m-d H:i:s') : null,
                ];
            }),
        ]);
    }

    public function checkAutoApply()
    {
        $cartData = $this->getCartData();

        if ($cartData['total'] == 0) {
            return response()->json([
                'success' => true,
                'data' => [],
            ]);
        }

        $customerType = $this->determineCustomerType();

        $autoApplyCoupons = Coupon::where('auto_apply', true)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('date_debut')->orWhere('date_debut', '<=', Carbon::now());
            })
            ->where(function ($q) {
                $q->whereNull('date_fin')->orWhere('date_fin', '>=', Carbon::now());
            })
            ->where(function ($q) use ($customerType) {
                $q->where('customer_type', 'all')->orWhere('customer_type', $customerType);
            })
            ->where(function ($q) {
                $q->whereNull('usage_limit')->orWhereRaw('usage_count < usage_limit');
            })
            ->orderBy('stack_priority', 'desc')
            ->get()
            ->filter(function ($coupon) use ($cartData) {
                if (!$coupon->checkAutoApplyConditions($cartData)) {
                    return false;
                }

                if (Auth::check() && !$coupon->canStudentUse(Auth::id())) {
                    return false;
                }

                $sessionId = session()->getId();
                $alreadyApplied = ActiveCartCoupon::where('coupon_id', $coupon->id)
                    ->where(function ($q) use ($sessionId) {
                        $q->where('session_id', $sessionId);
                        if (Auth::check()) {
                            $q->orWhere('student_id', Auth::id());
                        }
                    })
                    ->exists();

                return !$alreadyApplied;
            });

        return response()->json([
            'success' => true,
            'data' => $autoApplyCoupons->map(function ($coupon) use ($cartData) {
                return [
                    'id' => $coupon->id,
                    'code' => $coupon->code,
                    'nom' => $coupon->nom,
                    'description' => $coupon->description,
                    'discount_amount' => $coupon->calculateDiscount($cartData['total']),
                    'formatted_discount' => '$' . number_format($coupon->calculateDiscount($cartData['total']), 2),
                ];
            })->values(),
        ]);
    }

    public function validate(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50',
        ]);

        $code = strtoupper(trim($request->code));
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid coupon code.',
            ]);
        }

        if (!$coupon->isValid()) {
            return response()->json([
                'valid' => false,
                'message' => 'This coupon is not currently valid or has expired.',
            ]);
        }

        $cartData = $this->getCartData();

        if (!$coupon->canBeAppliedTo($cartData['total'])) {
            return response()->json([
                'valid' => false,
                'message' => 'Requires minimum purchase of $' . number_format($coupon->montant_minimum, 2),
            ]);
        }

        $customerType = $this->determineCustomerType();
        if (!$coupon->isEligibleForCustomer($customerType)) {
            return response()->json([
                'valid' => false,
                'message' => "Only available for {$coupon->customer_type} customers.",
            ]);
        }

        if (Auth::check() && !$coupon->canStudentUse(Auth::id())) {
            return response()->json([
                'valid' => false,
                'message' => 'You have reached the maximum uses for this coupon.',
            ]);
        }

        $discount = $coupon->calculateDiscount($cartData['total']);

        return response()->json([
            'valid' => true,
            'message' => 'Coupon is valid!',
            'coupon' => [
                'code' => $coupon->code,
                'nom' => $coupon->nom,
                'description' => $coupon->description,
                'discount_amount' => $discount,
                'formatted_discount' => '$' . number_format($discount, 2),
                'is_stackable' => $coupon->is_stackable,
            ],
        ]);
    }

    private function getCartData(): array
    {
        $total = 0;
        $itemsCount = 0;
        $courseTypes = [];

        if (Auth::check()) {
            $cartItems = Cart::where('student_id', Auth::id())->with(['product', 'book', 'package'])->get();

            foreach ($cartItems as $item) {
                $total += $item->total_price;
                $itemsCount += $item->quantity;

                if ($item->product) {
                    $courseTypes[] = $item->product->type ?? 'general';
                }
            }
        } else {
            $sessionCart = session('cart', []);

            foreach ($sessionCart as $item) {
                $total += $item['quantity'] * $item['price'];
                $itemsCount += $item['quantity'];
            }
        }

        return [
            'total' => $total,
            'items_count' => $itemsCount,
            'course_types' => array_unique($courseTypes),
        ];
    }

    private function determineCustomerType(): string
    {
        if (!Auth::check()) {
            return 'new';
        }

        $student = Auth::user();
        $orderCount = Order::where('student_id', $student->id)->count();

        if ($orderCount === 0) {
            return 'new';
        }

        if ($orderCount < 5) {
            return 'returning';
        }

        if (isset($student->is_vip) && $student->is_vip) {
            return 'vip';
        }

        return 'returning';
    }
}