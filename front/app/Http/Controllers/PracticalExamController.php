<?php

namespace App\Http\Controllers;

use App\Services\PracticalExamService;
use Illuminate\Http\Request;

class PracticalExamController extends Controller
{
    protected PracticalExamService $practicalExamService;

    public function __construct(PracticalExamService $practicalExamService)
    {
        $this->practicalExamService = $practicalExamService;
    }

    /**
     * List all practical exam attempts history for the student
     */
    public function index($locale)
    {
        return $this->practicalExamService->index($locale);
    }

    /**
     * Show the practical exam page for a course
     */
    public function show($locale, $productId)
    {
        return $this->practicalExamService->show($locale, $productId);
    }

    /**
     * Start a new practical exam attempt
     */
    public function start(Request $request, $locale, $productId)
    {
        return $this->practicalExamService->start($request, $locale, $productId);
    }

    /**
     * Submit video URL for online exam
     */
    public function submit(Request $request, $locale, $productId)
    {
        return $this->practicalExamService->submit($request, $locale, $productId);
    }

    /**
     * Mark classroom exam as completed (with file upload)
     */
    public function markCompleted(Request $request, $locale, $productId)
    {
        return $this->practicalExamService->markCompleted($request, $locale, $productId);
    }

    /**
     * Download training case file assigned to this attempt
     */
    public function downloadCaseFiles($locale, $attemptId)
    {
        return $this->practicalExamService->downloadCaseFiles($locale, $attemptId);
    }
}
