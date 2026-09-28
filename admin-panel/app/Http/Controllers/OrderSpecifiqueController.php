<?php

namespace App\Http\Controllers;

use App\Models\Installment;
use App\Models\OrderSpecifique;
use App\Services\OrderSpecifiqueService;
use Illuminate\Http\Request;

class OrderSpecifiqueController extends Controller
{
    protected OrderSpecifiqueService $orderSpecifiqueService;

    public function __construct(OrderSpecifiqueService $orderSpecifiqueService)
    {
        $this->orderSpecifiqueService = $orderSpecifiqueService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->orderSpecifiqueService->index($request);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return $this->orderSpecifiqueService->create();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->orderSpecifiqueService->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(OrderSpecifique $orderSpecifique)
    {
        return $this->orderSpecifiqueService->show($orderSpecifique);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OrderSpecifique $orderSpecifique)
    {
        return $this->orderSpecifiqueService->edit($orderSpecifique);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OrderSpecifique $orderSpecifique)
    {
        return $this->orderSpecifiqueService->update($request, $orderSpecifique);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OrderSpecifique $orderSpecifique)
    {
        return $this->orderSpecifiqueService->destroy($orderSpecifique);
    }

    /**
     * Add a payment to an order.
     */
    public function addPayment(Request $request, OrderSpecifique $orderSpecifique)
    {
        return $this->orderSpecifiqueService->addPayment($request, $orderSpecifique);
    }

    /**
     * Display detail page for a specific installment.
     */
    public function showInstallment(Installment $installment)
    {
        return $this->orderSpecifiqueService->showInstallment($installment);
    }

    /**
     * Mark an individual installment as paid.
     */
    public function markInstallmentPaid(Request $request, Installment $installment)
    {
        return $this->orderSpecifiqueService->markInstallmentPaid($request, $installment);
    }

    /**
     * Mark an individual installment as pending.
     */
    public function markInstallmentPending(Installment $installment)
    {
        return $this->orderSpecifiqueService->markInstallmentPending($installment);
    }

    /**
     * Update due date for an individual installment.
     */
    public function updateInstallmentDueDate(Request $request, Installment $installment)
    {
        return $this->orderSpecifiqueService->updateInstallmentDueDate($request, $installment);
    }

    /**
     * Update paid date for an individual installment.
     */
    public function updateInstallmentPaidDate(Request $request, Installment $installment)
    {
        return $this->orderSpecifiqueService->updateInstallmentPaidDate($request, $installment);
    }

    /**
     * Download installment payment receipt from user storage.
     */
    public function downloadInstallmentReceipt(Installment $installment)
    {
        return $this->orderSpecifiqueService->downloadInstallmentReceipt($installment);
    }

    /**
     * Get product variations for a specific product.
     */
    public function getProductVariations(Request $request)
    {
        return $this->orderSpecifiqueService->getProductVariations($request);
    }
}
