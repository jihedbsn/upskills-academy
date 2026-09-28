<?php

namespace App\Services;

use App\Models\Carousel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CarouselService
{
    public function index(Request $request)
    {
        $query = Carousel::query();

        if ($request->has('search') && !empty($request->search)) {
            $query->search($request->search);
        }

        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $sortBy = $request->get('sort_by', 'order');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $carousels = $query->paginate(10);

        return view('carousels.index', compact('carousels'));
    }

    public function create()
    {
        return view('carousels.create');
    }

    public function store(Request $request)
    {
        Log::info('Carousel creation attempt', $request->all());

        $validator = Validator::make($request->all(), [
            'slug_ar' => 'required|string|max:255',
            'slug_en' => 'required|string|max:255',
            'description_ar' => 'required|string',
            'description_en' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:100000',
            'order' => 'nullable|integer|min:0'
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed', $validator->errors()->toArray());

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = $request->all();
            $data['is_active'] = $request->has('is_active');

            if ($request->hasFile('image')) {
                $data['image'] = $this->uploadImage($request->file('image'), $data['slug_en']);
            }

            if (empty($data['slug_ar'])) {
                $data['slug_ar'] = Str::slug($data['slug_ar']);
            }
            if (empty($data['slug_en'])) {
                $data['slug_en'] = Str::slug($data['slug_en']);
            }

            Log::info('Creating carousel with data', $data);

            $carousel = Carousel::create($data);

            Log::info('Carousel created successfully', ['id' => $carousel->id]);

            return redirect()->route('carousels.index')
                ->with('success', 'Carousel créé avec succès.');
        } catch (\Exception $e) {
            Log::error('Error creating carousel', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withErrors(['error' => 'Erreur lors de la création du carousel: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(Carousel $carousel)
    {
        return view('carousels.show', compact('carousel'));
    }

    public function edit(Carousel $carousel)
    {
        return view('carousels.edit', compact('carousel'));
    }

    public function update(Request $request, Carousel $carousel)
    {
        $validator = Validator::make($request->all(), [
            'slug_ar' => 'required|string|max:255',
            'slug_en' => 'required|string|max:255',
            'description_ar' => 'required|string',
            'description_en' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:100000',
            'order' => 'nullable|integer|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = $request->all();
            $data['is_active'] = $request->has('is_active');

            if ($request->hasFile('image')) {
                $this->deleteImageIfExists($carousel->image);
                $data['image'] = $this->uploadImage($request->file('image'), $data['slug_en']);
            }

            if (empty($data['slug_ar'])) {
                $data['slug_ar'] = Str::slug($data['slug_ar']);
            }
            if (empty($data['slug_en'])) {
                $data['slug_en'] = Str::slug($data['slug_en']);
            }

            $carousel->update($data);

            return redirect()->route('carousels.index')
                ->with('success', 'Carousel mis à jour avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(Carousel $carousel)
    {
        try {
            $this->deleteImageIfExists($carousel->image);

            $carousel->delete();

            return redirect()->route('carousels.index')
                ->with('success', 'Carousel supprimé avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Erreur lors de la suppression: ' . $e->getMessage()]);
        }
    }

    private function uploadImage($image, string $slugEn): string
    {
        $imageName = time() . '_' . Str::slug($slugEn) . '.' . $image->getClientOriginalExtension();
        $uploadPath = public_path('uploads/carousels');

        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $image->move($uploadPath, $imageName);

        return $imageName;
    }

    private function deleteImageIfExists(?string $imageName): void
    {
        if ($imageName && file_exists(public_path('uploads/carousels/' . $imageName))) {
            unlink(public_path('uploads/carousels/' . $imageName));
        }
    }
}
