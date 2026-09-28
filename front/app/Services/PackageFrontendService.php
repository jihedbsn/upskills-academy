<?php

namespace App\Services;

use App\Models\Package;

class PackageFrontendService
{
    public function index()
    {
        $packages = Package::active()
            ->with(['packageProducts.product'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('packages.index', compact('packages'));
    }

    public function show($locale, $id)
    {
        $package = Package::active()
            ->with(['packageProducts.product.category', 'packageProducts.product.teacher'])
            ->findOrFail($id);

        $products = $package->packageProducts()
            ->with('product.category', 'product.teacher')
            ->get();

        return view('packages.show', compact('package', 'products'));
    }
}
