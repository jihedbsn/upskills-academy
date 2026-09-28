<?php

namespace App\Http\Controllers;

use App\Models\ProductAcceuil;
use App\Services\ProductAcceuilService;
use Illuminate\Http\Request;

class ProductAcceuilController extends Controller
{
    protected ProductAcceuilService $productAcceuilService;

    public function __construct(ProductAcceuilService $productAcceuilService)
    {
        $this->productAcceuilService = $productAcceuilService;
    }

    public function index(Request $request)
    {
        return $this->productAcceuilService->index($request);
    }

    public function create()
    {
        return $this->productAcceuilService->create();
    }

    public function store(Request $request)
    {
        return $this->productAcceuilService->store($request);
    }

    public function show(ProductAcceuil $productAcceuil)
    {
        return $this->productAcceuilService->show($productAcceuil);
    }

    public function edit(ProductAcceuil $productAcceuil)
    {
        return $this->productAcceuilService->edit($productAcceuil);
    }

    public function update(Request $request, ProductAcceuil $productAcceuil)
    {
        return $this->productAcceuilService->update($request, $productAcceuil);
    }

    public function destroy($id)
    {
        return $this->productAcceuilService->destroy($id);
    }
}
