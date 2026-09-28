<?php

namespace App\Services;

use App\Models\ReponseQuestion;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReponseQuestionService
{
    public function index()
    {
        return ReponseQuestion::with('question.quiz')->paginate(10);
    }

    public function getQuestions()
    {
        return Question::with('quiz')->get();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titre_ar'    => 'required|string|max:255',
            'titre_en'    => 'required|string|max:255',
            'question_id' => 'required|exists:questions,id',
            'is_correcte' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            ReponseQuestion::create($request->all());
            return redirect()->route('reponse-questions.index')
                ->with('success', 'Réponse créée avec succès !');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Erreur lors de la création de la réponse: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(ReponseQuestion $reponseQuestion): ReponseQuestion
    {
        $reponseQuestion->load('question.quiz');
        return $reponseQuestion;
    }

    public function update(Request $request, ReponseQuestion $reponseQuestion)
    {
        $validator = Validator::make($request->all(), [
            'titre_ar'    => 'required|string|max:255',
            'titre_en'    => 'required|string|max:255',
            'question_id' => 'required|exists:questions,id',
            'is_correcte' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $reponseQuestion->update($request->all());
            return redirect()->route('reponse-questions.index')
                ->with('success', 'Réponse mise à jour avec succès !');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Erreur lors de la mise à jour de la réponse: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(ReponseQuestion $reponseQuestion)
    {
        try {
            $reponseQuestion->delete();
            return redirect()->route('reponse-questions.index')
                ->with('success', 'Réponse supprimée avec succès !');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Erreur lors de la suppression de la réponse: ' . $e->getMessage()]);
        }
    }

    public function getByQuestion(int $questionId)
    {
        return ReponseQuestion::where('question_id', $questionId)->get();
    }
}
