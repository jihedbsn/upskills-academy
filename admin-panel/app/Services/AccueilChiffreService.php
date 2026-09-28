<?php

namespace App\Services;

use App\Models\AccueilChiffre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AccueilChiffreService
{
    public function index(Request $request)
    {
        $query = AccueilChiffre::query();

        if ($request->has('status') && in_array($request->status, ['active', 'inactive'])) {
            $query->where('is_active', $request->status === 'active');
        }

        $accueilChiffres = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.accueil-chiffres.index', compact('accueilChiffres'));
    }

    public function create()
    {
        return view('admin.accueil-chiffres.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'coach_ready' => 'required|integer|min:0',
            'icone_coach_ready' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'book_of_the_academy' => 'required|integer|min:0',
            'icone_book_of_the_academy' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'registered_student' => 'required|integer|min:0',
            'icone_registered_student' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'training_program' => 'required|integer|min:0',
            'icone_training_program' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only([
            'coach_ready',
            'book_of_the_academy',
            'registered_student',
            'training_program',
        ]);
        $data['is_active'] = $request->has('is_active');

        $imageFields = [
            'icone_coach_ready',
            'icone_book_of_the_academy',
            'icone_registered_student',
            'icone_training_program',
        ];

        foreach ($imageFields as $field) {
            if ($request->hasFile($field)) {
                $imageName = time() . '_' . Str::random(10) . '_' . $field . '.' . $request->file($field)->extension();
                $request->file($field)->move(public_path('uploads/accueil-chiffres'), $imageName);
                $data[$field] = $imageName;
            }
        }

        AccueilChiffre::create($data);

        return redirect()->route('accueil-chiffres.index')->with('success', 'Homepage statistics created successfully!');
    }

    public function show(AccueilChiffre $accueilChiffre)
    {
        return view('admin.accueil-chiffres.show', compact('accueilChiffre'));
    }

    public function edit(AccueilChiffre $accueilChiffre)
    {
        return view('admin.accueil-chiffres.edit', compact('accueilChiffre'));
    }

    public function update(Request $request, AccueilChiffre $accueilChiffre)
    {
        $validator = Validator::make($request->all(), [
            'coach_ready' => 'required|integer|min:0',
            'icone_coach_ready' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'book_of_the_academy' => 'required|integer|min:0',
            'icone_book_of_the_academy' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'registered_student' => 'required|integer|min:0',
            'icone_registered_student' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'training_program' => 'required|integer|min:0',
            'icone_training_program' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->only([
            'coach_ready',
            'book_of_the_academy',
            'registered_student',
            'training_program',
        ]);
        $data['is_active'] = $request->has('is_active');

        $imageFields = [
            'icone_coach_ready',
            'icone_book_of_the_academy',
            'icone_registered_student',
            'icone_training_program',
        ];

        foreach ($imageFields as $field) {
            if ($request->hasFile($field)) {
                if ($accueilChiffre->$field && file_exists(public_path('uploads/accueil-chiffres/' . $accueilChiffre->$field))) {
                    unlink(public_path('uploads/accueil-chiffres/' . $accueilChiffre->$field));
                }
                $imageName = time() . '_' . Str::random(10) . '_' . $field . '.' . $request->file($field)->extension();
                $request->file($field)->move(public_path('uploads/accueil-chiffres'), $imageName);
                $data[$field] = $imageName;
            }
        }

        $accueilChiffre->update($data);

        return redirect()->route('accueil-chiffres.index')->with('success', 'Homepage statistics updated successfully!');
    }

    public function destroy(AccueilChiffre $accueilChiffre)
    {
        $imageFields = [
            'icone_coach_ready',
            'icone_book_of_the_academy',
            'icone_registered_student',
            'icone_training_program',
        ];

        foreach ($imageFields as $field) {
            if ($accueilChiffre->$field && file_exists(public_path('uploads/accueil-chiffres/' . $accueilChiffre->$field))) {
                unlink(public_path('uploads/accueil-chiffres/' . $accueilChiffre->$field));
            }
        }

        $accueilChiffre->delete();

        return redirect()->route('accueil-chiffres.index')->with('success', 'Homepage statistics deleted successfully!');
    }

    public function toggle(AccueilChiffre $accueilChiffre)
    {
        $accueilChiffre->is_active = !$accueilChiffre->is_active;
        $accueilChiffre->save();

        return back()->with('success', 'Status updated successfully!');
    }
}
