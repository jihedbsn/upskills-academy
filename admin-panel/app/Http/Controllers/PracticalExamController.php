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

    public function index(Request $request)
    {
        return $this->practicalExamService->index($request);
    }

    public function show($attemptId)
    {
        return $this->practicalExamService->show($attemptId);
    }

    public function grade(Request $request, $attemptId)
    {
        return $this->practicalExamService->grade($request, $attemptId);
    }

    public function getStats()
    {
        return $this->practicalExamService->getStats();
    }
}
