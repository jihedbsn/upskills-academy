<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CourseExtensionService;
use Illuminate\Http\Request;

class CourseExtensionController extends Controller
{
    protected CourseExtensionService $courseExtensionService;

    public function __construct(CourseExtensionService $courseExtensionService)
    {
        $this->courseExtensionService = $courseExtensionService;
    }

    /**
     * Show list of courses that can be extended (expired or expiring soon)
     */
    public function index()
    {
        return $this->courseExtensionService->index();
    }

    /**
     * Show the extension checkout page for a specific course
     */
    public function checkout($locale, Product $product)
    {
        return $this->courseExtensionService->checkout((string) $locale, $product);
    }

    /**
     * Process the extension payment
     */
    public function processPayment(Request $request, $locale, Product $product)
    {
        return $this->courseExtensionService->processPayment($request, (string) $locale, $product);
    }

    /**
     * Handle successful Stripe extension payment
     */
    public function stripeSuccess(Request $request)
    {
        return $this->courseExtensionService->stripeSuccess($request);
    }

    /**
     * Handle cancelled Stripe extension payment
     */
    public function stripeCancel()
    {
        return $this->courseExtensionService->stripeCancel();
    }
}
