<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Country;
use App\Models\Coupon;
use App\Models\Order;
use App\Services\CouponService;
use App\Services\StripeService;
use App\Services\PointsService;
use App\Services\CurrencyService;
use App\Services\ReferralDiscountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Mail\OrderConfirmationEmail;
use App\Mail\AdminNewPurchaseNotification;
use App\Models\Notification;

class CheckoutService
{
    protected $couponService;
    protected $stripeService;
    protected $pointsService;
    protected $currencyService;

    public function __construct(CouponService $couponService, StripeService $stripeService, PointsService $pointsService, CurrencyService $currencyService)
    {
        $this->couponService = $couponService;
        $this->stripeService = $stripeService;
        $this->pointsService = $pointsService;
        $this->currencyService = $currencyService;
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

        // Fallback: some courses still store duration text in "period" (e.g. "5 months").
        if ($validityMonths < 1) {
            $validityMonths = $this->extractMonthsFromPeriod($product->period ?? null);
        }

        if ($validityMonths < 1) {
            return null;
        }

        return now()->addMonths($validityMonths);
    }

    private function applyReferralDiscountToCart($cartItems, ?int $studentId)
    {
        $pct = ReferralDiscountService::percentFor($studentId);
        if ($pct > 0) {
            $factor = 1 - $pct / 100;
            foreach ($cartItems as $item) {
                $item->price = round(((float) $item->price) * $factor, 2);
            }
        }

        $this->applyReferralCreditToCart($cartItems, $studentId);

        return $cartItems;
    }

    /**
     * Apply available referral credit balance as a per-item discount,
     * distributed proportionally to item subtotals. Stores the planned
     * consumption in the session so it can be redeemed after order creation.
     */
    private function applyReferralCreditToCart($cartItems, ?int $studentId): float
    {
        if (!$studentId) {
            return 0.0;
        }

        $balance = ReferralDiscountService::creditBalanceFor($studentId);
        if ($balance <= 0) {
            return 0.0;
        }

        $subtotal = 0.0;
        foreach ($cartItems as $item) {
            $subtotal += ((float) $item->price) * (int) ($item->quantity ?? 1);
        }
        if ($subtotal <= 0) {
            session()->forget('pending_referral_credit');
            return 0.0;
        }

        $applied = min($balance, $subtotal);
        $factor = ($subtotal - $applied) / $subtotal;

        foreach ($cartItems as $item) {
            $item->price = round(((float) $item->price) * $factor, 2);
        }

        session(['pending_referral_credit' => round($applied, 2)]);

        return round($applied, 2);
    }

    private function processReferralReward(int $studentId, ?int $orderId): void
    {
        if (!$orderId) {
            return;
        }

        try {
            $adminUrl = env('ADMIN_URL', 'http://localhost:8100');
            Http::timeout(5)->post($adminUrl . '/api/referrals/process-reward', [
                'student_id' => $studentId,
                'order_id'   => $orderId,
            ]);
        } catch (\Exception $e) {
            Log::error('Referral reward processing request failed: ' . $e->getMessage());
        }

        $this->consumePendingReferralCredit($studentId, $orderId);
    }

    private function consumePendingReferralCredit(int $studentId, ?int $orderId): void
    {
        if (!$orderId) {
            return;
        }

        $pending = (float) session('pending_referral_credit', 0);
        if ($pending <= 0) {
            return;
        }

        ReferralDiscountService::consumeCreditForOrder($studentId, $orderId, $pending);
        session()->forget('pending_referral_credit');
    }

    private function markAbandonedCartAsConverted()
    {
        if (Auth::check()) {
            try {
                $adminUrl = env('ADMIN_URL', 'http://localhost:8100');
                $studentId = Auth::id();

                Log::info("Attempting to mark abandoned cart as converted for student ID: {$studentId}");

                $response = Http::timeout(5)->post($adminUrl . '/api/abandoned-carts/mark-converted', [
                    'student_id' => $studentId
                ]);

                if ($response->successful()) {
                    Log::info("Successfully marked abandoned cart as converted for student ID: {$studentId}");
                } else {
                    Log::warning("Failed to mark abandoned cart as converted. Status: {$response->status()}, Response: " . $response->body());
                }
            } catch (\Exception $e) {
                Log::error('Failed to mark abandoned cart as converted: ' . $e->getMessage());
            }
        }
    }

    private function processPointsForOrder(float $totalAmount, array $orderIds = [])
    {
        if (!Auth::check()) {
            return;
        }

        $studentId = Auth::id();
        $orderId = !empty($orderIds) ? $orderIds[0] : null;

        try {
            $appliedPoints = session('applied_points', 0);
            if ($appliedPoints > 0) {
                $result = $this->pointsService->redeemPoints(
                    $studentId,
                    $appliedPoints,
                    $orderId,
                    "Points redeemed at checkout"
                );

                if ($result['success']) {
                    Log::info("Redeemed {$appliedPoints} points for student {$studentId}");
                }

                session()->forget('applied_points');
            }

            if ($totalAmount > 0) {
                $pointsEarned = $this->pointsService->awardPointsForPurchase(
                    $studentId,
                    $totalAmount,
                    $orderId,
                    "Points earned from purchase"
                );

                Log::info("Awarded {$pointsEarned} points to student {$studentId} for \${$totalAmount} purchase");
            }
        } catch (\Exception $e) {
            Log::error("Error processing points for order: " . $e->getMessage());
        }
    }

