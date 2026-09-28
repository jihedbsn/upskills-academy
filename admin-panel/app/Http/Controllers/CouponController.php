<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreCouponRequest;
use App\Http\Requests\UpdateCouponRequest;
use App\Models\Coupon;
use App\Services\CouponService;

class CouponController extends Controller
{
    protected $couponService;

    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }

    /**
     * Display a listing of coupons with advanced filtering
     */
    public function index(Request $request)
    {
        return $this->couponService->index($request);
    }

    /**
     * Creation form for a new coupon with advanced features
     */
    public function create()
    {
        return $this->couponService->create();
    }

    /**
     * Store a newly created coupon with all advanced features
     */
    public function store(StoreCouponRequest $request)
    {
        try {
            $coupon = $this->couponService->createCoupon($request->validated());

            return redirect()->route('coupons.index')
                ->with('success', "Coupon '{$coupon->code}' created successfully with all advanced features!");
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['valeur' => $e->getMessage()])->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create coupon: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified coupon with detailed statistics
     */
    public function show(Coupon $coupon)
    {
        $data = $this->couponService->getCouponWithStats($coupon);

        return view('admin.coupons.show', $data);
    }

    /**
     * Show the form for editing the specified coupon
     */
    public function edit(Coupon $coupon)
    {
        return $this->couponService->edit($coupon);
    }

    /**
     * Update the specified coupon with all advanced features
     */
    public function update(UpdateCouponRequest $request, Coupon $coupon)
    {
        try {
            $updatedCoupon = $this->couponService->updateCoupon($coupon, $request->validated());

            return redirect()->route('coupons.index')
                ->with('success', "Coupon '{$updatedCoupon->code}' updated successfully!");
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['valeur' => $e->getMessage()])->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update coupon: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified coupon from storage or deactivate if used
     */
    public function destroy(Coupon $coupon)
    {
        try {
            $code = $coupon->code;
            $result = $this->couponService->deleteCoupon($coupon);

            if ($result['action'] === 'deactivated') {
                return redirect()->route('coupons.index')
                    ->with('warning', $result['message'])
                    ->with('show_popup', true);
            }

            return redirect()->route('coupons.index')
                ->with('success', "Coupon '{$code}' deleted successfully!");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to process coupon deletion: ' . $e->getMessage()]);
        }
    }

    /**
     * Toggle the active status of a coupon
     */
    public function toggle(Coupon $coupon)
    {
        $newStatus = $this->couponService->toggleCouponStatus($coupon);
        $status = $newStatus ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "Coupon '{$coupon->code}' {$status} successfully!");
    }

    /**
     * Duplicate an existing coupon
     */
    public function duplicate(Coupon $coupon)
    {
        try {
            $newCoupon = $this->couponService->duplicateCoupon($coupon);

            return redirect()->route('coupons.edit', $newCoupon)
                ->with('success', "Coupon duplicated successfully with code '{$newCoupon->code}'. Please review and activate.");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to duplicate coupon: ' . $e->getMessage()]);
        }
    }

    /**
     * Validate if a coupon code is available
     */
    public function validateCode(Request $request)
    {
        $available = $this->couponService->isCodeAvailable(
            $request->code,
            $request->filled('except_id') ? $request->except_id : null
        );

        return response()->json([
            'available' => $available,
            'message' => $available ? 'Code is available' : 'This code is already in use'
        ]);
    }

    /**
     * Validate if a coupon name is available
     */
    public function validateName(Request $request)
    {
        $available = $this->couponService->isNameAvailable(
            $request->name,
            $request->filled('except_id') ? $request->except_id : null
        );

        return response()->json([
            'available' => $available,
            'message' => $available ? 'Name is available' : 'A coupon with this name already exists'
        ]);
    }

    /**
     * Check coupon compatibility for stacking
     */
    public function checkStacking(Request $request)
    {
        return $this->couponService->checkStacking($request);
    }


    /**
     * Get coupon statistics and analytics
     */
    public function statistics(Request $request)
    {
        return $this->couponService->statistics($request);
    }

    /**
     * Export coupon usage data
     */
    public function export(Request $request)
    {
        return $this->couponService->export($request);
    }



    /**
     * Get active coupons for customer type
     */
    public function getActiveForCustomerType(Request $request)
    {
        $customerType = $request->get('customer_type', 'all');
        $coupons = $this->couponService->getActiveCouponsForCustomerType($customerType);

        return response()->json($coupons);
    }
}
