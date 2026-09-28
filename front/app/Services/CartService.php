<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Cart;
use App\Models\Package;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartService
{
    protected CouponService $couponService;

    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }

    public function index()
    {
        if (Auth::check()) {
            $cartItems = Cart::where('student_id', Auth::id())
                ->with(['book', 'product', 'package'])
                ->get()
                ->map(function ($item) {
                    if ($item->package) {
                        $item->price = (float) ($item->package->discounted_price ?? $item->price ?? 0);
                    } elseif ($item->product) {
                        $item->price = (float) ($item->price ?? $item->product->prix ?? 0);
                    } elseif ($item->book) {
                        $item->price = (float) ($item->price ?? $item->book->prix ?? 0);
                    } else {
                        $item->price = (float) ($item->price ?? 0);
                    }

                    return $item;
                });

            $total = $cartItems->sum(function ($item) {
                return ((float) ($item->price ?? 0)) * ((int) ($item->quantity ?? 1));
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
                } else {
                    continue;
                }

                if ($itemType === 'book') {
                    $book = Book::find($itemId);
                    if ($book) {
                        $price = (float) ($item['price'] ?? $book->prix ?? 0);
                        $cartItem = (object) [
                            'id' => $itemKey,
                            'book' => $book,
                            'product' => null,
                            'package' => null,
                            'quantity' => $item['quantity'],
                            'price' => $price,
                            'total_price' => $item['quantity'] * $price,
                            'formatted_price' => '$' . number_format($price, 2),
                            'formatted_total_price' => '$' . number_format($item['quantity'] * $price, 2),
                            'item_title' => $book->titre,
                            'item_type' => 'book',
                        ];
                        $cartItems->push($cartItem);
                        $total += $item['quantity'] * $price;
                        $itemCount += $item['quantity'];
                    }
                } elseif ($itemType === 'course') {
                    $product = Product::find($itemId);
                    if ($product) {
                        $price = (float) ($item['price'] ?? $product->prix ?? 0);
                        $cartItem = (object) [
                            'id' => $itemKey,
                            'book' => null,
                            'product' => $product,
                            'package' => null,
                            'quantity' => $item['quantity'],
                            'price' => $price,
                            'total_price' => $item['quantity'] * $price,
                            'formatted_price' => '$' . number_format($price, 2),
                            'formatted_total_price' => '$' . number_format($item['quantity'] * $price, 2),
                            'item_title' => $product->titre,
                            'item_type' => 'course',
                        ];
                        $cartItems->push($cartItem);
                        $total += $item['quantity'] * $price;
                        $itemCount += $item['quantity'];
                    }
                } elseif ($itemType === 'package') {
                    $package = Package::find($itemId);
                    if ($package) {
                        $packagePrice = (float) ($package->discounted_price ?? $item['price'] ?? $package->prix ?? 0);
                        $cartItem = (object) [
                            'id' => $itemKey,
                            'book' => null,
                            'product' => null,
                            'package' => $package,
                            'quantity' => $item['quantity'],
                            'price' => $packagePrice,
                            'total_price' => $item['quantity'] * $packagePrice,
                            'formatted_price' => '$' . number_format($packagePrice, 2),
                            'formatted_total_price' => '$' . number_format($item['quantity'] * $packagePrice, 2),
                            'item_title' => $package->title,
                            'item_type' => 'package',
                        ];
                        $cartItems->push($cartItem);
                        $total += $item['quantity'] * $packagePrice;
                        $itemCount += $item['quantity'];
                    }
                }
            }
        }

        $autoAppliedCoupon = null;
        if (Auth::check() && $itemCount >= 2) {
            $currentCoupon = session('applied_coupon');
            if (!$currentCoupon) {
                $cartData = [
                    'total' => $total,
                    'item_count' => $itemCount,
                    'course_types' => $cartItems->pluck('product.type')->filter()->unique()->toArray(),
                    'product_ids' => $cartItems->pluck('product_id')->filter()->toArray(),
                ];

                $bestCoupon = $this->couponService->findBestAutoApplyCoupon(Auth::user(), $cartData);

                if ($bestCoupon) {
                    session([
                        'applied_coupon' => [
                            'id' => $bestCoupon['coupon']->id,
                            'code' => $bestCoupon['coupon']->code,
                            'name' => $bestCoupon['coupon']->nom ?: $bestCoupon['coupon']->code,
                            'type' => $bestCoupon['coupon']->type,
                            'value' => $bestCoupon['coupon']->valeur,
                            'discount_amount' => $bestCoupon['discount_amount'],
                            'customer_type' => $bestCoupon['user_type'],
                            'is_stackable' => $bestCoupon['coupon']->is_stackable ?? false,
                            'auto_applied' => true,
                        ],
                    ]);
                    $autoAppliedCoupon = $bestCoupon;
                }
            }
        }

        return view('cart.index', compact('cartItems', 'total', 'itemCount', 'autoAppliedCoupon'));
    }

    public function add(Request $request, Book $book)
    {
        return $this->addItem($request, $book, 'book');
    }

    public function addCourse(Request $request, Product $product)
    {
        return $this->addItem($request, $product, 'course');
    }

    public function buyNowCourse(Request $request, Product $product)
    {
        try {
            $quantity = 1;
            $price = (float) ($product->prix ?? 0);

            if (Auth::check()) {
                $existingItem = Cart::where('student_id', Auth::id())
                    ->where('product_id', $product->id)
                    ->first();

                if (!$existingItem) {
                    Cart::create([
                        'student_id' => Auth::id(),
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'price' => $price,
                    ]);
                }
            } else {
                $cart = session('cart', []);
                $itemKey = 'course_' . $product->id;

                if (!isset($cart[$itemKey])) {
                    $cart[$itemKey] = [
                        'type' => 'course',
                        'quantity' => $quantity,
                        'price' => $price,
                    ];
                    session(['cart' => $cart]);
                    session(['cart_count' => count($cart)]);
                }
            }

            return redirect()->route('checkout.index');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('messages.something_went_wrong'));
        }
    }

    public function addPackage(Request $request, Package $package)
    {
        return $this->addItem($request, $package, 'package');
    }

    private function addItem(Request $request, $item, string $type)
    {
        try {
            $quantity = 1;

            if (Auth::check()) {
                $query = Cart::where('student_id', Auth::id());

                if ($type === 'book') {
                    $query->where('book_id', $item->id);
                    $price = (float) ($item->prix ?? 0);
                } elseif ($type === 'course') {
                    $query->where('product_id', $item->id);
                    $price = (float) ($item->prix ?? 0);
                } else {
                    $query->where('package_id', $item->id);
                    $price = (float) ($item->discounted_price ?? $item->prix ?? 0);
                }

                $existingItem = $query->first();

                if ($existingItem) {
                    $message = $type === 'book' ? __('messages.book_already_in_cart') : ($type === 'course' ? __('messages.course_already_in_cart') : __('messages.package_already_in_cart'));

                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => $message,
                        ]);
                    }

                    return redirect()->back()->with('error', $message);
                }

                $cartData = [
                    'student_id' => Auth::id(),
                    'quantity' => $quantity,
                    'price' => $price,
                ];

                if ($type === 'book') {
                    $cartData['book_id'] = $item->id;
                } elseif ($type === 'course') {
                    $cartData['product_id'] = $item->id;
                } else {
                    $cartData['package_id'] = $item->id;
                }

                Cart::create($cartData);

                $cartCount = Cart::where('student_id', Auth::id())->count();
            } else {
                $cart = session('cart', []);
                $itemKey = $type . '_' . $item->id;

                if (isset($cart[$itemKey])) {
                    $message = $type === 'book' ? __('messages.book_already_in_cart') : ($type === 'course' ? __('messages.course_already_in_cart') : __('messages.package_already_in_cart'));

                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => $message,
                        ]);
                    }

                    return redirect()->back()->with('error', $message);
                }

                $cart[$itemKey] = [
                    'type' => $type,
                    'quantity' => $quantity,
                    'price' => $type === 'package'
                        ? (float) ($item->discounted_price ?? $item->prix ?? 0)
                        : (float) ($item->prix ?? 0),
                ];

                session(['cart' => $cart]);
                $cartCount = count($cart);
                session(['cart_count' => $cartCount]);
            }

            if ($request->expectsJson()) {
                $message = $type === 'book' ? __('messages.book_added_to_cart') : ($type === 'course' ? __('messages.course_added_successfully') : __('messages.package_added_successfully'));

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'cart_count' => $cartCount,
                ]);
            }

            $message = $type === 'book' ? __('messages.book_added_to_cart') : __('messages.course_added_successfully');

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.something_went_wrong'),
                ], 500);
            }

            return redirect()->back()->with('error', __('messages.something_went_wrong'));
        }
    }

    public function update(Request $request, $cartItemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        if (Auth::check()) {
            $cartItem = Cart::findOrFail($cartItemId);
            if ($cartItem->student_id !== Auth::id()) {
                abort(403);
            }
            $cartItem->update(['quantity' => $request->quantity]);
        } else {
            $cart = session('cart', []);
            if (isset($cart[$cartItemId])) {
                $cart[$cartItemId]['quantity'] = $request->quantity;
                session(['cart' => $cart]);
                session(['cart_count' => array_sum(array_column($cart, 'quantity'))]);
            }
        }

        return redirect()->back()->with('success', __('messages.cart_updated'));
    }

    public function remove($cartItemId)
    {
        if (Auth::check()) {
            $cartItem = Cart::findOrFail($cartItemId);
            if ($cartItem->student_id !== Auth::id()) {
                abort(403);
            }
            $cartItem->delete();

            $remainingCount = Cart::where('student_id', Auth::id())->count();

            if ($remainingCount < 2) {
                $appliedCoupons = session('applied_coupons', []);
                $appliedCoupon = session('applied_coupon');

                $hasAutoCoupon = false;
                if (!empty($appliedCoupons)) {
                    foreach ($appliedCoupons as $couponData) {
                        $coupon = \App\Models\Coupon::find($couponData['id']);
                        if ($coupon && $coupon->auto_apply) {
                            $hasAutoCoupon = true;
                            break;
                        }
                    }
                } elseif ($appliedCoupon) {
                    $coupon = \App\Models\Coupon::find($appliedCoupon['id']);
                    if ($coupon && $coupon->auto_apply) {
                        $hasAutoCoupon = true;
                    }
                }

                if ($hasAutoCoupon) {
                    session()->forget(['applied_coupon', 'applied_coupons']);
                }
            }
        } else {
            $cart = session('cart', []);
            if (isset($cart[$cartItemId])) {
                unset($cart[$cartItemId]);
                session(['cart' => $cart]);
                session(['cart_count' => array_sum(array_column($cart, 'quantity'))]);

                if (count($cart) < 2) {
                    session()->forget(['applied_coupon', 'applied_coupons']);
                }
            }
        }

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.item_removed_from_cart'),
                'cart_count' => Auth::check() ? Cart::where('student_id', Auth::id())->sum('quantity') : session('cart_count', 0),
            ]);
        }

        return redirect()->back()->with('success', __('messages.item_removed_from_cart'));
    }

    public function clear()
    {
        if (Auth::check()) {
            Cart::where('student_id', Auth::id())->delete();
        } else {
            session()->forget(['cart', 'cart_count']);
        }

        session()->forget(['applied_coupon', 'applied_coupons']);

        return redirect()->back()->with('success', __('messages.cart_cleared'));
    }

    public function getCartCount()
    {
        if (Auth::check()) {
            $count = Cart::where('student_id', Auth::id())->sum('quantity');
        } else {
            $count = session('cart_count', 0);
        }

        return response()->json(['count' => $count]);
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string|max:255',
        ]);

        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => __('messages.login_required'),
            ], 401);
        }

        $user = Auth::user();
        $code = strtoupper(trim($request->coupon_code));

        $totalAmount = $this->calculateTotalAmount();
        $cartItems = $this->getCartItems();

        $cartData = [
            'total' => $totalAmount,
            'item_count' => $cartItems->sum('quantity'),
            'course_types' => $cartItems->pluck('product.type')->filter()->unique()->toArray(),
            'product_ids' => $cartItems->pluck('product_id')->filter()->toArray(),
            'session_id' => session()->getId(),
        ];

        $appliedCoupons = session('applied_coupons', []);
        foreach ($appliedCoupons as $appliedCoupon) {
            if ($appliedCoupon['code'] === $code) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.coupon_already_applied'),
                ], 422);
            }
        }

        $result = $this->couponService->applyCouponToCart($code, $user, $cartData);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 422);
        }

        if (!empty($appliedCoupons)) {
            if (!($result['coupon']->is_stackable ?? false)) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.coupon_not_stackable'),
                ], 422);
            }

            foreach ($appliedCoupons as $appliedCoupon) {
                if (!isset($appliedCoupon['is_stackable']) || !$appliedCoupon['is_stackable']) {
                    return response()->json([
                        'success' => false,
                        'message' => __('messages.existing_coupon_not_stackable'),
                    ], 422);
                }
            }
        }

        $couponData = [
            'id' => $result['coupon']->id,
            'code' => $result['coupon']->code,
            'name' => $result['coupon']->nom ?: $result['coupon']->code,
            'type' => $result['coupon']->type,
            'value' => $result['coupon']->valeur,
            'discount_amount' => $result['discount_amount'],
            'customer_type' => $result['user_type'],
            'is_stackable' => $result['coupon']->is_stackable ?? false,
            'affiliate_partner_id' => $result['coupon']->affiliate_partner_id ?? null,
            'commission_rate' => $result['coupon']->commission_rate ?? null,
        ];

        $appliedCoupons[] = $couponData;

        $totalDiscount = array_sum(array_column($appliedCoupons, 'discount_amount'));
        $finalTotal = max(0, $totalAmount - $totalDiscount);

        session(['applied_coupons' => $appliedCoupons]);
        session(['applied_coupon' => $couponData]);

        return response()->json([
            'success' => true,
            'message' => __('messages.coupon_applied'),
            'coupon' => [
                'code' => $result['coupon']->code,
                'name' => $result['coupon']->nom ?: $result['coupon']->code,
                'discount_amount' => $result['discount_amount'],
                'formatted_discount' => '$' . number_format($result['discount_amount'], 2),
                'customer_type' => $result['user_type'],
            ],
            'all_coupons' => array_map(function ($coupon) {
                return [
                    'code' => $coupon['code'],
                    'name' => $coupon['name'],
                    'discount_amount' => $coupon['discount_amount'],
                    'formatted_discount' => '$' . number_format($coupon['discount_amount'], 2),
                ];
            }, $appliedCoupons),
            'total_discount' => $totalDiscount,
            'formatted_total_discount' => '$' . number_format($totalDiscount, 2),
            'original_total' => $totalAmount,
            'formatted_original_total' => '$' . number_format($totalAmount, 2),
            'new_total' => $finalTotal,
            'formatted_new_total' => '$' . number_format($finalTotal, 2),
            'coupons_count' => count($appliedCoupons),
        ]);
    }

    public function removeCoupon(Request $request)
    {
        $couponCode = $request->input('coupon_code') ?? $request->query('coupon_code');

        if ($couponCode) {
            $appliedCoupons = session('applied_coupons', []);
            $appliedCoupons = array_filter($appliedCoupons, function ($coupon) use ($couponCode) {
                return $coupon['code'] !== strtoupper($couponCode);
            });

            $appliedCoupons = array_values($appliedCoupons);

            session(['applied_coupons' => $appliedCoupons]);

            if (!empty($appliedCoupons)) {
                session(['applied_coupon' => end($appliedCoupons)]);
            } else {
                session()->forget('applied_coupon');
            }

            $message = __('messages.coupon_removed');
        } else {
            if (Auth::check()) {
                $this->couponService->removeCouponFromCart(Auth::user());
            }

            session()->forget(['applied_coupon', 'applied_coupons']);
            $message = __('messages.all_coupons_removed');
        }

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    private function getCartItems()
    {
        if (Auth::check()) {
            return Cart::where('student_id', Auth::id())
                ->with(['book', 'product', 'package'])
                ->get();
        }

        return collect();
    }

    private function calculateTotalAmount()
    {
        $total = 0;

        if (Auth::check()) {
            $cartItems = Cart::where('student_id', Auth::id())->get();
            foreach ($cartItems as $item) {
                $total += $item->total_price;
            }
        } else {
            $cart = session('cart', []);
            foreach ($cart as $item) {
                $total += $item['price'] * $item['quantity'];
            }
        }

        return $total;
    }

    public function getCouponDetails(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string|max:255',
        ]);

        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => __('messages.login_required'),
            ], 401);
        }

        $user = Auth::user();
        $code = strtoupper(trim($request->coupon_code));

        $validation = $this->couponService->validateCouponForUser($code, $user);

        if (!$validation['valid']) {
            return response()->json([
                'success' => false,
                'message' => $validation['message'],
            ]);
        }

        $coupon = $validation['coupon'];
        $totalAmount = $this->calculateTotalAmount();
        $cartItems = $this->getCartItems();

        $cartData = [
            'total' => $totalAmount,
            'item_count' => $cartItems->sum('quantity'),
            'course_types' => $cartItems->pluck('product.type')->filter()->unique()->toArray(),
            'product_ids' => $cartItems->pluck('product_id')->filter()->toArray(),
        ];

        if ($coupon->type === 'percentage') {
            $discountAmount = ($totalAmount * $coupon->valeur) / 100;
            if ($coupon->max_discount_amount && $discountAmount > $coupon->max_discount_amount) {
                $discountAmount = $coupon->max_discount_amount;
            }
        } else {
            $discountAmount = min($coupon->valeur, $totalAmount);
        }
        $discountAmount = round($discountAmount, 2);

        $canBeApplied = true;
        $reason = '';

        if ($coupon->min_purchase_amount && $totalAmount < $coupon->min_purchase_amount) {
            $canBeApplied = false;
            $reason = __('coupon.minimum_purchase_required', [
                'amount' => '$' . number_format($coupon->min_purchase_amount, 2),
                'current' => '$' . number_format($totalAmount, 2),
            ]);
        }

        if ($coupon->min_items && $cartData['item_count'] < $coupon->min_items) {
            $canBeApplied = false;
            $reason = __('coupon.minimum_items_required', [
                'count' => $coupon->min_items,
                'current' => $cartData['item_count'],
            ]);
        }

        return response()->json([
            'success' => true,
            'coupon' => [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'name' => $coupon->nom,
                'description' => $coupon->description,
                'type' => $coupon->type,
                'value' => $coupon->valeur,
                'customer_type' => $validation['user_type'],
                'minimum_amount' => $coupon->min_purchase_amount,
                'formatted_minimum_amount' => '$' . number_format($coupon->min_purchase_amount ?? 0, 2),
                'can_be_applied' => $canBeApplied,
                'reason' => $reason,
                'discount_amount' => $discountAmount,
                'formatted_discount_amount' => '$' . number_format($discountAmount, 2),
            ],
        ]);
    }

    public function autoApplyBestCoupon()
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => __('messages.login_required'),
            ], 401);
        }

        $user = Auth::user();
        $cartItems = $this->getCartItems();

        if ($cartItems->count() < 2) {
            return response()->json([
                'success' => false,
                'message' => __('messages.minimum_items_for_auto_coupon'),
            ]);
        }

        $totalAmount = $this->calculateTotalAmount();
        $cartData = [
            'total' => $totalAmount,
            'item_count' => $cartItems->sum('quantity'),
            'course_types' => $cartItems->pluck('product.type')->filter()->unique()->toArray(),
            'product_ids' => $cartItems->pluck('product_id')->filter()->toArray(),
            'session_id' => session()->getId(),
        ];

        $bestCouponResult = $this->couponService->findBestCouponForCart($user, $cartData);

        if (!$bestCouponResult) {
            return response()->json([
                'success' => false,
                'message' => __('messages.no_applicable_coupons'),
            ]);
        }

        session([
            'applied_coupon' => [
                'id' => $bestCouponResult['coupon']->id,
                'code' => $bestCouponResult['coupon']->code,
                'name' => $bestCouponResult['coupon']->nom ?: $bestCouponResult['coupon']->code,
                'type' => $bestCouponResult['coupon']->type,
                'value' => $bestCouponResult['coupon']->valeur,
                'discount_amount' => $bestCouponResult['discount_amount'],
                'customer_type' => $bestCouponResult['user_type'],
                'is_stackable' => $bestCouponResult['coupon']->is_stackable ?? false,
                'affiliate_partner_id' => $bestCouponResult['coupon']->affiliate_partner_id ?? null,
                'commission_rate' => $bestCouponResult['coupon']->commission_rate ?? null,
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => __('messages.best_coupon_auto_applied'),
            'coupon' => [
                'code' => $bestCouponResult['coupon']->code,
                'name' => $bestCouponResult['coupon']->nom ?: $bestCouponResult['coupon']->code,
                'discount_amount' => $bestCouponResult['discount_amount'],
                'formatted_discount' => '$' . number_format($bestCouponResult['discount_amount'], 2),
                'customer_type' => $bestCouponResult['user_type'],
            ],
            'original_total' => $bestCouponResult['original_total'],
            'formatted_original_total' => '$' . number_format($bestCouponResult['original_total'], 2),
            'new_total' => $bestCouponResult['final_total'],
            'formatted_new_total' => '$' . number_format($bestCouponResult['final_total'], 2),
        ]);
    }
}