    public function index()
    {
        if (Auth::check()) {
            $cartItems = Cart::where('student_id', Auth::id())
                ->with(['book', 'product', 'package'])
                ->get()
                ->map(function ($item) {
                    if ($item->package) {
                        $item->price = $item->package->discounted_price ?? $item->price;
                    }
                    return $item;
                });

            $total = $cartItems->sum(function ($item) {
                return $item->price * $item->quantity;
            });
            $itemCount = $cartItems->sum('quantity');
        } else {
            $cartItems = collect();
            $sessionCart = session('cart', []);
            $total = 0;
            $itemCount = 0;

            foreach ($sessionCart as $itemKey => $item) {
                $parts = explode('_', $itemKey);
                if (count($parts) >= 2) {
                    $itemType = $parts[0];
                    $itemId = $parts[1];

                    if ($itemType === 'book') {
                        $book = \App\Models\Book::find($itemId);
                        if ($book) {
                            $price = $item['price'];
                            $cartItem = (object) [
                                'id' => $itemKey,
                                'book' => $book,
                                'product' => null,
                                'package' => null,
                                'quantity' => $item['quantity'],
                                'price' => $price,
                                'formatted_price' => '$' . number_format($price, 2)
                            ];
                            $cartItems->push($cartItem);
                            $total += $item['quantity'] * $price;
                            $itemCount += $item['quantity'];
                        }
                    } elseif ($itemType === 'course') {
                        $product = \App\Models\Product::find($itemId);
                        if ($product) {
                            $price = $item['price'];
                            $cartItem = (object) [
                                'id' => $itemKey,
                                'book' => null,
                                'product' => $product,
                                'package' => null,
                                'quantity' => $item['quantity'],
                                'price' => $price,
                                'formatted_price' => '$' . number_format($price, 2)
                            ];
                            $cartItems->push($cartItem);
                            $total += $item['quantity'] * $price;
                            $itemCount += $item['quantity'];
                        }
                    } elseif ($itemType === 'package') {
                        $package = \App\Models\Package::find($itemId);
                        if ($package) {
                            $price = $package->discounted_price ?? $item['price'];
                            $cartItem = (object) [
                                'id' => $itemKey,
                                'book' => null,
                                'product' => null,
                                'package' => $package,
                                'quantity' => $item['quantity'],
                                'price' => $price,
                                'formatted_price' => '$' . number_format($price, 2)
                            ];
                            $cartItems->push($cartItem);
                            $total += $item['quantity'] * $price;
                            $itemCount += $item['quantity'];
                        }
                    }
                }
            }
        }

        if ($cartItems->count() == 0) {
            return redirect()->route('cart.index')->with('error', __('messages.cart_empty'));
        }

        $countries = Country::all();
        $user = Auth::user();

        $referralPct = Auth::check() ? ReferralDiscountService::percentFor(Auth::id()) : 0.0;
        $referralDiscount = ReferralDiscountService::amountFor(Auth::id(), $total);
        $totalAfterReferral = max(0, $total - $referralDiscount);

        $referralCreditBalance = Auth::check() ? ReferralDiscountService::creditBalanceFor(Auth::id()) : 0.0;
        $referralCreditApplied = min($referralCreditBalance, $totalAfterReferral);
        $totalAfterReferral = max(0, $totalAfterReferral - $referralCreditApplied);
        if ($referralCreditApplied > 0) {
            session(['pending_referral_credit' => round($referralCreditApplied, 2)]);
        } else {
            session()->forget('pending_referral_credit');
        }

        $appliedCoupons = session('applied_coupons', []);
        $appliedCoupon = session('applied_coupon');
        $totalDiscount = 0;
        $finalTotal = $totalAfterReferral;

        if (!empty($appliedCoupons) && Auth::check()) {
            $validCoupons = [];

            foreach ($appliedCoupons as $couponData) {
                $couponInstance = Coupon::find($couponData['id']);

                if ($couponInstance) {
                    $cartData = [
                        'total' => $totalAfterReferral,
                        'item_count' => $itemCount,
                        'course_types' => $cartItems->pluck('product.type')->filter()->unique()->toArray(),
                        'product_ids' => $cartItems->pluck('product_id')->filter()->toArray(),
                    ];

                    $validationResult = $this->couponService->applyCouponToCart(
                        $couponInstance->code,
                        Auth::user(),
                        $cartData
                    );

                    if ($validationResult['success']) {
                        $validCoupons[] = [
                            'id' => $couponInstance->id,
                            'code' => $couponInstance->code,
                            'description' => $couponInstance->description,
                            'discount_amount' => $validationResult['discount_amount'],
                        ];
                        $totalDiscount += $validationResult['discount_amount'];
                    }
                }
            }

            if (!empty($validCoupons)) {
                session(['applied_coupons' => $validCoupons]);
                session(['applied_coupon' => end($validCoupons)]);
                $appliedCoupons = $validCoupons;
                $appliedCoupon = end($validCoupons);
            } else {
                session()->forget(['applied_coupons', 'applied_coupon']);
                $appliedCoupons = [];
                $appliedCoupon = null;
            }

            $finalTotal = $totalAfterReferral - $totalDiscount;
        } elseif ($appliedCoupon && Auth::check()) {
            $couponInstance = Coupon::find($appliedCoupon['id']);

            if ($couponInstance) {
                $cartData = [
                    'total' => $totalAfterReferral,
                    'item_count' => $itemCount,
                    'course_types' => $cartItems->pluck('product.type')->filter()->unique()->toArray(),
                    'product_ids' => $cartItems->pluck('product_id')->filter()->toArray(),
                ];

                $validationResult = $this->couponService->applyCouponToCart(
                    $couponInstance->code,
                    Auth::user(),
                    $cartData
                );

                if ($validationResult['success']) {
                    $totalDiscount = $validationResult['discount_amount'];
                    $finalTotal = $totalAfterReferral - $totalDiscount;
                } else {
                    session()->forget(['applied_coupon', 'applied_coupons']);
                    $appliedCoupon = null;
                }
            } else {
                session()->forget(['applied_coupon', 'applied_coupons']);
                $appliedCoupon = null;
            }
        } elseif (!empty($appliedCoupons)) {
            foreach ($appliedCoupons as $couponData) {
                $totalDiscount += $couponData['discount_amount'] ?? 0;
            }
            $finalTotal = $totalAfterReferral - $totalDiscount;
        } elseif ($appliedCoupon) {
            $totalDiscount = $appliedCoupon['discount_amount'] ?? 0;
            $finalTotal = $totalAfterReferral - $totalDiscount;
        }

        $isAuthenticated = Auth::check();
        $discountAmount = $totalDiscount;

        $pointsBalance = null;
        $pointsDiscount = 0;
        $appliedPoints = session('applied_points', 0);

        if ($isAuthenticated) {
            $pointsBalance = $this->pointsService->getPointsBalance(Auth::id());

            $maxPointsDiscount = min($pointsBalance['available_discount'], max(0, $finalTotal));
            $pointsBalance['max_discount_for_order'] = $maxPointsDiscount;
            $pointsBalance['max_points_for_order'] = PointsService::pointsNeededForDiscount($maxPointsDiscount);

            if ($appliedPoints > 0) {
                $pointsDiscount = min(
                    PointsService::calculateDiscount($appliedPoints),
                    $maxPointsDiscount
                );
                $finalTotal -= $pointsDiscount;
            }
        }

        $isFreeCheckout = $finalTotal <= 0;

        return view('checkout.index', compact(
            'cartItems', 'total', 'itemCount', 'countries', 'user',
            'appliedCoupon', 'appliedCoupons', 'discountAmount', 'finalTotal',
            'isAuthenticated', 'isFreeCheckout', 'pointsBalance', 'pointsDiscount', 'appliedPoints',
            'referralDiscount', 'referralPct',
            'referralCreditBalance', 'referralCreditApplied'
        ));
    }

    public function process(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', __('messages.login_required_checkout'));
        }

        Log::info('Checkout process started', [
            'student_id' => Auth::id(),
            'payment_method' => $request->payment_method
        ]);

