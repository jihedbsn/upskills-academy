<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderFrontendService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected OrderFrontendService $orderFrontendService;

    public function __construct(OrderFrontendService $orderFrontendService)
    {
        $this->orderFrontendService = $orderFrontendService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->orderFrontendService->index();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return $this->orderFrontendService->create();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->orderFrontendService->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        return $this->orderFrontendService->show($order);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        return $this->orderFrontendService->edit($order);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        return $this->orderFrontendService->update($request, $order);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        return $this->orderFrontendService->destroy($order);
    }

    /**
     * Méthodes pour le front-end (étudiants)
     */
    public function addToCart(Request $request)
    {
        return $this->orderFrontendService->addToCart($request);
    }

    public function checkout(Request $request)
    {
        return $this->orderFrontendService->checkout($request);
    }
}
