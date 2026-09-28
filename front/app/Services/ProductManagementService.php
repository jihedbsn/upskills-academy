<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Country;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductManagementService
{
    public function index()
    {
        $products = Product::with(['category', 'teacher', 'country', 'variations'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        $teachers = Teacher::where('is_active', true)->get();
        $countries = Country::where('is_active', true)->get();

        return view('admin.products.create', compact('categories', 'teachers', 'countries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'categories_id' => 'required|exists:categories,id',
            'period' => 'required|string|max:255',
            'point' => 'required|numeric|min:0',
            'video' => 'nullable|string|max:255',
            'promo_points' => 'nullable|numeric|min:0',
            'langue' => 'required|string|in:en,ar',
            'statut' => 'required|boolean',
            'online' => 'required|boolean',
            'classroom' => 'required|boolean',
            'teacher_id' => 'required|exists:teachers,id',
            'country_id' => 'required|exists:countries,id',
            'goverrnement' => 'nullable|string|max:255',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'prix' => 'required|numeric|min:0',
            'is_exam_video' => 'boolean',
            'is_stage' => 'boolean',
            'is_classroom' => 'boolean',
            'is_zoom' => 'boolean',
            'titre_en' => 'required|string|max:255',
            'titre_ar' => 'required|string|max:255',
            'description_en' => 'required|string',
            'description_ar' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads/products/images'), $imageName);
            }

            $product = Product::create([
                'image' => $imageName ?? null,
                'iscach' => $request->iscach ?? false,
                'categories_id' => $request->categories_id,
                'period' => $request->period,
                'point' => $request->point,
                'video' => $request->video,
                'promo_points' => $request->promo_points ?? 0,
                'langue' => $request->langue,
                'statut' => $request->statut,
                'online' => $request->online,
                'classroom' => $request->classroom,
                'teacher_id' => $request->teacher_id,
                'country_id' => $request->country_id,
                'goverrnement' => $request->goverrnement,
                'date_debut' => $request->date_debut,
                'date_fin' => $request->date_fin,
                'prix' => $request->prix,
                'is_exam_video' => $request->is_exam_video ?? false,
                'is_stage' => $request->is_stage ?? false,
                'is_classroom' => $request->is_classroom ?? false,
                'is_zoom' => $request->is_zoom ?? false,
            ]);

            ProductVariation::create([
                'products_id' => $product->id,
                'langue' => 'en',
                'titre' => $request->titre_en,
                'description' => $request->description_en,
            ]);

            ProductVariation::create([
                'products_id' => $product->id,
                'langue' => 'ar',
                'titre' => $request->titre_ar,
                'description' => $request->description_ar,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('messages.course_created_successfully'),
                'redirect' => route('admin.products.index'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => __('messages.error_creating_course') . ': ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show(Product $product)
    {
        $product->load(['category', 'teacher', 'country', 'variations', 'studies', 'types']);

        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->get();
        $teachers = Teacher::where('is_active', true)->get();
        $countries = Country::where('is_active', true)->get();

        $product->load('variations');

        return view('admin.products.edit', compact('product', 'categories', 'teachers', 'countries'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'categories_id' => 'required|exists:categories,id',
            'period' => 'required|string|max:255',
            'point' => 'required|numeric|min:0',
            'video' => 'nullable|string|max:255',
            'promo_points' => 'nullable|numeric|min:0',
            'langue' => 'required|string|in:en,ar',
            'statut' => 'required|boolean',
            'online' => 'required|boolean',
            'classroom' => 'required|boolean',
            'teacher_id' => 'required|exists:teachers,id',
            'country_id' => 'required|exists:countries,id',
            'goverrnement' => 'nullable|string|max:255',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'prix' => 'required|numeric|min:0',
            'is_exam_video' => 'boolean',
            'is_stage' => 'boolean',
            'is_classroom' => 'boolean',
            'is_zoom' => 'boolean',
            'titre_en' => 'required|string|max:255',
            'titre_ar' => 'required|string|max:255',
            'description_en' => 'required|string',
            'description_ar' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->except(['image', 'titre_en', 'titre_ar', 'description_en', 'description_ar']);

            if ($request->hasFile('image')) {
                if ($product->image && file_exists(public_path('uploads/products/images/' . $product->image))) {
                    unlink(public_path('uploads/products/images/' . $product->image));
                }

                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads/products/images'), $imageName);
                $data['image'] = $imageName;
            }

            $product->update($data);

            $product->variations()->where('langue', 'en')->update([
                'titre' => $request->titre_en,
                'description' => $request->description_en,
            ]);

            $product->variations()->where('langue', 'ar')->update([
                'titre' => $request->titre_ar,
                'description' => $request->description_ar,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('messages.course_updated_successfully'),
                'redirect' => route('admin.products.index'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => __('messages.error_updating_course') . ': ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Product $product)
    {
        try {
            DB::beginTransaction();

            if ($product->image && file_exists(public_path('uploads/products/images/' . $product->image))) {
                unlink(public_path('uploads/products/images/' . $product->image));
            }

            $product->variations()->delete();
            $product->studies()->delete();
            $product->types()->delete();

            $product->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('messages.course_deleted_successfully'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => __('messages.error_deleting_course') . ': ' . $e->getMessage(),
            ], 500);
        }
    }
}
