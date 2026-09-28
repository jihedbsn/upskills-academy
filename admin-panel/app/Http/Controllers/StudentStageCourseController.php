<?php

namespace App\Http\Controllers;

use App\Models\StudentStageCourse;
use App\Services\StudentStageCourseService;
use Illuminate\Http\Request;

class StudentStageCourseController extends Controller
{
    public function __construct(private StudentStageCourseService $service) {}

    public function index(Request $request)
    {
        $data = $this->service->index($request);
        return view('student-stage-courses.index', $data);
    }

    public function byProduct(Request $request)
    {
        $data = $this->service->byProduct($request);
        return view('student-stage-courses.by-product', $data);
    }

    public function create()
    {
        $data = $this->service->getCreateData();
        return view('student-stage-courses.create', $data);
    }

    public function store(Request $request)
    {
        return $this->service->store($request);
    }

    public function show(StudentStageCourse $studentStageCourse)
    {
        $studentStageCourse = $this->service->show($studentStageCourse);
        return view('student-stage-courses.show', compact('studentStageCourse'));
    }

    public function edit(StudentStageCourse $studentStageCourse)
    {
        $data = $this->service->getEditData($studentStageCourse);
        return view('student-stage-courses.edit', $data);
    }

    public function update(Request $request, StudentStageCourse $studentStageCourse)
    {
        return $this->service->update($request, $studentStageCourse);
    }

    public function destroy(StudentStageCourse $studentStageCourse)
    {
        return $this->service->destroy($studentStageCourse);
    }

    public function downloadFile(StudentStageCourse $studentStageCourse, $fileNumber)
    {
        return $this->service->downloadFile($studentStageCourse, $fileNumber);
    }

    public function validate(Request $request, StudentStageCourse $studentStageCourse)
    {
        return $this->service->validateSubmission($request, $studentStageCourse);
    }

    public function reject(Request $request, StudentStageCourse $studentStageCourse)
    {
        return $this->service->rejectSubmission($request, $studentStageCourse);
    }
}