        $request->validate([
            'first_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\-\']+$/'],
            'last_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\-\']+$/'],
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'zip_code' => 'required|string|max:20',
            'payment_method' => 'nullable|in:bank_transfer,paypal,cash_on_delivery,credit_card,free,installment',
            'notes' => 'nullable|string|max:1000',
            'payment_description' => 'nullable|string|max:1000',
            'payment_receipt' => 'required_if:payment_method,bank_transfer|nullable|file|mimes:jpg,jpeg,png,pdf|max:5120'
        ], [
            'first_name.regex' => 'First name must contain only English letters.',
            'last_name.regex' => 'Last name must contain only English letters.',
        ]);

        $student = Auth::user();
        if ($student) {
            $addressUpdated = false;

            if ($request->phone && $student->phone !== $request->phone) {
                $student->phone = $request->phone;
                $addressUpdated = true;
            }

            if (empty($student->address) && $request->address) {
                $student->address = $request->address;
                $addressUpdated = true;
            }
            if (empty($student->city) && $request->city) {
                $student->city = $request->city;
                $addressUpdated = true;
            }
            if (empty($student->zip_code) && $request->zip_code) {
                $student->zip_code = $request->zip_code;
                $addressUpdated = true;
            }
            if (empty($student->country) && $request->country) {
                $student->country = $request->country;
                $addressUpdated = true;
            }

            if ($addressUpdated) {
                $student->save();
            }
        }

        $cartItems = Cart::where('student_id', Auth::id())
            ->with(['book', 'product', 'package'])
            ->get();
        $this->applyReferralDiscountToCart($cartItems, Auth::id());

        $originalTotal = $cartItems->sum('price');

        $appliedCoupons = session('applied_coupons', []);
        $appliedCoupon = session('applied_coupon');
        $totalDiscount = 0;

        if (!empty($appliedCoupons)) {
            foreach ($appliedCoupons as $couponData) {
                $totalDiscount += $couponData['discount_amount'] ?? 0;
            }
        } elseif ($appliedCoupon) {
            $totalDiscount = $appliedCoupon['discount_amount'] ?? 0;
        }

        $finalTotal = $originalTotal - $totalDiscount;

        if ($finalTotal <= 0) {
            return $this->processFreeOrder($request);
        }

        if ($request->payment_method === 'installment') {
            if ($cartItems->count() !== 1) {
                return redirect()->route('checkout.index')
                    ->with('error', __('Installment payment is only available for single course purchases.'));
            }

            $item = $cartItems->first();
            $product = $item->product;

            if (!$product || !$product->installment_allowed || !$product->validity_months) {
                return redirect()->route('checkout.index')
                    ->with('error', __('This product does not support installment payments.'));
            }

            return redirect()->route('student-dashboard.installments.checkout', ['product' => $product->id]);
        }

        if ($request->payment_method === 'credit_card') {
            return $this->processStripeCheckout($request);
        }

        if ($request->payment_method === 'bank_transfer') {
            return $this->processBankTransfer($request);
        }

        if ($request->payment_method === 'cash_on_delivery') {
            return $this->processCashOnDelivery($request);
        }

        if (Auth::check()) {
            $cartItems = Cart::where('student_id', Auth::id())
                ->with(['book', 'product', 'package'])
                ->get();
            $this->applyReferralDiscountToCart($cartItems, Auth::id());

            $appliedCoupons = session('applied_coupons', []);
            $appliedCoupon = session('applied_coupon');
            $totalDiscount = 0;
            $originalTotal = $cartItems->sum('price');
            $validCoupons = [];

            if (!empty($appliedCoupons)) {
                foreach ($appliedCoupons as $couponData) {
                    $couponInstance = Coupon::find($couponData['id']);

                    if ($couponInstance) {
                        $cartData = [
                            'total' => $originalTotal,
                            'item_count' => $cartItems->sum('quantity'),
                            'course_types' => $cartItems->pluck('product.type')->filter()->unique()->toArray(),
                            'product_ids' => $cartItems->pluck('product_id')->filter()->toArray(),
                        ];

                        $validationResult = $this->couponService->applyCouponToCart(
                            $couponInstance->code,
                            Auth::user(),
                            $cartData
                        );

                        if ($validationResult['success']) {
                            $validCoupons[] = [
                                'instance' => $couponInstance,
                                'discount' => $validationResult['discount_amount'],
                                'data' => $couponData
                            ];
                            $totalDiscount += $validationResult['discount_amount'];
                        }
                    }
                }

                if (count($validCoupons) < count($appliedCoupons)) {
                    session()->forget(['applied_coupons', 'applied_coupon']);
                    return redirect()->route('checkout.index')
                        ->with('error', __('messages.coupon_removed_invalid'));
                }
            } elseif ($appliedCoupon) {
                $couponInstance = Coupon::find($appliedCoupon['id']);

                if ($couponInstance) {
                    $cartData = [
                        'total' => $originalTotal,
                        'item_count' => $cartItems->sum('quantity'),
                        'course_types' => $cartItems->pluck('product.type')->filter()->unique()->toArray(),
                        'product_ids' => $cartItems->pluck('product_id')->filter()->toArray(),
                    ];

                    $validationResult = $this->couponService->applyCouponToCart(
                        $couponInstance->code,
                        Auth::user(),
                        $cartData
                    );

                    if (!$validationResult['success']) {
                        session()->forget(['applied_coupon', 'applied_coupons']);
                        return redirect()->route('checkout.index')
                            ->with('error', __('messages.coupon_removed_invalid') . ' ' . $validationResult['message']);
                    }

                    $validCoupons[] = [
                        'instance' => $couponInstance,
                        'discount' => $validationResult['discount_amount'],
                        'data' => $appliedCoupon
                    ];
                    $totalDiscount = $validationResult['discount_amount'];
                } else {
                    session()->forget(['applied_coupon', 'applied_coupons']);
                    return redirect()->route('checkout.index')
                        ->with('error', __('messages.coupon_no_longer_available'));
                }
            }

            $discountAmount = $totalDiscount;

            $eligibleProductIds = [];
            if (!empty($validCoupons)) {
                foreach ($validCoupons as $validCoupon) {
                    $couponProducts = $validCoupon['instance']->detailles()->pluck('product_id')->toArray();
                    if (empty($couponProducts)) {
                        $currentEligible = $cartItems->pluck('product_id')->filter()->toArray();
                    } else {
                        $currentEligible = $couponProducts;
                    }

                    $eligibleProductIds = array_unique(array_merge($eligibleProductIds, $currentEligible));
                }
            }

            $eligibleTotal = 0;
            if (!empty($eligibleProductIds)) {
                foreach ($cartItems as $item) {
                    if ($item->product_id && in_array($item->product_id, $eligibleProductIds)) {
                        $eligibleTotal += $item->price;
                    }
                }
            }

            $discountRatio = $eligibleTotal > 0 ? $discountAmount / $eligibleTotal : 0;

            $appliedPoints = session('applied_points', 0);
            $pointsDiscount = 0;
            if ($appliedPoints > 0) {
                $pointsDiscount = PointsService::calculateDiscount($appliedPoints);
            }
            $afterCouponTotal = $originalTotal - $discountAmount;
            $pointsDiscountRatio = ($afterCouponTotal > 0 && $pointsDiscount > 0) ? $pointsDiscount / $afterCouponTotal : 0;

            $student = Auth::user();
            $emailItems = [];
            $totalAmount = 0;

            foreach ($cartItems as $cartItem) {
                $itemDiscount = 0;
                if ($cartItem->product_id && in_array($cartItem->product_id, $eligibleProductIds)) {
                    $itemDiscount = $cartItem->price * $discountRatio;
                }
                $priceAfterCoupon = $cartItem->price - $itemDiscount;

                $itemPointsDiscount = $priceAfterCoupon * $pointsDiscountRatio;
                $finalPrice = max(0, $priceAfterCoupon - $itemPointsDiscount);

                $orderData = [
                    'student_id' => Auth::id(),
                    'price' => $finalPrice,
                    'payment_success' => true,
                    'payment_method' => $request->payment_method,
                    'notes' => $request->notes,
                    'points_used' => $appliedPoints,
                    'points_discount' => $pointsDiscount,
                    'points_processed' => true,
                ];

                if ($cartItem->book_id) {
                    $orderData['book_id'] = $cartItem->book_id;

                    $book = $cartItem->book;
                    $emailItems[] = [
                        'type' => 'book',
                        'title' => $book?->titre,
                        'author' => $book?->auteur ?? null,
                        'quantity' => $cartItem->quantity,
                        'unit_price' => $finalPrice,
                        'total_price' => $finalPrice * $cartItem->quantity
                    ];

                    $totalAmount += $finalPrice * $cartItem->quantity;
                    $order = \App\Models\Order::create($orderData);
                } elseif ($cartItem->package_id) {
                    $package = $cartItem->package ?? \App\Models\Package::find($cartItem->package_id);

                    if ($package) {
                        $packageProducts = $package->packageProducts()->where('is_active', true)->with('product')->get();
                        $courseCount = $packageProducts->count();

                        if ($courseCount > 0) {
                            $pricePerCourse = $finalPrice / $courseCount;

                            foreach ($packageProducts as $packageProduct) {
                                if ($packageProduct->product) {
                                    $product = $packageProduct->product;

                                    $courseOrderData = [
                                        'student_id' => Auth::id(),
                                        'product_id' => $product->id,
                                        'price' => $pricePerCourse,
                                        'payment_success' => true,
                                        'payment_method' => $request->payment_method,
                                        'notes' => $request->notes
                                    ];

                                    $order = \App\Models\Order::create($courseOrderData);

                                    $expirationDate = $this->resolveCourseExpirationDate($product);

                                    \App\Models\ProductStudent::updateOrCreate(
                                        [
                                            'product_id' => $product->id,
                                            'student_id' => Auth::id()
                                        ],
                                        [
                                            'date' => now()->toDateString(),
                                            'is_active' => true,
                                            'access_granted_at' => now(),
                                            'expiration_date' => $expirationDate,
                                            'is_expired' => false
                                        ]
                                    );

                                    $emailItems[] = [
                                        'type' => 'course',
                                        'title' => $product->titre,
                                        'author' => $product->teacher ? $product->teacher->prenom . ' ' . $product->teacher->nom : null,
                                        'quantity' => $cartItem->quantity,
                                        'unit_price' => $pricePerCourse,
                                        'total_price' => $pricePerCourse * $cartItem->quantity
                                    ];

                                    $totalAmount += $pricePerCourse * $cartItem->quantity;
                                }
                            }
                        }
                    }
                    continue;
                } else {
                    $orderData['product_id'] = $cartItem->product_id;

                    $product = $cartItem->product ?? \App\Models\Product::find($cartItem->product_id);
                    if (!$product) {
                        continue;
                    }

                    $emailItems[] = [
                        'type' => 'course',
                        'title' => $product->titre,
                        'author' => $product->teacher ? $product->teacher->prenom . ' ' . $product->teacher->nom : null,
                        'quantity' => $cartItem->quantity,
                        'unit_price' => $finalPrice,
                        'total_price' => $finalPrice * $cartItem->quantity
                    ];

                    $totalAmount += $finalPrice * $cartItem->quantity;
                    $order = \App\Models\Order::create($orderData);
                }

                try {
                    Mail::to(config('mail.admin_email', env('ADMIN_EMAIL')))->send(
                        new AdminNewPurchaseNotification($order)
                    );
                } catch (\Exception $e) {
                    Log::error('Admin notification email error: ' . $e->getMessage());
                }

                try {
                    $student = Auth::user();
                    $itemName = '';
                    if ($order->product_id) {
                        $product = \App\Models\Product::find($order->product_id);
                        $itemName = $product->name_en ?? $product->name_ar ?? $product->titre;
                    } elseif ($order->package_id) {
                        $package = \App\Models\Package::find($order->package_id);
                        $itemName = $package->titre_en ?? $package->titre_ar ?? 'Package';
                    }

                    Notification::notifyAllAdmins(
                        Notification::TYPE_PURCHASE,
                        'New Purchase',
                        $student->first_name . ' ' . $student->last_name . ' purchased "' . $itemName . '" for $' . number_format((float)$order->price, 2),
                        config('app.admin_url') . '/orders/' . $order->id,
                        ['order_id' => $order->id, 'student_id' => $student->id, 'amount' => $order->price],
                        '🛒',
                        'green'
                    );
                } catch (\Exception $e) {
                    Log::error('Admin purchase notification creation error: ' . $e->getMessage());
                }

                if ($order->product_id) {
                    $product = \App\Models\Product::find($order->product_id);

                    $expirationDate = $this->resolveCourseExpirationDate($product);

                    \App\Models\ProductStudent::updateOrCreate(
                        [
                            'product_id' => $order->product_id,
                            'student_id' => Auth::id()
                        ],
                        [
                            'date' => now()->toDateString(),
                            'is_active' => true,
                            'access_granted_at' => now(),
                            'expiration_date' => $expirationDate,
                            'is_expired' => false
                        ]
                    );
                }
            }

            if ($request->filled('first_name') && $request->filled('last_name')) {
                $student->update([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                ]);
            }

            if ($appliedCoupon && $discountAmount > 0) {
                $couponInstance = Coupon::find($appliedCoupon['id']);
                if ($couponInstance) {
                    $firstOrder = \App\Models\Order::where('student_id', Auth::id())
                        ->latest()
                        ->first();

                    if ($firstOrder) {
                        if (!empty($validCoupons)) {
                            foreach ($validCoupons as $validCoupon) {
                                $finalPrice = $firstOrder->price;
                                $couponDiscount = $validCoupon['discount'];
                                $originalPrice = $finalPrice + $totalDiscount;

                                $this->couponService->recordCouponUsage(
                                    $validCoupon['instance'],
                                    $student,
                                    (string) $firstOrder->id,
                                    $couponDiscount,
                                    $originalPrice,
                                    $finalPrice
                                );
                            }
                        }
                    }
                }

                session()->forget(['applied_coupon', 'applied_coupons']);
            }

            try {
                Mail::to($student->email)->send(
                    new OrderConfirmationEmail(
                        $student,
                        $emailItems,
                        $totalAmount,
                        now()->format('Y-m-d H:i'),
                        $request->payment_method,
                        $request->notes
                    )
                );
            } catch (\Exception $e) {
                Log::error('Email error: ' . $e->getMessage());
            }

            $this->markAbandonedCartAsConverted();
            $this->processPointsForOrder($totalAmount);
            $latestDirectOrder = \App\Models\Order::where('student_id', Auth::id())
                ->orderByDesc('id')->first();
            $this->processReferralReward(Auth::id(), $latestDirectOrder?->id);
            Cart::where('student_id', Auth::id())->delete();
        } else {
            session()->forget(['cart', 'cart_count']);
        }

        return redirect()->route('student-dashboard.index')->with('success', __('messages.order_placed_successfully'));
    }

    protected function processBankTransfer(Request $request)
    {
        try {
            Log::info('Processing bank transfer', ['student_id' => Auth::id()]);

            $cartItems = Cart::where('student_id', Auth::id())
                ->with(['book', 'product', 'package'])
                ->get();
            $this->applyReferralDiscountToCart($cartItems, Auth::id());

            if ($cartItems->isEmpty()) {
                Log::warning('Cart is empty for bank transfer', ['student_id' => Auth::id()]);
                return redirect()->route('checkout.index')->with('error', __('messages.cart_is_empty'));
            }

            $appliedCoupon = session('applied_coupon');
            $discountAmount = 0;
            $eligibleProductIds = [];
            $discountRatio = 0;

            if ($appliedCoupon) {
                $couponInstance = Coupon::find($appliedCoupon['id']);
                if ($couponInstance) {
                    $discountAmount = $appliedCoupon['discount_amount'] ?? 0;

                    $couponProducts = $couponInstance->detailles()->pluck('product_id')->toArray();
                    if (empty($couponProducts)) {
                        $eligibleProductIds = $cartItems->pluck('product_id')->filter()->toArray();
                    } else {
                        $eligibleProductIds = $couponProducts;
                    }

                    $eligibleTotal = 0;
                    foreach ($cartItems as $item) {
                        if ($item->product_id && in_array($item->product_id, $eligibleProductIds)) {
                            $eligibleTotal += $item->price;
                        }
                    }

                    $discountRatio = $eligibleTotal > 0 ? $discountAmount / $eligibleTotal : 0;
                }
            }

            $appliedPoints = session('applied_points', 0);
            $pointsDiscount = 0;
            $originalTotal = $cartItems->sum('price');
            if ($appliedPoints > 0) {
                $pointsDiscount = PointsService::calculateDiscount($appliedPoints);
            }
            $afterCouponTotal = $originalTotal - $discountAmount;
            $pointsDiscountRatio = ($afterCouponTotal > 0 && $pointsDiscount > 0) ? $pointsDiscount / $afterCouponTotal : 0;

            $receiptPath = null;
            if ($request->hasFile('payment_receipt')) {
                $file = $request->file('payment_receipt');
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $receiptPath = $file->storeAs('payment_receipts', $fileName, 'public');
            }

            $metadata = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'country' => $request->country,
                'city' => $request->city,
                'address' => $request->address,
                'zip_code' => $request->zip_code,
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
                'payment_description' => $request->payment_description,
                'payment_receipt' => $receiptPath,
            ];

            $createdOrders = [];
            $totalAmount = 0;

            foreach ($cartItems as $cartItem) {
                $itemDiscount = 0;
                if ($cartItem->product_id && in_array($cartItem->product_id, $eligibleProductIds)) {
                    $itemDiscount = $cartItem->price * $discountRatio;
                }
                $priceAfterCoupon = $cartItem->price - $itemDiscount;

                $itemPointsDiscount = $priceAfterCoupon * $pointsDiscountRatio;
                $finalPrice = max(0, $priceAfterCoupon - $itemPointsDiscount);

                $orderData = [
                    'student_id' => Auth::id(),
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'country' => $request->country,
                    'city' => $request->city,
                    'address' => $request->address,
                    'zip_code' => $request->zip_code,
                    'payment_method' => $request->payment_method,
                    'payment_success' => false,
                    'payment_description' => $request->payment_description,
                    'payment_receipt' => $receiptPath,
                    'payment_status' => 'pending',
                    'notes' => $request->notes,
                    'points_used' => $appliedPoints,
                    'points_discount' => $pointsDiscount,
                    'points_processed' => false,
                ];

                if ($cartItem->book_id) {
                    $orderData['book_id'] = $cartItem->book_id;
                    $orderData['price'] = $finalPrice;
                    $orderData['quantity'] = $cartItem->quantity ?? 1;
                    $order = Order::create($orderData);
                    $createdOrders[] = $order->id;
                    $totalAmount += $finalPrice * ($cartItem->quantity ?? 1);

                } elseif ($cartItem->package_id) {
                    $package = \App\Models\Package::find($cartItem->package_id);
                    if ($package) {
                        $courseCount = $package->products->count();
                        foreach ($package->products as $course) {
                            $courseOrderData = $orderData;
                            $courseOrderData['product_id'] = $course->id;
                            $courseOrderData['price'] = $courseCount > 0 ? $finalPrice / $courseCount : 0;
                            $courseOrderData['quantity'] = 1;
                            $order = Order::create($courseOrderData);
                            $createdOrders[] = $order->id;
                        }
                        $totalAmount += $finalPrice;
                    }

                } elseif ($cartItem->product_id) {
                    $orderData['product_id'] = $cartItem->product_id;
                    $orderData['price'] = $finalPrice;
                    $orderData['quantity'] = $cartItem->quantity ?? 1;
                    $order = Order::create($orderData);
                    $createdOrders[] = $order->id;
                    $totalAmount += $finalPrice * ($cartItem->quantity ?? 1);
                }
            }

            $student = Auth::user();
            if ($request->filled('first_name') && $request->filled('last_name')) {
                $student->update([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                ]);
            }

            \App\Models\Notification::notifyAllAdmins(
                \App\Models\Notification::TYPE_PURCHASE,
                'New Bank Transfer Order',
                "New order from {$student->first_name} {$student->last_name} - Total: $" . number_format($totalAmount, 2),
                env('ADMIN_URL') . '/orders',
                ['order_ids' => $createdOrders, 'amount' => $totalAmount],
                null,
                'warning'
            );

            try {
                if (!empty($createdOrders)) {
                    Mail::to(config('mail.admin_email', env('ADMIN_EMAIL')))->send(
                        new AdminNewPurchaseNotification($createdOrders)
                    );
                }
            } catch (\Exception $e) {
                Log::error('Admin notification email error: ' . $e->getMessage());
            }

            $emailItems = [];
            foreach ($cartItems as $item) {
                if ($item->book) {
                    $emailItems[] = [
                        'type' => 'book',
                        'title' => $item->book->titre,
                        'quantity' => $item->quantity ?? 1,
                        'unit_price' => $item->price,
                        'total_price' => $item->price * ($item->quantity ?? 1)
                    ];
                } elseif ($item->product) {
                    $emailItems[] = [
                        'type' => 'course',
                        'title' => $item->product->titre,
                        'quantity' => 1,
                        'unit_price' => $item->price,
                        'total_price' => $item->price
                    ];
                } elseif ($item->package) {
                    $emailItems[] = [
                        'type' => 'package',
                        'title' => $item->package->title,
                        'quantity' => 1,
                        'unit_price' => $item->price,
                        'total_price' => $item->price
                    ];
                }
            }

            try {
                Mail::to($student->email)->send(
                    new \App\Mail\OrderConfirmationEmail(
                        $student,
                        $emailItems,
                        $totalAmount,
                        now()->format('Y-m-d H:i'),
                        'bank_transfer',
                        $request->notes
                    )
                );
            } catch (\Exception $e) {
                Log::error('Email error: ' . $e->getMessage());
            }

            $latestBankOrderId = !empty($createdOrders) ? $createdOrders[0] : null;
            $this->processReferralReward(Auth::id(), $latestBankOrderId);

            Cart::where('student_id', Auth::id())->delete();
            session()->forget(['applied_coupon', 'applied_coupons', 'applied_points']);

            Log::info('Bank transfer completed successfully', [
                'student_id' => Auth::id(),
                'orders_created' => count($createdOrders),
                'total_amount' => $totalAmount
            ]);

            return redirect()->route('student-dashboard.orders.index')
                ->with('info', __('messages.bank_transfer_pending_approval'));

        } catch (\Exception $e) {
            Log::error('Bank transfer processing error', [
                'error' => $e->getMessage(),
                'student_id' => Auth::id(),
            ]);

            return redirect()->route('checkout.index')
                ->with('error', __('messages.payment_error') . ' ' . $e->getMessage());
        }
    }

    protected function processCashOnDelivery(Request $request)
    {
        try {
            Log::info('Processing cash on delivery', ['student_id' => Auth::id()]);

            $cartItems = Cart::where('student_id', Auth::id())
                ->with(['book', 'product', 'package'])
                ->get();
            $this->applyReferralDiscountToCart($cartItems, Auth::id());

            if ($cartItems->isEmpty()) {
                Log::warning('Cart is empty for cash on delivery', ['student_id' => Auth::id()]);
                return redirect()->route('checkout.index')->with('error', __('messages.cart_is_empty'));
            }

            $appliedCoupon = session('applied_coupon');
            $discountAmount = 0;
            $eligibleProductIds = [];
            $discountRatio = 0;

            if ($appliedCoupon) {
                $couponInstance = Coupon::find($appliedCoupon['id']);
                if ($couponInstance) {
                    $discountAmount = $appliedCoupon['discount_amount'] ?? 0;

                    $couponProducts = $couponInstance->detailles()->pluck('product_id')->toArray();
                    if (empty($couponProducts)) {
                        $eligibleProductIds = $cartItems->pluck('product_id')->filter()->toArray();
                    } else {
                        $eligibleProductIds = $couponProducts;
                    }

                    $eligibleTotal = 0;
                    foreach ($cartItems as $item) {
                        if ($item->product_id && in_array($item->product_id, $eligibleProductIds)) {
                            $eligibleTotal += $item->price;
                        }
                    }

                    $discountRatio = $eligibleTotal > 0 ? $discountAmount / $eligibleTotal : 0;
                }
            }

            $appliedPoints = session('applied_points', 0);
            $pointsDiscount = 0;
            $originalTotal = $cartItems->sum('price');
            if ($appliedPoints > 0) {
                $pointsDiscount = PointsService::calculateDiscount($appliedPoints);
            }
            $afterCouponTotal = $originalTotal - $discountAmount;
            $pointsDiscountRatio = ($afterCouponTotal > 0 && $pointsDiscount > 0) ? $pointsDiscount / $afterCouponTotal : 0;

            $createdOrders = [];
            $totalAmount = 0;

            foreach ($cartItems as $cartItem) {
                $itemDiscount = 0;
                if ($cartItem->product_id && in_array($cartItem->product_id, $eligibleProductIds)) {
                    $itemDiscount = $cartItem->price * $discountRatio;
                }
                $priceAfterCoupon = $cartItem->price - $itemDiscount;

                $itemPointsDiscount = $priceAfterCoupon * $pointsDiscountRatio;
                $finalPrice = max(0, $priceAfterCoupon - $itemPointsDiscount);

                $orderData = [
                    'student_id' => Auth::id(),
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'country' => $request->country,
                    'city' => $request->city,
                    'address' => $request->address,
                    'zip_code' => $request->zip_code,
                    'payment_method' => 'cash_on_delivery',
                    'payment_success' => false,
                    'payment_status' => 'pending',
                    'notes' => $request->notes,
                    'points_used' => $appliedPoints,
                    'points_discount' => $pointsDiscount,
                    'points_processed' => false,
                ];

                if ($cartItem->book_id) {
                    $orderData['book_id'] = $cartItem->book_id;
                    $orderData['price'] = $finalPrice;
                    $orderData['quantity'] = $cartItem->quantity ?? 1;
                    $order = Order::create($orderData);
                    $createdOrders[] = $order->id;
                    $totalAmount += $finalPrice * ($cartItem->quantity ?? 1);

                } elseif ($cartItem->package_id) {
                    $package = \App\Models\Package::find($cartItem->package_id);
                    if ($package) {
                        $courseCount = $package->products->count();
                        foreach ($package->products as $course) {
                            $courseOrderData = $orderData;
                            $courseOrderData['product_id'] = $course->id;
                            $courseOrderData['price'] = $courseCount > 0 ? $finalPrice / $courseCount : 0;
                            $courseOrderData['quantity'] = 1;
                            $order = Order::create($courseOrderData);
                            $createdOrders[] = $order->id;
                        }
                        $totalAmount += $finalPrice;
                    }

                } elseif ($cartItem->product_id) {
                    $orderData['product_id'] = $cartItem->product_id;
                    $orderData['price'] = $finalPrice;
                    $orderData['quantity'] = $cartItem->quantity ?? 1;
                    $order = Order::create($orderData);
                    $createdOrders[] = $order->id;
                    $totalAmount += $finalPrice * ($cartItem->quantity ?? 1);
                }
            }

            $student = Auth::user();
            if ($request->filled('first_name') && $request->filled('last_name')) {
                $student->update([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                ]);
            }

            \App\Models\Notification::notifyAllAdmins(
                \App\Models\Notification::TYPE_PURCHASE,
                'New Cash on Delivery Order',
                "New COD order from {$student->first_name} {$student->last_name} - Total: $" . number_format($totalAmount, 2),
                env('ADMIN_URL') . '/orders',
                ['order_ids' => $createdOrders, 'amount' => $totalAmount],
                null,
                'warning'
            );

            try {
                if (!empty($createdOrders)) {
                    Mail::to(config('mail.admin_email', env('ADMIN_EMAIL')))->send(
                        new AdminNewPurchaseNotification($createdOrders)
                    );
                }
            } catch (\Exception $e) {
                Log::error('Admin notification email error: ' . $e->getMessage());
            }

            $emailItems = [];
            foreach ($cartItems as $item) {
                if ($item->book) {
                    $emailItems[] = [
                        'type' => 'book',
                        'title' => $item->book->titre,
                        'quantity' => $item->quantity ?? 1,
                        'unit_price' => $item->price,
                        'total_price' => $item->price * ($item->quantity ?? 1)
                    ];
                } elseif ($item->product) {
                    $emailItems[] = [
                        'type' => 'course',
                        'title' => $item->product->titre,
                        'quantity' => 1,
                        'unit_price' => $item->price,
                        'total_price' => $item->price
                    ];
                } elseif ($item->package) {
                    $emailItems[] = [
                        'type' => 'package',
                        'title' => $item->package->title,
                        'quantity' => 1,
                        'unit_price' => $item->price,
                        'total_price' => $item->price
                    ];
                }
            }

            try {
                Mail::to($student->email)->send(
                    new \App\Mail\OrderConfirmationEmail(
                        $student,
                        $emailItems,
                        $totalAmount,
                        now()->format('Y-m-d H:i'),
                        'cash_on_delivery',
                        $request->notes
                    )
                );
            } catch (\Exception $e) {
                Log::error('Email error: ' . $e->getMessage());
            }

            $latestCodOrderId = !empty($createdOrders) ? $createdOrders[0] : null;
            $this->processReferralReward(Auth::id(), $latestCodOrderId);

            $this->markAbandonedCartAsConverted();
            Cart::where('student_id', Auth::id())->delete();
            session()->forget(['applied_coupon', 'applied_coupons', 'applied_points']);

            Log::info('Cash on delivery completed successfully', [
                'student_id' => Auth::id(),
                'orders_created' => count($createdOrders),
                'total_amount' => $totalAmount
            ]);

            return redirect()->route('student-dashboard.orders.index')
                ->with('info', __('messages.cash_on_delivery_pending'));

        } catch (\Exception $e) {
            Log::error('Cash on delivery processing error', [
                'error' => $e->getMessage(),
                'student_id' => Auth::id(),
            ]);

            return redirect()->route('checkout.index')
                ->with('error', __('messages.payment_error') . ' ' . $e->getMessage());
        }
    }

    protected function processStripeCheckout(Request $request)
    {
        try {
            $cartItems = Cart::where('student_id', Auth::id())
                ->with(['book', 'product', 'package'])
                ->get();
            $this->applyReferralDiscountToCart($cartItems, Auth::id());

            if ($cartItems->isEmpty()) {
                return redirect()->route('checkout.index')->with('error', __('messages.cart_is_empty'));
            }

            $appliedCoupons = session('applied_coupons', []);
            $appliedCoupon = session('applied_coupon');
            $totalDiscount = 0;
            $originalTotal = $cartItems->sum('price');
            $validCoupons = [];

            if (!empty($appliedCoupons)) {
                foreach ($appliedCoupons as $couponData) {
                    $couponInstance = Coupon::find($couponData['id']);
                    if ($couponInstance) {
                        $cartData = [
                            'total' => $originalTotal,
                            'item_count' => $cartItems->sum('quantity'),
                            'course_types' => $cartItems->pluck('product.type')->filter()->unique()->toArray(),
                            'product_ids' => $cartItems->pluck('product_id')->filter()->toArray(),
                        ];
                        $validationResult = $this->couponService->applyCouponToCart(
                            $couponInstance->code,
                            Auth::user(),
                            $cartData
                        );
                        if ($validationResult['success']) {
                            $validCoupons[] = [
                                'instance' => $couponInstance,
                                'discount' => $validationResult['discount_amount'],
                                'data' => $couponData
                            ];
                            $totalDiscount += $validationResult['discount_amount'];
                        }
                    }
                }
            } elseif ($appliedCoupon) {
                $couponInstance = Coupon::find($appliedCoupon['id']);
                if ($couponInstance) {
                    $cartData = [
                        'total' => $originalTotal,
                        'item_count' => $cartItems->sum('quantity'),
                        'course_types' => $cartItems->pluck('product.type')->filter()->unique()->toArray(),
                        'product_ids' => $cartItems->pluck('product_id')->filter()->toArray(),
                    ];
                    $validationResult = $this->couponService->applyCouponToCart(
                        $couponInstance->code,
                        Auth::user(),
                        $cartData
                    );
                    if ($validationResult['success']) {
                        $validCoupons[] = [
                            'instance' => $couponInstance,
                            'discount' => $validationResult['discount_amount'],
                            'data' => $appliedCoupon
                        ];
                        $totalDiscount = $validationResult['discount_amount'];
                    }
                }
            }

            $eligibleProductIds = [];
            if (!empty($validCoupons)) {
                foreach ($validCoupons as $validCoupon) {
                    $couponProducts = $validCoupon['instance']->detailles()->pluck('product_id')->toArray();
                    if (empty($couponProducts)) {
                        $currentEligible = $cartItems->pluck('product_id')->filter()->toArray();
                    } else {
                        $currentEligible = $couponProducts;
                    }
                    $eligibleProductIds = array_unique(array_merge($eligibleProductIds, $currentEligible));
                }
            }

            $eligibleTotal = 0;
            if (!empty($eligibleProductIds)) {
                foreach ($cartItems as $item) {
                    if ($item->product_id && in_array($item->product_id, $eligibleProductIds)) {
                        $eligibleTotal += $item->price;
                    }
                }
            }
            $discountRatio = $eligibleTotal > 0 ? $totalDiscount / $eligibleTotal : 0;

            $appliedPoints = session('applied_points', 0);
            $pointsDiscount = 0;
            if ($appliedPoints > 0) {
                $pointsDiscount = PointsService::calculateDiscount($appliedPoints);
                $originalTotal = $cartItems->sum('price');
            }

            $selectedCurrency = $this->currencyService->getSelectedCurrency();
            $stripeCurrency = strtolower($selectedCurrency->code);

            $lineItems = [];
            $metadata = [
                'student_id' => Auth::id(),
                'customer_email' => Auth::user()->email,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'country' => $request->country,
                'city' => $request->city,
                'address' => $request->address,
                'zip_code' => $request->zip_code,
                'notes' => $request->notes ?? '',
            ];

            $cartItemsData = [];

            $afterCouponTotal = $originalTotal - $totalDiscount;
            $pointsDiscountRatio = ($afterCouponTotal > 0 && $pointsDiscount > 0) ? $pointsDiscount / $afterCouponTotal : 0;

            foreach ($cartItems as $cartItem) {
                $itemDiscount = 0;
                if ($cartItem->product_id && in_array($cartItem->product_id, $eligibleProductIds)) {
                    $itemDiscount = $cartItem->price * $discountRatio;
                }
                $priceAfterCoupon = $cartItem->price - $itemDiscount;

                $itemPointsDiscount = $priceAfterCoupon * $pointsDiscountRatio;
                $finalPrice = max(0, $priceAfterCoupon - $itemPointsDiscount);

                $itemName = '';
                $itemDescription = '';
                $itemImage = null;

                if ($cartItem->book) {
                    $itemName = $cartItem->book->titre;
                    $itemDescription = $cartItem->book->auteur ?? '';
                } elseif ($cartItem->product) {
                    $itemName = $cartItem->product->titre;
                    $itemDescription = $cartItem->product->sous_titre ?? '';
                    $itemImage = $cartItem->product->image ? url('storage/' . $cartItem->product->image) : null;
                } elseif ($cartItem->package) {
                    $itemName = $cartItem->package->title;
                    $itemDescription = $cartItem->package->description ?? '';
                }

                $convertedPrice = $this->currencyService->convert($finalPrice, $selectedCurrency->code);

                $lineItems[] = [
                    'name' => $itemName,
                    'description' => $itemDescription,
                    'price' => $convertedPrice,
                    'quantity' => $cartItem->quantity ?? 1,
                    'image' => $itemImage,
                ];

                $cartItemsData[] = [
                    'cart_item_id' => $cartItem->id,
                    'book_id' => $cartItem->book_id,
                    'product_id' => $cartItem->product_id,
                    'package_id' => $cartItem->package_id,
                    'price' => $finalPrice,
                    'quantity' => $cartItem->quantity ?? 1,
                ];
            }

            session(['pending_order_data' => [
                'cart_items' => $cartItemsData,
                'metadata' => $metadata,
                'discount_amount' => $totalDiscount,
                'applied_coupons' => $validCoupons,
                'points_used' => $appliedPoints,
                'points_discount' => $pointsDiscount,
                'display_currency' => $selectedCurrency->code,
                'exchange_rate' => $selectedCurrency->exchange_rate ?? 1,
            ]]);

            $successUrl = route('checkout.stripe.success', ['locale' => app()->getLocale()]) . '?session_id={CHECKOUT_SESSION_ID}';
            $cancelUrl = route('checkout.stripe.cancel', ['locale' => app()->getLocale()]);

            $session = $this->stripeService->createCheckoutSession(
                $lineItems,
                $metadata,
                $successUrl,
                $cancelUrl,
                $stripeCurrency
            );

            return redirect($session->url);

        } catch (\Exception $e) {
            Log::error('Stripe checkout error', [
                'error' => $e->getMessage(),
                'student_id' => Auth::id(),
            ]);

            return redirect()->route('checkout.index')
                ->with('error', __('messages.payment_error') . ' ' . $e->getMessage());
        }
    }

    public function stripeSuccess(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', __('messages.login_required_checkout'));
        }

        $sessionId = $request->query('session_id');
        if (!$sessionId) {
            return redirect()->route('checkout.index')->with('error', __('messages.invalid_payment_session'));
        }

        try {
            $stripeSession = $this->stripeService->retrieveSession($sessionId);

            if ($stripeSession->payment_status !== 'paid') {
                return redirect()->route('checkout.index')
                    ->with('error', __('messages.payment_not_completed'));
            }

            $pendingOrderData = session('pending_order_data');
            if (!$pendingOrderData) {
                Log::error('Pending order data not found in session', ['session_id' => $sessionId]);
                return redirect()->route('checkout.index')
                    ->with('error', __('messages.order_data_not_found'));
            }

            Log::info('Processing Stripe payment success', [
                'session_id' => $sessionId,
                'student_id' => Auth::id(),
                'cart_items_count' => count($pendingOrderData['cart_items']),
            ]);

            DB::beginTransaction();

            try {
                $student = Auth::user();
                $emailItems = [];
                $totalAmount = 0;

                foreach ($pendingOrderData['cart_items'] as $itemData) {
                    $orderData = [
                        'student_id' => Auth::id(),
                        'price' => $itemData['price'],
                        'payment_success' => true,
                        'payment_method' => 'credit_card',
                        'notes' => $pendingOrderData['metadata']['notes'] ?? null,
                        'stripe_session_id' => $sessionId,
                        'stripe_payment_intent' => $stripeSession->payment_intent,
                        'points_used' => $pendingOrderData['points_used'] ?? 0,
                        'points_discount' => $pendingOrderData['points_discount'] ?? 0,
                        'points_processed' => true,
                    ];

                    if ($itemData['book_id']) {
                        $orderData['book_id'] = $itemData['book_id'];
                        $book = \App\Models\Book::find($itemData['book_id']);

                        if ($book) {
                            $emailItems[] = [
                                'type' => 'book',
                                'title' => $book->titre,
                                'author' => $book->auteur ?? null,
                                'quantity' => $itemData['quantity'],
                                'unit_price' => $itemData['price'],
                                'total_price' => $itemData['price'] * $itemData['quantity']
                            ];
                        }

                        $totalAmount += $itemData['price'] * $itemData['quantity'];
                        \App\Models\Order::create($orderData);

                    } elseif ($itemData['package_id']) {
                        $package = \App\Models\Package::find($itemData['package_id']);

                        if ($package) {
                            foreach ($package->products as $course) {
                                $courseOrderData = $orderData;
                                $courseOrderData['product_id'] = $course->id;
                                \App\Models\Order::create($courseOrderData);

                                $courseExpirationDate = $this->resolveCourseExpirationDate($course);

                                \App\Models\ProductStudent::updateOrCreate(
                                    [
                                        'student_id' => Auth::id(),
                                        'product_id' => $course->id
                                    ],
                                    [
                                        'date' => now(),
                                        'is_active' => true,
                                        'access_granted_at' => now(),
                                        'expiration_date' => $courseExpirationDate,
                                        'is_expired' => false,
                                    ]
                                );
                            }

                            $emailItems[] = [
                                'type' => 'package',
                                'title' => $package->title,
                                'courses_count' => $package->products->count(),
                                'quantity' => $itemData['quantity'],
                                'unit_price' => $itemData['price'],
                                'total_price' => $itemData['price'] * $itemData['quantity']
                            ];
                        }

                        $totalAmount += $itemData['price'] * $itemData['quantity'];

                    } elseif ($itemData['product_id']) {
                        $orderData['product_id'] = $itemData['product_id'];
                        $product = \App\Models\Product::find($itemData['product_id']);

                        if ($product) {
                            $emailItems[] = [
                                'type' => 'course',
                                'title' => $product->titre,
                                'quantity' => $itemData['quantity'],
                                'unit_price' => $itemData['price'],
                                'total_price' => $itemData['price'] * $itemData['quantity']
                            ];
                        }

                        $totalAmount += $itemData['price'] * $itemData['quantity'];
                        \App\Models\Order::create($orderData);

                        $productExpirationDate = $this->resolveCourseExpirationDate($product);

                        \App\Models\ProductStudent::updateOrCreate(
                            [
                                'student_id' => Auth::id(),
                                'product_id' => $itemData['product_id']
                            ],
                            [
                                'date' => now(),
                                'is_active' => true,
                                'access_granted_at' => now(),
                                'expiration_date' => $productExpirationDate,
                                'is_expired' => false,
                            ]
                        );
                    }
                }

                if (!empty($pendingOrderData['applied_coupons'])) {
                    foreach ($pendingOrderData['applied_coupons'] as $validCoupon) {
                        $couponInstance = $validCoupon['instance'];
                        $couponInstance->increment('usage_count');

                        if ($couponInstance->usage_limit && $couponInstance->usage_count >= $couponInstance->usage_limit) {
                            $couponInstance->update(['active' => false]);
                        }
                    }
                }

                Notification::create([
                    'notifiable_type' => 'App\Models\Student',
                    'notifiable_id' => Auth::id(),
                    'type' => 'order',
                    'title' => 'Order Placed',
                    'message' => 'Your order has been placed successfully and payment confirmed.',
                    'data' => json_encode(['session_id' => $sessionId]),
                    'is_read' => false,
                ]);

                DB::commit();

                Log::info('Order processing completed successfully', [
                    'session_id' => $sessionId,
                    'student_id' => Auth::id(),
                    'total_amount' => $totalAmount,
                ]);

                session()->forget(['applied_coupon', 'applied_coupons', 'pending_order_data']);

                if (isset($pendingOrderData['metadata']['first_name']) && isset($pendingOrderData['metadata']['last_name'])) {
                    $student->update([
                        'first_name' => $pendingOrderData['metadata']['first_name'],
                        'last_name' => $pendingOrderData['metadata']['last_name'],
                    ]);
                }

                try {
                    Mail::to($student->email)->send(
                        new OrderConfirmationEmail(
                            $student,
                            $emailItems,
                            $totalAmount,
                            now()->format('Y-m-d H:i'),
                            'credit_card',
                            $pendingOrderData['metadata']['notes'] ?? null
                        )
                    );
                } catch (\Exception $e) {
                    Log::error('Email error: ' . $e->getMessage());
                }

                $this->markAbandonedCartAsConverted();

                $pointsUsed = $pendingOrderData['points_used'] ?? 0;
                if ($pointsUsed > 0) {
                    session(['applied_points' => $pointsUsed]);
                }
                $this->processPointsForOrder($totalAmount);
                $latestOrder = \App\Models\Order::where('student_id', Auth::id())
                    ->orderByDesc('id')->first();
                $this->processReferralReward(Auth::id(), $latestOrder?->id);

                Cart::where('student_id', Auth::id())->delete();

                return redirect()->route('student-dashboard.index')
                    ->with('success', __('messages.payment_successful_order_placed'));

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Order processing error after Stripe payment', [
                    'error' => $e->getMessage(),
                    'session_id' => $sessionId,
                ]);

                return redirect()->route('checkout.index')
                    ->with('error', __('messages.order_processing_error'));
            }

        } catch (\Exception $e) {
            Log::error('Stripe session retrieval error', [
                'error' => $e->getMessage(),
                'session_id' => $sessionId,
            ]);

            return redirect()->route('checkout.index')
                ->with('error', __('messages.payment_verification_error'));
        }
    }

    public function stripeCancel()
    {
        session()->forget('pending_order_data');

        return redirect()->route('checkout.index')
            ->with('error', __('messages.payment_cancelled'));
    }

    protected function processFreeOrder(Request $request)
    {
        DB::beginTransaction();

        try {
            $student = Auth::user();
            $cartItems = Cart::where('student_id', Auth::id())
                ->with(['book', 'product', 'package'])
                ->get();
            $this->applyReferralDiscountToCart($cartItems, Auth::id());

            $appliedCoupons = session('applied_coupons', []);
            $appliedCoupon = session('applied_coupon');
            $totalDiscount = 0;
            $validCoupons = [];

            if (!empty($appliedCoupons)) {
                foreach ($appliedCoupons as $couponData) {
                    $couponInstance = \App\Models\Coupon::find($couponData['id']);
                    if ($couponInstance) {
                        $validCoupons[] = [
                            'instance' => $couponInstance,
                            'discount' => $couponData['discount_amount'] ?? 0,
                        ];
                        $totalDiscount += $couponData['discount_amount'] ?? 0;
                    }
                }
            } elseif ($appliedCoupon) {
                $couponInstance = \App\Models\Coupon::find($appliedCoupon['id']);
                if ($couponInstance) {
                    $validCoupons[] = [
                        'instance' => $couponInstance,
                        'discount' => $appliedCoupon['discount_amount'] ?? 0,
                    ];
                    $totalDiscount = $appliedCoupon['discount_amount'] ?? 0;
                }
            }

            $originalTotal = $cartItems->sum('price');
            $discountRatio = $originalTotal > 0 ? $totalDiscount / $originalTotal : 0;

            $appliedPoints = session('applied_points', 0);
            $pointsDiscount = 0;
            if ($appliedPoints > 0) {
                $pointsDiscount = PointsService::calculateDiscount($appliedPoints);
            }
            $afterCouponTotal = $originalTotal - $totalDiscount;
            $pointsDiscountRatio = ($afterCouponTotal > 0 && $pointsDiscount > 0) ? $pointsDiscount / $afterCouponTotal : 0;

            $emailItems = [];
            $totalAmount = 0;

            foreach ($cartItems as $cartItem) {
                $itemDiscount = $cartItem->price * $discountRatio;
                $priceAfterCoupon = max(0, $cartItem->price - $itemDiscount);

                $itemPointsDiscount = $priceAfterCoupon * $pointsDiscountRatio;
                $finalPrice = max(0, $priceAfterCoupon - $itemPointsDiscount);

                $orderData = [
                    'student_id' => Auth::id(),
                    'price' => $finalPrice,
                    'payment_success' => true,
                    'payment_method' => 'free',
                    'notes' => $request->notes,
                    'points_used' => $appliedPoints,
                    'points_discount' => $pointsDiscount,
                    'points_processed' => true,
                ];

                if ($cartItem->book_id) {
                    $orderData['book_id'] = $cartItem->book_id;
                    $book = $cartItem->book;

                    $emailItems[] = [
                        'type' => 'book',
                        'title' => $book?->titre,
                        'author' => $book?->auteur ?? null,
                        'quantity' => $cartItem->quantity,
                        'unit_price' => $finalPrice,
                        'total_price' => $finalPrice * $cartItem->quantity
                    ];

                    $totalAmount += $finalPrice * $cartItem->quantity;
                    Order::create($orderData);

                } elseif ($cartItem->package_id) {
                    $package = $cartItem->package ?? \App\Models\Package::find($cartItem->package_id);

                    if ($package) {
                        $packageProducts = $package->packageProducts()->where('is_active', true)->with('product')->get();
                        $courseCount = $packageProducts->count();

                        if ($courseCount > 0) {
                            $pricePerCourse = $finalPrice / $courseCount;

                            foreach ($packageProducts as $packageProduct) {
                                if ($packageProduct->product) {
                                    $product = $packageProduct->product;

                                    $courseOrderData = [
                                        'student_id' => Auth::id(),
                                        'product_id' => $product->id,
                                        'price' => $pricePerCourse,
                                        'payment_success' => true,
                                        'payment_method' => 'free',
                                        'notes' => $request->notes
                                    ];

                                    Order::create($courseOrderData);

                                    $expirationDate = $this->resolveCourseExpirationDate($product);

                                    \App\Models\ProductStudent::updateOrCreate(
                                        [
                                            'student_id' => Auth::id(),
                                            'product_id' => $product->id
                                        ],
                                        [
                                            'date' => now(),
                                            'is_active' => true,
                                            'access_granted_at' => now(),
                                            'expiration_date' => $expirationDate,
                                            'is_expired' => false,
                                        ]
                                    );
                                }
                            }

                            $emailItems[] = [
                                'type' => 'package',
                                'title' => $package->formatted_title,
                                'courses' => $packageProducts->pluck('product.titre')->toArray(),
                                'quantity' => $cartItem->quantity,
                                'unit_price' => $finalPrice,
                                'total_price' => $finalPrice * $cartItem->quantity
                            ];

                            $totalAmount += $finalPrice * $cartItem->quantity;
                        }
                    }

                } elseif ($cartItem->product_id) {
                    $orderData['product_id'] = $cartItem->product_id;
                    $product = $cartItem->product;

                    $emailItems[] = [
                        'type' => 'course',
                        'title' => $product?->titre,
                        'quantity' => $cartItem->quantity,
                        'unit_price' => $finalPrice,
                        'total_price' => $finalPrice * $cartItem->quantity
                    ];

                    $totalAmount += $finalPrice * $cartItem->quantity;
                    Order::create($orderData);

                    $expirationDate = $this->resolveCourseExpirationDate($product);

                    \App\Models\ProductStudent::updateOrCreate(
                        [
                            'student_id' => Auth::id(),
                            'product_id' => $cartItem->product_id
                        ],
                        [
                            'date' => now(),
                            'is_active' => true,
                            'access_granted_at' => now(),
                            'expiration_date' => $expirationDate,
                            'is_expired' => false,
                        ]
                    );
                }
            }

            $student = Auth::user();
            if ($request->filled('first_name') && $request->filled('last_name')) {
                $student->update([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                ]);
            }

            foreach ($validCoupons as $validCoupon) {
                $couponInstance = $validCoupon['instance'];
                $couponInstance->increment('usage_count');

                if ($couponInstance->usage_limit && $couponInstance->usage_count >= $couponInstance->usage_limit) {
                    $couponInstance->update(['active' => false]);
                }
            }

            Notification::create([
                'notifiable_type' => 'App\Models\Student',
                'notifiable_id' => Auth::id(),
                'type' => 'order',
                'title' => __('messages.order_placed'),
                'message' => __('messages.free_order_completed'),
                'data' => json_encode(['order_type' => 'free']),
                'is_read' => false,
            ]);

            DB::commit();

            try {
                Mail::to($student->email)->send(
                    new OrderConfirmationEmail(
                        $student,
                        $emailItems,
                        $totalAmount,
                        now()->format('Y-m-d H:i'),
                        'free',
                        $request->notes
                    )
                );
            } catch (\Exception $e) {
                Log::error('Free order email error: ' . $e->getMessage());
            }

            $this->markAbandonedCartAsConverted();
            $this->processPointsForOrder($totalAmount);
            $latestFreeOrder = \App\Models\Order::where('student_id', Auth::id())
                ->orderByDesc('id')->first();
            $this->processReferralReward(Auth::id(), $latestFreeOrder?->id);

            session()->forget(['applied_coupon', 'applied_coupons']);
            Cart::where('student_id', Auth::id())->delete();

            return redirect()->route('student-dashboard.index')
                ->with('success', __('messages.free_order_success'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Free order processing error', [
                'error' => $e->getMessage(),
                'student_id' => Auth::id(),
            ]);

            return redirect()->route('checkout.index')
                ->with('error', __('messages.order_processing_error'));
        }
    }

    public function applyPoints(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', __('messages.login_required'));
        }

        $request->validate([
            'points' => 'required|integer|min:1',
        ]);

        $pointsToApply = $request->points;
        $pointsBalance = $this->pointsService->getPointsBalance(Auth::id());

        if ($pointsToApply > $pointsBalance['available_points']) {
            return redirect()->route('checkout.index')
                ->with('error', __('messages.insufficient_points') ?? 'Insufficient points balance');
        }

        $cartItems = Cart::where('student_id', Auth::id())->get();
        $cartTotal = $cartItems->sum(fn($item) => $item->price * $item->quantity);

        $appliedCoupons = session('applied_coupons', []);
        $appliedCoupon = session('applied_coupon');
        $couponDiscount = 0;

        if (!empty($appliedCoupons)) {
            foreach ($appliedCoupons as $couponData) {
                $couponDiscount += $couponData['discount_amount'] ?? 0;
            }
        } elseif ($appliedCoupon) {
            $couponDiscount = $appliedCoupon['discount_amount'] ?? 0;
        }

        $remainingTotal = $cartTotal - $couponDiscount;
        $maxPointsDiscount = $remainingTotal;
        $maxPoints = PointsService::pointsNeededForDiscount($maxPointsDiscount);

        $pointsToApply = min($pointsToApply, $maxPoints, $pointsBalance['available_points']);

        session(['applied_points' => $pointsToApply]);

        $discount = PointsService::calculateDiscount($pointsToApply);

        return redirect()->route('checkout.index')
            ->with('success', __('messages.points_applied') ?? "{$pointsToApply} points applied for \${$discount} discount");
    }

    public function removePoints()
    {
        session()->forget('applied_points');

        return redirect()->route('checkout.index')
            ->with('success', __('messages.points_removed') ?? 'Points discount removed');
    }
}
