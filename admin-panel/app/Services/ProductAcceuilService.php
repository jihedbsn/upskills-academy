<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductAcceuil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProductAcceuilService
{
    public function index(Request $request)
    {
        $query = ProductAcceuil::withProduct();

        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $sortBy    = $request->get('sort_by', 'order');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $productsAcceuil = $query->paginate(10);

        return view('products-acceuil.index', compact('productsAcceuil'));
    }

    public function create()
    {
        $availableProducts = Product::with('variations')
            ->whereNotIn('id', function ($query) {
                $query->select('product_id')->from('products_acceuil');
            })->get();

        return view('products-acceuil.create', compact('availableProducts'));
    }

    public function store(Request $request)
    {
        Log::info('ProductAcceuil creation attempt', $request->all());

        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id|unique:products_acceuil,product_id',
            'order'      => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed', $validator->errors()->toArray());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $data              = $request->all();
            $data['is_active'] = $request->has('is_active');

            Log::info('Creating ProductAcceuil with data', $data);
            $productAcceuil = ProductAcceuil::create($data);
            Log::info('ProductAcceuil created successfully', ['id' => $productAcceuil->id]);

            return redirect()->route('products-acceuil.index')
                ->with('success', 'Produit ajouté à la page d\'accueil avec succès.');
        } catch (\Exception $e) {
            Log::error('Error creating ProductAcceuil', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->withErrors(['error' => 'Erreur lors de l\'ajout du produit: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(ProductAcceuil $productAcceuil)
    {
        $productAcceuil->load(['product', 'product.variations']);
        return view('products-acceuil.show', compact('productAcceuil'));
    }

    public function edit(ProductAcceuil $productAcceuil)
    {
        $productAcceuil->load(['product', 'product.variations']);
        return view('products-acceuil.edit', compact('productAcceuil'));
    }

    public function update(Request $request, ProductAcceuil $productAcceuil)
    {
        $validator = Validator::make($request->all(), [
            'order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $data              = $request->all();
            $data['is_active'] = $request->has('is_active');

            $productAcceuil->update($data);

            return redirect()->route('products-acceuil.index')
                ->with('success', 'Produit mis à jour avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(int $id)
    {
        try {
            Log::info('Attempting to delete ProductAcceuil', ['id' => $id]);

            $productAcceuil = ProductAcceuil::findOrFail($id);

            Log::info('ProductAcceuil found', ['id' => $productAcceuil->id]);
            $productAcceuil->delete();
            Log::info('ProductAcceuil deleted successfully', ['id' => $productAcceuil->id]);

            return redirect()->route('products-acceuil.index')
                ->with('success', 'Produit retiré de la page d\'accueil avec succès.');
        } catch (\Exception $e) {
            Log::error('Error deleting ProductAcceuil', [
                'id'      => $id,
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->withErrors(['error' => 'Erreur lors de la suppression: ' . $e->getMessage()]);
        }
    }
}
