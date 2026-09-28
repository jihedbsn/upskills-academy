<?php

namespace App\Services;

use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TeacherService
{
    public function index(): \Illuminate\Pagination\LengthAwarePaginator
    {
        return Teacher::orderBy('nom')->paginate(10);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom'       => 'required|string|max:255',
            'prenom'    => 'required|string|max:255',
            'nom_en'    => 'nullable|string|max:255',
            'prenom_en' => 'nullable|string|max:255',
            'email'     => 'required|email|unique:teachers,email',
            'password'  => 'required|string|min:6',
            'image'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data             = $request->all();
        $data['password'] = Hash::make($request->password);

        if ($request->hasFile('image')) {
            $uploadsDir = public_path('uploads');
            if (!is_dir($uploadsDir)) {
                @mkdir($uploadsDir, 0755, true);
            }

            $file             = $request->file('image');
            $filename         = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadsDir, $filename);
            $data['image']    = 'uploads/' . $filename;
        }

        Teacher::create($data);

        return redirect()->route('teachers.index')->with('success', 'Enseignant créé avec succès !');
    }

    public function update(Request $request, Teacher $teacher)
    {
        $validator = Validator::make($request->all(), [
            'nom'       => 'required|string|max:255',
            'prenom'    => 'required|string|max:255',
            'nom_en'    => 'nullable|string|max:255',
            'prenom_en' => 'nullable|string|max:255',
            'email'     => 'required|email|unique:teachers,email,' . $teacher->id,
            'password'  => 'nullable|string|min:6',
            'image'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->except('password');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('image')) {
            if (!empty($teacher->image)) {
                $oldPath = public_path($teacher->image);
                if (Str::startsWith($teacher->image, 'uploads/') && file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $uploadsDir = public_path('uploads');
            if (!is_dir($uploadsDir)) {
                @mkdir($uploadsDir, 0755, true);
            }

            $file          = $request->file('image');
            $filename      = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadsDir, $filename);
            $data['image'] = 'uploads/' . $filename;
        }

        $teacher->update($data);

        return redirect()->route('teachers.index')->with('success', 'Enseignant mis à jour avec succès !');
    }

    public function destroy(Teacher $teacher)
    {
        if (!empty($teacher->image)) {
            $oldPath = public_path($teacher->image);
            if (Str::startsWith($teacher->image, 'uploads/') && file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }

        $teacher->delete();

        return redirect()->route('teachers.index')->with('success', 'Enseignant supprimé avec succès !');
    }
}
