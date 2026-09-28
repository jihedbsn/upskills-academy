<?php

namespace App\Services;

use App\Models\AboutAcceuil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AboutAcceuilService
{
    public function index(Request $request)
    {
        $query = AboutAcceuil::query();

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

        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $abouts = $query->paginate(10);

        return view('about-acceuil.index', compact('abouts'));
    }

    public function create()
    {
        return view('about-acceuil.create');
    }

    public function store(Request $request)
    {
        Log::info('About creation attempt', $request->all());

        $validator = Validator::make($request->all(), [
            'description_en' => 'required|string',
            'description_ar' => 'required|string',
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

            Log::info('Creating about with data', $data);

            $about = AboutAcceuil::create($data);

            Log::info('About created successfully', ['id' => $about->id]);

            return redirect()->route('about-acceuil.index')
                ->with('success', 'Section About créée avec succès.');
        } catch (\Exception $e) {
            Log::error('Error creating about', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->withErrors(['error' => 'Erreur lors de la création de la section About: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(AboutAcceuil $aboutAcceuil)
    {
        return view('about-acceuil.show', compact('aboutAcceuil'));
    }

    public function edit(AboutAcceuil $aboutAcceuil)
    {
        return view('about-acceuil.edit', compact('aboutAcceuil'));
    }

    public function update(Request $request, AboutAcceuil $aboutAcceuil)
    {
        $validator = Validator::make($request->all(), [
            'description_en' => 'required|string',
            'description_ar' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = $request->all();
            $data['is_active'] = $request->has('is_active');

            $aboutAcceuil->update($data);

            return redirect()->route('about-acceuil.index')
                ->with('success', 'Section About mise à jour avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(AboutAcceuil $aboutAcceuil)
    {
        try {
            $aboutAcceuil->delete();
            return redirect()->route('about-acceuil.index')
                ->with('success', 'Section About supprimée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Erreur lors de la suppression: ' . $e->getMessage()]);
        }
    }
}
