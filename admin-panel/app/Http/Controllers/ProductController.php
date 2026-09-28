<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(protected ProductService $service) {}

    public function index(Request $request)
    {
        $products = $this->service->index($request);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        $data = $this->service->getCreateData();
        return view('products.add.add', $data);
    }

    public function store(Request $request)
    {
        return $this->service->store($request);
    }

    public function show(Product $product)
    {
        $product = $this->service->show($product);
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $data = $this->service->getEditData($product);
        return view('products.edit', $data);
    }

    public function duplicate(Product $product)
    {
        return $this->service->duplicate($product);
    }

    public function update(Request $request, Product $product)
    {
        return $this->service->update($request, $product);
    }

    public function destroy(Product $product)
    {
        return $this->service->destroy($product);
    }

    public function publicArabic()
    {
        $products = $this->service->publicArabic();
        return view('products.public.arabic', compact('products'));
    }

    public function publicEnglish()
    {
        $products = $this->service->publicEnglish();
        return view('products.public.english', compact('products'));
    }

    public function removeStudyResource(Request $request, Product $product)
    {
        return $this->service->removeStudyResource($request, $product);
    }
}
