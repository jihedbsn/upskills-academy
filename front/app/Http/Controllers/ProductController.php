<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ProductManagementService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected ProductManagementService $productManagementService;

    public function __construct(ProductManagementService $productManagementService)
    {
        $this->productManagementService = $productManagementService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->productManagementService->index();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return $this->productManagementService->create();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->productManagementService->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return $this->productManagementService->show($product);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return $this->productManagementService->edit($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        return $this->productManagementService->update($request, $product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        return $this->productManagementService->destroy($product);
    }
}
