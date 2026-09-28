<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Quiz;
use App\Services\QuizService;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    protected QuizService $quizService;

    public function __construct(QuizService $quizService)
    {
        $this->quizService = $quizService;
    }

    public function start(Request $request, $locale, Product $product, Quiz $quiz)
    {
        return $this->quizService->start($request, $locale, $product, $quiz);
    }

    public function submit(Request $request, $locale, Product $product, Quiz $quiz)
    {
        return $this->quizService->submit($request, $locale, $product, $quiz);
    }

    public function result($locale, Product $product, Quiz $quiz)
    {
        return $this->quizService->result($locale, $product, $quiz);
    }
}
