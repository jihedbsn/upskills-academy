<?php

namespace App\Services;

use App\Models\Package;
use App\Models\PackageProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PackageService
{
    public function index()
    {
        $packages = Package::with(['packageProducts.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.packages.index', compact('packages'));
    }

    public function create()
    {
        $products = Product::query()
            ->with(['variations' => function ($query) {
                $query->where('langue', 'en');
            }])
            ->get();

        return view('admin.packages.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'required|string|max:255',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'products' => 'required|array|min:1',
            'products.*' => 'exists:products,id',
            'discount_types' => 'required|array|min:1',
            'discount_types.*' => 'in:percentage,fixed',
            'reductions' => 'required|array|min:1',
            'reductions.*' => 'numeric|min:0',
            'is_active' => 'boolean'
        ]);

        $imageName = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . Str::slug($request->title) . '.' . $image->getClientOriginalExtension();

            $uploadPath = public_path('uploads/package');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $image->move($uploadPath, $imageName);
        }

        $package = Package::create([
            'title' => $request->title,
            'title_ar' => $request->title_ar,
            'description' => $request->description,
            'description_ar' => $request->description_ar,
            'image' => $imageName,
            'is_active' => $request->has('is_active')
        ]);

        foreach ($request->products as $index => $productId) {
            $discountType = $request->discount_types[$index] ?? 'percentage';
            $reductionValue = $request->reductions[$index] ?? 0;

            PackageProduct::create([
                'package_id' => $package->id,
                'product_id' => $productId,
                'discount_type' => $discountType,
                'valeur_reduction' => $discountType === 'percentage' ? $reductionValue : 0,
                'fixed_discount' => $discountType === 'fixed' ? $reductionValue : null,
                'is_active' => true
            ]);
        }

        return redirect()->route('packages.index')
            ->with('success', 'Package created successfully!');
    }

    public function show(Package $package)
    {
        $package->load(['packageProducts.product.variations' => function ($query) {
            $query->where('langue', 'en');
        }]);

        return view('admin.packages.show', compact('package'));
    }

    public function edit(Package $package)
    {
        $products = Product::query()
            ->with(['variations' => function ($query) {
                $query->where('langue', 'en');
            }])
            ->get();

        $package->load('packageProducts');
        $selectedProducts = $package->packageProducts->pluck('product_id')->toArray();
        $reductions = $package->packageProducts->mapWithKeys(function ($pp) {
            $discountType = $pp->discount_type ?? 'percentage';
            $value = ($discountType === 'fixed') ? ($pp->fixed_discount ?? 0) : ($pp->valeur_reduction ?? 0);

            return [$pp->product_id => [
                'type' => $discountType,
                'value' => $value
            ]];
        })->toArray();

        return view('admin.packages.edit', compact('package', 'products', 'selectedProducts', 'reductions'));
    }

    public function update(Request $request, Package $package)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'required|string|max:255',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'products' => 'required|array|min:1',
            'products.*' => 'exists:products,id',
            'discount_types' => 'required|array|min:1',
            'discount_types.*' => 'in:percentage,fixed',
            'reductions' => 'required|array|min:1',
            'reductions.*' => 'numeric|min:0',
            'is_active' => 'boolean'
        ]);

        $imageName = $package->image;
        if ($request->hasFile('image')) {
            if ($package->image && file_exists(public_path('uploads/package/' . $package->image))) {
                unlink(public_path('uploads/package/' . $package->image));
            }

            $image = $request->file('image');
            $imageName = time() . '_' . Str::slug($request->title) . '.' . $image->getClientOriginalExtension();

            $uploadPath = public_path('uploads/package');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $image->move($uploadPath, $imageName);
        }

        $package->update([
            'title' => $request->title,
            'title_ar' => $request->title_ar,
            'description' => $request->description,
            'description_ar' => $request->description_ar,
            'image' => $imageName,
            'is_active' => $request->has('is_active')
        ]);

        $package->packageProducts()->delete();

        Log::info('Package Update Debug', [
            'products' => $request->products,
            'discount_types' => $request->discount_types,
            'reductions' => $request->reductions
        ]);

        foreach ($request->products as $index => $productId) {
            $discountType = $request->discount_types[$index] ?? 'percentage';
            $reductionValue = $request->reductions[$index] ?? 0;

            $data = [
                'package_id' => $package->id,
                'product_id' => $productId,
                'discount_type' => $discountType,
                'valeur_reduction' => $discountType === 'percentage' ? $reductionValue : 0,
                'fixed_discount' => $discountType === 'fixed' ? $reductionValue : null,
                'is_active' => true
            ];

            Log::info('Creating PackageProduct', $data);
            PackageProduct::create($data);
        }

        return redirect()->route('packages.index')
            ->with('success', 'Package updated successfully!');
    }

    public function destroy(Package $package)
    {
        if ($package->image && file_exists(public_path('uploads/package/' . $package->image))) {
            unlink(public_path('uploads/package/' . $package->image));
        }

        $package->delete();

        return redirect()->route('packages.index')
            ->with('success', 'Package deleted successfully!');
    }

    public function toggle(Package $package)
    {
        $package->update(['is_active' => !$package->is_active]);

        $status = $package->is_active ? 'activated' : 'deactivated';
        return redirect()->back()
            ->with('success', "Package {$status} successfully!");
    }

    public function statistics()
    {
        $stats = [
            'total' => Package::count(),
            'active' => Package::active()->count(),
            'inactive' => Package::where('is_active', false)->count(),
            'total_products' => PackageProduct::count(),
            'active_products' => PackageProduct::active()->count(),
            'average_reduction' => PackageProduct::active()->avg('valeur_reduction') ?? 0
        ];

        return view('admin.packages.statistics', compact('stats'));
    }
}
