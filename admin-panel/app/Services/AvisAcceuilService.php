<?php

namespace App\Services;

use App\Models\AvisAcceuil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AvisAcceuilService
{
    public function index(Request $request)
    {
        $query = AvisAcceuil::query();

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

        $avis = $query->paginate(10);

        return view('avis-acceuil.index', compact('avis'));
    }

    public function create()
    {
        return view('avis-acceuil.create');
    }

    public function store(Request $request)
    {
        Log::info('Avis creation attempt', $request->all());

        $validator = Validator::make($request->all(), [
            'client' => 'required|string|max:255',
            'avis_en' => 'required|string',
            'avis_ar' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:104800',
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
                $data['image'] = $this->uploadImage($request->file('image'), $data['client']);
            }

            Log::info('Creating avis with data', $data);

            $avis = AvisAcceuil::create($data);

            Log::info('Avis created successfully', ['id' => $avis->id]);

            return redirect()->route('avis-acceuil.index')
                ->with('success', 'Avis client créé avec succès.');
        } catch (\Exception $e) {
            Log::error('Error creating avis', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withErrors(['error' => 'Erreur lors de la création de l\'avis: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(AvisAcceuil $avisAcceuil)
    {
        return view('avis-acceuil.show', compact('avisAcceuil'));
    }

    public function edit(AvisAcceuil $avisAcceuil)
    {
        return view('avis-acceuil.edit', compact('avisAcceuil'));
    }

    public function update(Request $request, AvisAcceuil $avisAcceuil)
    {
        $validator = Validator::make($request->all(), [
            'client' => 'required|string|max:255',
            'avis_en' => 'required|string',
            'avis_ar' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:104800',
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
                $this->deleteImageIfExists($avisAcceuil->image);
                $data['image'] = $this->uploadImage($request->file('image'), $data['client']);
            }

            $avisAcceuil->update($data);

            return redirect()->route('avis-acceuil.index')
                ->with('success', 'Avis client mis à jour avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(AvisAcceuil $avisAcceuil)
    {
        try {
            $this->deleteImageIfExists($avisAcceuil->image);

            $avisAcceuil->delete();

            return redirect()->route('avis-acceuil.index')
                ->with('success', 'Avis client supprimé avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Erreur lors de la suppression: ' . $e->getMessage()]);
        }
    }

    private function uploadImage($image, string $client): string
    {
        $imageName = time() . '_' . Str::slug($client) . '.' . $image->getClientOriginalExtension();
        $uploadPath = public_path('uploads/avis');

        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $image->move($uploadPath, $imageName);

        return $imageName;
    }

    private function deleteImageIfExists(?string $imageName): void
    {
        if ($imageName && file_exists(public_path('uploads/avis/' . $imageName))) {
            unlink(public_path('uploads/avis/' . $imageName));
        }
    }
}
