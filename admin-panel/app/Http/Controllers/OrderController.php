<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(Request $request)
    {
        return $this->orderService->index($request);
    }

    public function create()
    {
        return $this->orderService->create();
    }

    public function store(Request $request)
    {
        return $this->orderService->store($request);
    }

    public function show(Order $order)
    {
        return $this->orderService->show($order);
    }

    public function edit(Order $order)
    {
        return $this->orderService->edit($order);
    }

    public function update(Request $request, Order $order)
    {
        return $this->orderService->update($request, $order);
    }

    public function destroy(Order $order)
    {
        return $this->orderService->destroy($order);
    }

    public function togglePayment(Request $request, Order $order)
    {
        return $this->orderService->togglePayment($request, $order);
    }

    public function approvePayment(Order $order)
    {
        return $this->orderService->approvePayment($order);
    }

    public function rejectPayment(Request $request, Order $order)
    {
        return $this->orderService->rejectPayment($request, $order);
    }

    public function downloadReceipt(Order $order)
    {
        return $this->orderService->downloadReceipt($order);
    }
}
