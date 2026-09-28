<?php

namespace App\Http\Controllers;

use App\Models\ResultatQuiz;
use App\Services\ResultatQuizService;
use Illuminate\Http\Request;

class ResultatQuizController extends Controller
{
    public function __construct(private ResultatQuizService $service) {}

    public function index(Request $request)
    {
        $data = $this->service->index($request);
        return view('resultat-quizzes.index', $data);
    }

    public function create()
    {
        $data = $this->service->getCreateData();
        return view('resultat-quizzes.create', $data);
    }

    public function store(Request $request)
    {
        return $this->service->store($request);
    }

    public function show(ResultatQuiz $resultatQuiz)
    {
        $data = $this->service->show($resultatQuiz);
        return view('resultat-quizzes.show', $data);
    }

    public function edit(ResultatQuiz $resultatQuiz)
    {
        $data = $this->service->getEditData($resultatQuiz);
        return view('resultat-quizzes.edit', $data);
    }

    public function update(Request $request, ResultatQuiz $resultatQuiz)
    {
        return $this->service->update($request, $resultatQuiz);
    }

    public function destroy(ResultatQuiz $resultatQuiz)
    {
        return $this->service->destroy($resultatQuiz);
    }

    public function adminUpdateSuccess(Request $request, ResultatQuiz $resultatQuiz)
    {
        return $this->service->adminUpdateSuccess($request, $resultatQuiz);
    }

    public function resetAttempts(ResultatQuiz $resultatQuiz)
    {
        return $this->service->resetAttempts($resultatQuiz);
    }

    public function markAsSuccess(ResultatQuiz $resultatQuiz)
    {
        return $this->service->markAsSuccess($resultatQuiz);
    }
}
