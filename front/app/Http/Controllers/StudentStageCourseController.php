<?php

namespace App\Http\Controllers;

use App\Models\StudentStageCourse;
use App\Services\StudentStageCourseService;
use Illuminate\Http\Request;

class StudentStageCourseController extends Controller
{
    protected StudentStageCourseService $studentStageCourseService;

    public function __construct(StudentStageCourseService $studentStageCourseService)
    {
        $this->studentStageCourseService = $studentStageCourseService;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->studentStageCourseService->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(StudentStageCourse $studentStageCourse)
    {
        return $this->studentStageCourseService->show($studentStageCourse);
    }

    /**
     * Update the validation status (admin only).
     */
    public function updateValidation(Request $request, StudentStageCourse $studentStageCourse)
    {
        return $this->studentStageCourseService->updateValidation($request, $studentStageCourse);
    }

    /**
     * Get submissions for a specific product (admin only).
     */
    public function getSubmissionsForProduct($productId)
    {
        return $this->studentStageCourseService->getSubmissionsForProduct($productId);
    }

    /**
     * Get student's submission for a specific product.
     */
    public function getStudentSubmission($productId)
    {
        return $this->studentStageCourseService->getStudentSubmission($productId);
    }

    /**
     * Download stage submission file
     */
    public function downloadFile(StudentStageCourse $studentStageCourse, $fileNumber)
    {
        return $this->studentStageCourseService->downloadFile($studentStageCourse, $fileNumber);
    }
}
