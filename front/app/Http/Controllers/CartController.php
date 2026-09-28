<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Product;
use App\Models\Package;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
        * Display the cart
     */
    public function index()
    {
        return $this->cartService->index();
    }

    /**
        * Add a book to the cart
     */
    public function add(Request $request, $locale, Book $book)
    {
        return $this->cartService->add($request, $book);
    }

    /**
        * Add a course to the cart
     */
    public function addCourse(Request $request, $locale, Product $product)
    {
        return $this->cartService->addCourse($request, $product);
    }

    public function buyNowCourse(Request $request, string $locale, Product $product)
    {
        return $this->cartService->buyNowCourse($request, $product);
    }

    /**
        * Add a package to the cart
     */
    public function addPackage(Request $request, $locale, Package $package)
    {
        return $this->cartService->addPackage($request, $package);
    }

    /**
        * Update cart item quantity
     */
    public function update(Request $request, $locale, $cartItemId)
    {
        return $this->cartService->update($request, $cartItemId);
    }

    /**
        * Remove an item from the cart
     */
    public function remove($locale, $cartItemId)
    {
        return $this->cartService->remove($cartItemId);
    }

    /**
        * Clear the cart
     */
    public function clear()
    {
        return $this->cartService->clear();
    }

    /**
        * Get cart item count (for API)
     */
    public function getCartCount()
    {
        return $this->cartService->getCartCount();
    }

    /**
     * Apply coupon with full validation using CouponService
     */
    public function applyCoupon(Request $request)
    {
        return $this->cartService->applyCoupon($request);
    }

    /**
     * Remove applied coupon using CouponService
     * Can remove a specific coupon by code or all coupons
     */
    public function removeCoupon(Request $request)
    {
        return $this->cartService->removeCoupon($request);
    }

    /**
     * Get coupon details by code
     */
    public function getCouponDetails(Request $request)
    {
        return $this->cartService->getCouponDetails($request);
    }

    /**
     * Auto-apply the best coupon for cart with 2+ items
     */
    public function autoApplyBestCoupon()
    {
        return $this->cartService->autoApplyBestCoupon();
    }
}
