<?php

namespace App\Services;

use App\Models\TypeQuiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TypeQuizService
{
    public function index(): \Illuminate\Pagination\LengthAwarePaginator
    {
        return TypeQuiz::withCount('quizzes')->orderBy('created_at', 'desc')->paginate(10);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titre' => 'required|string|max:255|unique:type_quizzes,titre',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            TypeQuiz::create($request->all());
            return redirect()->route('type-quizzes.index')->with('success', 'Type de quiz créé avec succès !');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Erreur lors de la création du type de quiz: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function update(Request $request, TypeQuiz $typeQuiz)
    {
        $validator = Validator::make($request->all(), [
            'titre' => 'required|string|max:255|unique:type_quizzes,titre,' . $typeQuiz->id,
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $typeQuiz->update($request->all());
            return redirect()->route('type-quizzes.index')->with('success', 'Type de quiz mis à jour avec succès !');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Erreur lors de la mise à jour du type de quiz: ' . $e->getMessage()])
                ->withInput();
        }
    }
}
