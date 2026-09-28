<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Services\QuizService;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function __construct(protected QuizService $service) {}

    public function index(Request $request)
    {
        $data = $this->service->index($request);
        return view('quizzes.index', $data);
    }

    public function create()
    {
        $types = $this->service->getTypes();
        return view('quizzes.create', compact('types'));
    }

    public function store(Request $request)
    {
        return $this->service->store($request);
    }

    public function show(Quiz $quiz)
    {
        $quiz = $this->service->show($quiz);
        return view('quizzes.show', compact('quiz'));
    }

    public function edit(Quiz $quiz)
    {
        $data = $this->service->getEditData($quiz);
        return view('quizzes.edit', $data);
    }

    public function update(Request $request, Quiz $quiz)
    {
        return $this->service->update($request, $quiz);
    }

    public function destroy(Quiz $quiz)
    {
        return redirect()->back()->with('info', 'La suppression n\'est pas autorisée.');
    }

    public function addQuestion(Request $request, Quiz $quiz)
    {
        return $this->service->addQuestion($request, $quiz);
    }

    public function importQuiz(Request $request)
    {
        return $this->service->importQuiz($request);
    }
}
