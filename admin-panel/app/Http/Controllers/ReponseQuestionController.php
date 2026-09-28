<?php

namespace App\Http\Controllers;

use App\Models\ReponseQuestion;
use App\Services\ReponseQuestionService;
use Illuminate\Http\Request;

class ReponseQuestionController extends Controller
{
    public function __construct(protected ReponseQuestionService $service) {}

    public function index()
    {
        $reponses = $this->service->index();
        return view('reponse-questions.index', compact('reponses'));
    }

    public function create()
    {
        $questions = $this->service->getQuestions();
        return view('reponse-questions.create', compact('questions'));
    }

    public function store(Request $request)
    {
        return $this->service->store($request);
    }

    public function show(ReponseQuestion $reponseQuestion)
    {
        $reponseQuestion = $this->service->show($reponseQuestion);
        return view('reponse-questions.show', compact('reponseQuestion'));
    }

    public function edit(ReponseQuestion $reponseQuestion)
    {
        $questions = $this->service->getQuestions();
        return view('reponse-questions.edit', compact('reponseQuestion', 'questions'));
    }

    public function update(Request $request, ReponseQuestion $reponseQuestion)
    {
        return $this->service->update($request, $reponseQuestion);
    }

    public function destroy(ReponseQuestion $reponseQuestion)
    {
        return $this->service->destroy($reponseQuestion);
    }

    public function getByQuestion($questionId)
    {
        return response()->json($this->service->getByQuestion((int) $questionId));
    }
}
