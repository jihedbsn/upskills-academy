<?php

namespace App\Http\Controllers;

use App\Models\HistoriqueQuiz;
use App\Services\HistoriqueQuizService;
use Illuminate\Http\Request;

class HistoriqueQuizController extends Controller
{
    protected HistoriqueQuizService $historiqueQuizService;

    public function __construct(HistoriqueQuizService $historiqueQuizService)
    {
        $this->historiqueQuizService = $historiqueQuizService;
    }

    public function index(Request $request)
    {
        return $this->historiqueQuizService->index($request);
    }

    public function show(HistoriqueQuiz $historiqueQuiz)
    {
        return $this->historiqueQuizService->show($historiqueQuiz);
    }

    public function byStudent(Request $request, $studentId)
    {
        return $this->historiqueQuizService->byStudent($request, $studentId);
    }

    public function byCourse(Request $request, $courseId)
    {
        return $this->historiqueQuizService->byCourse($request, $courseId);
    }

    public function statistics()
    {
        return $this->historiqueQuizService->statistics();
    }
}
