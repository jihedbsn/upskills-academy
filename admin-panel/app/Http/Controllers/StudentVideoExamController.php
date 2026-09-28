<?php

namespace App\Http\Controllers;

use App\Models\StudentVideoExam;
use App\Services\StudentVideoExamService;
use Illuminate\Http\Request;

class StudentVideoExamController extends Controller
{
    public function __construct(private StudentVideoExamService $service) {}

    public function index(Request $request)
    {
        $data = $this->service->index($request);
        return view('student-video-exams.index', $data);
    }

    public function byProduct(Request $request)
    {
        $data = $this->service->byProduct($request);
        return view('student-video-exams.by-product', $data);
    }

    public function create()
    {
        $data = $this->service->getCreateData();
        return view('student-video-exams.create', $data);
    }

    public function store(Request $request)
    {
        return $this->service->store($request);
    }

    public function show(StudentVideoExam $studentVideoExam)
    {
        $studentVideoExam = $this->service->show($studentVideoExam);
        return view('student-video-exams.show', compact('studentVideoExam'));
    }

    public function edit(StudentVideoExam $studentVideoExam)
    {
        $data = $this->service->getEditData($studentVideoExam);
        return view('student-video-exams.edit', $data);
    }

    public function update(Request $request, StudentVideoExam $studentVideoExam)
    {
        return $this->service->update($request, $studentVideoExam);
    }

    public function destroy(StudentVideoExam $studentVideoExam)
    {
        return $this->service->destroy($studentVideoExam);
    }

    public function approve(Request $request, StudentVideoExam $studentVideoExam)
    {
        return $this->service->approve($request, $studentVideoExam);
    }

    public function reject(Request $request, StudentVideoExam $studentVideoExam)
    {
        return $this->service->reject($request, $studentVideoExam);
    }

    public function openVideo(StudentVideoExam $studentVideoExam)
    {
        return redirect($studentVideoExam->lien);
    }
}
