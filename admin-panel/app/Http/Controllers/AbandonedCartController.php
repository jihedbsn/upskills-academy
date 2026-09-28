<?php

namespace App\Http\Controllers;

use App\Services\AbandonedCartService;
use Illuminate\Http\Request;

class AbandonedCartController extends Controller
{
    protected AbandonedCartService $abandonedCartService;

    public function __construct(AbandonedCartService $abandonedCartService)
    {
        $this->abandonedCartService = $abandonedCartService;
    }

    /**
     * Display abandoned cart dashboard.
     */
    public function index(Request $request)
    {
        return $this->abandonedCartService->index($request);
    }

    /**
     * Show details of a specific abandoned cart.
     */
    public function show($id)
    {
        return $this->abandonedCartService->show($id);
    }

    /**
     * Export abandoned carts data.
     */
    public function export(Request $request)
    {
        return $this->abandonedCartService->export($request);
    }

    /**
     * Force send reminder for a specific cart (for legacy/old carts).
     */
    public function sendReminder($id)
    {
        return $this->abandonedCartService->sendReminder($id);
    }
}
