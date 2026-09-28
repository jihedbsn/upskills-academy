<?php

namespace App\Http\Controllers;

use App\Models\TeacherHomePage;
use App\Services\TeacherHomePageService;
use Illuminate\Http\Request;

class TeacherHomePageController extends Controller
{
    public function __construct(private TeacherHomePageService $service) {}

    public function index()
    {
        try {
            $teachers = $this->service->index();
            return view('teacher-home-pages.index', compact('teachers'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading teachers.');
        }
    }

    public function create()
    {
        return view('teacher-home-pages.create');
    }

    public function store(Request $request)
    {
        try {
            return $this->service->store($request);
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error creating teacher: ' . $e->getMessage());
        }
    }

    public function show(TeacherHomePage $teacherHomePage)
    {
        return view('teacher-home-pages.show', compact('teacherHomePage'));
    }

    public function edit(TeacherHomePage $teacherHomePage)
    {
        return view('teacher-home-pages.edit', compact('teacherHomePage'));
    }

    public function update(Request $request, TeacherHomePage $teacherHomePage)
    {
        try {
            return $this->service->update($request, $teacherHomePage);
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error updating teacher: ' . $e->getMessage());
        }
    }

    public function destroy(TeacherHomePage $teacherHomePage)
    {
        try {
            return $this->service->destroy($teacherHomePage);
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting teacher: ' . $e->getMessage());
        }
    }
}
