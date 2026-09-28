<?php

namespace App\Services;

use App\Models\TeacherHomePage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TeacherHomePageService
{
    public function index(): \Illuminate\Pagination\LengthAwarePaginator
    {
        return TeacherHomePage::ordered()->paginate(10);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_ar'   => 'required|string|max:255',
            'name_en'   => 'required|string|max:255',
            'image'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'order'     => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            $image      = $request->file('image');
            $imageName  = time() . '_' . Str::slug($validated['name_en']) . '.' . $image->getClientOriginalExtension();
            $uploadPath = public_path('uploads/teachers');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            $image->move($uploadPath, $imageName);
            $validated['image'] = $imageName;
        }

        $validated['order']     = $validated['order'] ?? 0;
        $validated['is_active'] = $request->has('is_active');

        TeacherHomePage::create($validated);

        return redirect()->route('teacher-home-pages.index')->with('success', 'Teacher added to homepage successfully!');
    }

    public function update(Request $request, TeacherHomePage $teacherHomePage)
    {
        $validated = $request->validate([
            'name_ar'   => 'required|string|max:255',
            'name_en'   => 'required|string|max:255',
            'image'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'order'     => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            $teacherHomePage->deleteImage();

            $image      = $request->file('image');
            $imageName  = time() . '_' . Str::slug($validated['name_en']) . '.' . $image->getClientOriginalExtension();
            $uploadPath = public_path('uploads/teachers');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            $image->move($uploadPath, $imageName);
            $validated['image'] = $imageName;
        }

        $validated['order']     = $validated['order'] ?? 0;
        $validated['is_active'] = $request->has('is_active');

        $teacherHomePage->update($validated);

        return redirect()->route('teacher-home-pages.index')->with('success', 'Teacher updated successfully!');
    }

    public function destroy(TeacherHomePage $teacherHomePage)
    {
        $teacherHomePage->delete();
        return redirect()->route('teacher-home-pages.index')->with('success', 'Teacher removed from homepage successfully!');
    }
}
