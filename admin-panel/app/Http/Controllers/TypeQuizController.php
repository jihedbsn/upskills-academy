<?php

namespace App\Http\Controllers;

use App\Models\TypeQuiz;
use App\Services\TypeQuizService;
use Illuminate\Http\Request;

class TypeQuizController extends Controller
{
    public function __construct(private TypeQuizService $service) {}

    public function index()
    {
        $types = $this->service->index();
        return view('type-quizzes.index', compact('types'));
    }

    public function create()
    {
        return view('type-quizzes.create');
    }

    public function store(Request $request)
    {
        return $this->service->store($request);
    }

    public function show(TypeQuiz $typeQuiz)
    {
        $typeQuiz->load('quizzes');
        return view('type-quizzes.show', compact('typeQuiz'));
    }

    public function edit(TypeQuiz $typeQuiz)
    {
        return view('type-quizzes.edit', compact('typeQuiz'));
    }

    public function update(Request $request, TypeQuiz $typeQuiz)
    {
        return $this->service->update($request, $typeQuiz);
    }

    public function destroy(TypeQuiz $typeQuiz)
    {
        return redirect()->back()->with('info', 'La suppression n\'est pas autorisée.');
    }
}
