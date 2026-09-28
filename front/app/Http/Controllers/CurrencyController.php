<?php

namespace App\Http\Controllers;

use App\Services\CurrencyFrontendService;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    protected CurrencyFrontendService $currencyFrontendService;

    public function __construct(CurrencyFrontendService $currencyFrontendService)
    {
        $this->currencyFrontendService = $currencyFrontendService;
    }

    /**
     * Get available currencies
     */
    public function index()
    {
        return $this->currencyFrontendService->index();
    }

    /**
     * Set the selected currency
     */
    public function set(Request $request)
    {
        return $this->currencyFrontendService->set($request);
    }

    /**
     * Detect currency based on geo-location
     */
    public function detect()
    {
        return $this->currencyFrontendService->detect();
    }

    /**
     * Convert amount to selected currency
     */
    public function convert(Request $request)
    {
        return $this->currencyFrontendService->convert($request);
    }

    /**
     * Get detected location info
     */
    public function location()
    {
        return $this->currencyFrontendService->location();
    }

    /**
     * Force re-detect currency from geo-location
     */
    public function redetect()
    {
        return $this->currencyFrontendService->redetect();
    }
}
