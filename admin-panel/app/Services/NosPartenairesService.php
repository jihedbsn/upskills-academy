<?php

namespace App\Services;

use App\Models\NosPartenaires;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NosPartenairesService
{
    public function index()
    {
        $partenaires = NosPartenaires::orderBy('order')->get();
        return view('admin.nos-partenaires.index', compact('partenaires'));
    }

    public function create()
    {
        return view('admin.nos-partenaires.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'url' => 'nullable|url',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/partenaires'), $fileName);
            $data['logo'] = 'partenaires/' . $fileName;
        }

        $data['order'] = $data['order'] ?? NosPartenaires::max('order') + 1;
        $data['is_active'] = $request->has('is_active');

        NosPartenaires::create($data);

        return redirect()->route('nos-partenaires.index')
            ->with('success', 'Partenaire créé avec succès.');
    }

    public function show(NosPartenaires $nosPartenaires)
    {
        return view('admin.nos-partenaires.show', compact('nosPartenaires'));
    }

    public function edit(NosPartenaires $nosPartenaires)
    {
        return view('admin.nos-partenaires.edit', compact('nosPartenaires'));
    }

    public function update(Request $request, NosPartenaires $nosPartenaires)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'url' => 'nullable|url',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        if ($request->hasFile('logo')) {
            if ($nosPartenaires->logo) {
                $oldLogoPath = public_path('uploads/' . $nosPartenaires->logo);
                if (file_exists($oldLogoPath)) {
                    unlink($oldLogoPath);
                }
            }

            $file = $request->file('logo');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/partenaires'), $fileName);
            $data['logo'] = 'partenaires/' . $fileName;
        }

        $data['is_active'] = $request->has('is_active');

        $nosPartenaires->update($data);

        return redirect()->route('nos-partenaires.index')
            ->with('success', 'Partenaire mis à jour avec succès.');
    }

    public function destroy(NosPartenaires $nosPartenaires)
    {
        if ($nosPartenaires->logo) {
            $logoPath = public_path('uploads/' . $nosPartenaires->logo);
            if (file_exists($logoPath)) {
                unlink($logoPath);
            }
        }

        $nosPartenaires->delete();

        return redirect()->route('nos-partenaires.index')
            ->with('success', 'Partenaire supprimé avec succès.');
    }

    public function updateOrder(Request $request)
    {
        $partenaires = $request->input('partenaires');

        foreach ($partenaires as $index => $partenaireId) {
            NosPartenaires::where('id', $partenaireId)->update(['order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }
}
