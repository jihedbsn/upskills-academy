<?php

namespace App\Services;

use App\Models\Sujet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SujetService
{
    public function index(Request $request): array
    {
        $query = Sujet::query();

        if ($request->filled('lang')) {
            $query->where('lang', $request->lang);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $sujets = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();

        $stats = [
            'total'   => Sujet::count(),
            'arabic'  => Sujet::where('lang', 'ar')->count(),
            'english' => Sujet::where('lang', 'en')->count(),
            'fa'      => Sujet::where('type', 'fa')->count(),
            'fi'      => Sujet::where('type', 'fi')->count(),
            'pt'      => Sujet::where('type', 'pt')->count(),
            'autres'  => Sujet::where('type', 'autres')->count(),
        ];

        return compact('sujets', 'stats');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|string|max:5000',
            'lang'        => 'required|in:ar,en',
            'type'        => 'required|in:fa,fi,pt,autres',
        ], [
            'description.required' => 'The description is required.',
            'description.max'      => 'The description must not exceed 5000 characters.',
            'lang.required'        => 'The language is required.',
            'lang.in'              => 'The language must be "ar" or "en".',
            'type.required'        => 'The type is required.',
            'type.in'              => 'The type must be valid.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Sujet::create($request->all());

        return redirect()->route('sujets.index')->with('success', 'Subject created successfully.');
    }

    public function update(Request $request, Sujet $sujet)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|string|max:5000',
            'lang'        => 'required|in:ar,en',
            'type'        => 'required|in:fa,fi,pt,autres',
        ], [
            'description.required' => 'The description is required.',
            'description.max'      => 'The description must not exceed 5000 characters.',
            'lang.required'        => 'The language is required.',
            'lang.in'              => 'The language must be "ar" or "en".',
            'type.required'        => 'The type is required.',
            'type.in'              => 'The type must be valid.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $sujet->update($request->all());

        return redirect()->route('sujets.index')->with('success', 'Subject updated successfully.');
    }

    public function destroy(Sujet $sujet)
    {
        $sujet->delete();
        return redirect()->route('sujets.index')->with('success', 'Subject deleted successfully.');
    }
}
