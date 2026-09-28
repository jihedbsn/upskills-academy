<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CourseStudentService;

class CourseStudentController extends Controller
{
    protected CourseStudentService $courseStudentService;

    public function __construct(CourseStudentService $courseStudentService)
    {
        $this->courseStudentService = $courseStudentService;
    }

    /**
     * Afficher la liste des cours avec le nombre d'étudiants
     */
    public function index()
    {
        return $this->courseStudentService->index();
    }

    /**
     * Afficher les détails d'un cours avec la liste des étudiants
     */
    public function show($id)
    {
        return $this->courseStudentService->show($id);
    }

    /**
     * Remove a student's enrollment from a course
     */
    public function removeEnrollment($orderId)
    {
        return $this->courseStudentService->removeEnrollment($orderId);
    }

    /**
     * Add a student to a course
     */
    public function addStudent(Request $request, $courseId)
    {
        return $this->courseStudentService->addStudent($request, $courseId);
    }

    /**
     * Get students not enrolled in a course (for AJAX)
     */
    public function getAvailableStudents($courseId)
    {
        return $this->courseStudentService->getAvailableStudents($courseId);
    }

    /**
     * Block or unblock a student for a course
     */
    public function toggleBlockStudent($courseId, $studentId)
    {
        return $this->courseStudentService->toggleBlockStudent($courseId, $studentId);
    }
}
