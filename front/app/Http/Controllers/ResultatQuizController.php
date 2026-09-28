<?php

namespace App\Http\Controllers;

use App\Models\ResultatQuiz;
use App\Services\ResultatQuizService;
use Illuminate\Http\Request;

class ResultatQuizController extends Controller
{
    protected ResultatQuizService $resultatQuizService;

    public function __construct(ResultatQuizService $resultatQuizService)
    {
        $this->resultatQuizService = $resultatQuizService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->resultatQuizService->index($request);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return $this->resultatQuizService->create();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->resultatQuizService->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(ResultatQuiz $resultatQuiz)
    {
        return $this->resultatQuizService->show($resultatQuiz);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ResultatQuiz $resultatQuiz)
    {
        return $this->resultatQuizService->edit($resultatQuiz);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ResultatQuiz $resultatQuiz)
    {
        return $this->resultatQuizService->update($request, $resultatQuiz);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ResultatQuiz $resultatQuiz)
    {
        return $this->resultatQuizService->destroy($resultatQuiz);
    }

    /**
     * Méthodes pour le front-end (étudiants)
     */
    public function startQuiz(Request $request)
    {
        return $this->resultatQuizService->startQuiz($request);
    }

    public function submitQuiz(Request $request)
    {
        return $this->resultatQuizService->submitQuiz($request);
    }

    public function showResult(ResultatQuiz $resultatQuiz)
    {
        return $this->resultatQuizService->showResult($resultatQuiz);
    }

    /**
     * Méthodes pour l'admin
     */
    public function adminUpdateSuccess(Request $request, ResultatQuiz $resultatQuiz)
    {
        return $this->resultatQuizService->adminUpdateSuccess($request, $resultatQuiz);
    }

    public function resetAttempts(ResultatQuiz $resultatQuiz)
    {
        return $this->resultatQuizService->resetAttempts($resultatQuiz);
    }
}
