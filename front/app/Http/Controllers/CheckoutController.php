<?php

namespace App\Http\Controllers;

use App\Services\CheckoutService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    protected CheckoutService $checkoutService;

    public function __construct(CheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }

    /**
     * Afficher la page de checkout
     */
    public function index()
    {
        return $this->checkoutService->index();
    }

    /**
     * Traiter la commande
     */
    public function process(Request $request)
    {
        return $this->checkoutService->process($request);
    }

    /**
     * Handle successful Stripe payment
     */
    public function stripeSuccess(Request $request)
    {
        return $this->checkoutService->stripeSuccess($request);
    }

    /**
     * Handle cancelled Stripe payment
     */
    public function stripeCancel()
    {
        return $this->checkoutService->stripeCancel();
    }

    /**
     * Apply points for discount at checkout
     */
    public function applyPoints(Request $request)
    {
        return $this->checkoutService->applyPoints($request);
    }

    /**
     * Remove applied points from checkout
     */
    public function removePoints()
    {
        return $this->checkoutService->removePoints();
    }
}
