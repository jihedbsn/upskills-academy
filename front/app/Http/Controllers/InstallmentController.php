<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\InstallmentFrontendService;
use Illuminate\Http\Request;

/**
 * InstallmentController
 *
 * Handles student-facing installment operations:
 * - Viewing installment plans and payment schedule
 * - Initiating installment checkout
 * - Processing installment payments
 */
class InstallmentController extends Controller
{
    protected InstallmentFrontendService $installmentFrontendService;

    public function __construct(InstallmentFrontendService $installmentFrontendService)
    {
        $this->installmentFrontendService = $installmentFrontendService;
    }

    /**
     * Display the student's installment orders dashboard.
     */
    public function index()
    {
        return $this->installmentFrontendService->index();
    }

    /**
     * Display details of a specific installment order.
     */
    public function show($locale, \App\Models\OrderSpecifique $orderSpecifique)
    {
        return $this->installmentFrontendService->show($locale, $orderSpecifique);
    }

    /**
     * Show payment method selection page for the next payable installment.
     */
    public function paymentOptions($locale, $orderSpecifique)
    {
        return $this->installmentFrontendService->paymentOptions($locale, $orderSpecifique);
    }

    /**
     * Show the installment checkout page for a course.
     */
    public function checkout($locale, Product $product)
    {
        return $this->installmentFrontendService->checkout($locale, $product);
    }

    /**
     * Process the installment checkout.
     * Creates the installment order and sends the student to payment options (legacy).
     */
    public function processCheckout(Request $request, $locale, Product $product)
    {
        return $this->installmentFrontendService->processCheckout($request, $locale, $product);
    }

    /**
     * Create installment order and continue: Stripe, cash, or bank-transfer instructions.
     */
    public function checkoutStartPayment(Request $request, $locale, Product $product)
    {
        return $this->installmentFrontendService->checkoutStartPayment($request, $locale, $product);
    }

    /**
     * Handle successful Stripe payment for an installment.
     */
    public function paymentSuccess(Request $request)
    {
        return $this->installmentFrontendService->paymentSuccess($request);
    }

    /**
     * Handle cancelled Stripe payment for an installment.
     */
    public function paymentCancel(Request $request)
    {
        return $this->installmentFrontendService->paymentCancel($request);
    }

    /**
     * Pay a specific installment (for subsequent installments).
     */
    public function payInstallment(Request $request, $locale, $orderSpecifique)
    {
        return $this->installmentFrontendService->payInstallment($request, $locale, $orderSpecifique);
    }

    /**
     * Show suspended account page.
     */
    public function suspended()
    {
        return $this->installmentFrontendService->suspended();
    }
}
