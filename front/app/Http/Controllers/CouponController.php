<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Services\CouponFrontendService;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    protected CouponFrontendService $couponFrontendService;

    public function __construct(CouponFrontendService $couponFrontendService)
    {
        $this->couponFrontendService = $couponFrontendService;
    }

    /**
     * Display public available coupons for the student
     */
    public function index()
    {
        return $this->couponFrontendService->index();
    }

    /**
     * Display a specific coupon details
     */
    public function show(Coupon $coupon)
    {
        return $this->couponFrontendService->show($coupon);
    }

    /**
     * Apply a coupon to the cart
     */
    public function apply(Request $request)
    {
        return $this->couponFrontendService->apply($request);
    }

    /**
     * Remove a coupon from the cart
     */
    public function remove(Request $request, $couponId)
    {
        return $this->couponFrontendService->remove($request, $couponId);
    }

    /**
     * Get active coupons in cart
     */
    public function getActiveCoupons()
    {
        return $this->couponFrontendService->getActiveCoupons();
    }

    /**
     * Check for auto-apply coupons
     */
    public function checkAutoApply()
    {
        return $this->couponFrontendService->checkAutoApply();
    }

    /**
     * Validate a coupon code without applying
     */
    public function validate(Request $request)
    {
        return $this->couponFrontendService->validate($request);
    }
}
