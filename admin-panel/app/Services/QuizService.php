<?php

namespace App\Services;

use App\Models\Quiz;
use App\Models\TypeQuiz;
use App\Models\Question;
use App\Models\ReponseQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class QuizService
{
    public function index(Request $request): array
    {
        $query = Quiz::with('type');

        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name_ar', 'like', '%' . $searchTerm . '%')
                  ->orWhere('name_en', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('type', function ($subQuery) use ($searchTerm) {
                      $subQuery->where('titre', 'like', '%' . $searchTerm . '%');
                  });
            });
        }

        if ($request->has('type_id') && !empty($request->type_id)) {
            $query->where('type_id', $request->type_id);
        }

        if ($request->has('score_min') && !empty($request->score_min)) {
            $query->where('score', '>=', $request->score_min);
        }

        if ($request->has('score_max') && !empty($request->score_max)) {
            $query->where('score', '<=', $request->score_max);
        }

        return [
            'quizzes' => $query->orderBy('created_at', 'desc')->paginate(10),
            'types'   => TypeQuiz::all(),
        ];
    }

    public function getTypes()
    {
        return TypeQuiz::all();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_ar'   => 'required|string|max:255',
            'name_en'   => 'required|string|max:255',
            'score'     => 'required|integer|min:0|max:100',
            'type_id'   => 'required|exists:type_quizzes,id',
            'max_attempts' => 'nullable|integer|min:1',
            'questions' => 'nullable|array',
            'questions.*.name_ar' => 'required|string|max:255',
            'questions.*.name_en' => 'required|string|max:255',
            'questions.*.point'   => 'nullable|integer|min:1|max:100',
            'questions.*.reponses' => 'nullable|array',
            'questions.*.reponses.*.titre_ar'   => 'required|string|max:255',
            'questions.*.reponses.*.titre_en'   => 'required|string|max:255',
            'questions.*.reponses.*.is_correcte' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $quiz = Quiz::create($request->only(['name_ar', 'name_en', 'score', 'type_id', 'max_attempts']));

            if ($request->has('questions') && is_array($request->questions)) {
                foreach ($request->questions as $questionData) {
                    $question = Question::create([
                        'name_ar' => $questionData['name_ar'],
                        'name_en' => $questionData['name_en'],
                        'point'   => $questionData['point'] ?? 10,
                        'quiz_id' => $quiz->id,
                    ]);

                    if (isset($questionData['reponses']) && is_array($questionData['reponses'])) {
                        foreach ($questionData['reponses'] as $reponseData) {
                            ReponseQuestion::create([
                                'titre_ar'    => $reponseData['titre_ar'],
                                'titre_en'    => $reponseData['titre_en'],
                                'is_correcte' => $reponseData['is_correcte'] ?? false,
                                'question_id' => $question->id,
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('quizzes.index')->with('success', 'Quiz créé avec succès !');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withErrors(['error' => 'Erreur lors de la création du quiz: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(Quiz $quiz): Quiz
    {
        $quiz->load(['type', 'questions.reponses']);
        return $quiz;
    }

    public function getEditData(Quiz $quiz): array
    {
        $quiz->load(['questions.reponses']);
        return [
            'quiz'  => $quiz,
            'types' => TypeQuiz::all(),
        ];
    }

    public function update(Request $request, Quiz $quiz)
    {
        $validator = Validator::make($request->all(), [
            'name_ar'   => 'required|string|max:255',
            'name_en'   => 'required|string|max:255',
            'score'     => 'required|integer|min:0|max:100',
            'type_id'   => 'required|exists:type_quizzes,id',
            'max_attempts' => 'nullable|integer|min:1',
            'questions' => 'nullable|array',
            'questions.*.name_ar' => 'required|string|max:255',
            'questions.*.name_en' => 'required|string|max:255',
            'questions.*.point'   => 'nullable|integer|min:1|max:100',
            'questions.*.reponses' => 'nullable|array',
            'questions.*.reponses.*.titre_ar'   => 'required|string|max:255',
            'questions.*.reponses.*.titre_en'   => 'required|string|max:255',
            'questions.*.reponses.*.is_correcte' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $quiz->update($request->only(['name_ar', 'name_en', 'score', 'type_id', 'max_attempts']));

            foreach ($quiz->questions as $question) {
                $question->reponses()->delete();
            }
            $quiz->questions()->delete();

            if ($request->has('questions') && is_array($request->questions)) {
                foreach ($request->questions as $questionData) {
                    $question = Question::create([
                        'name_ar' => $questionData['name_ar'],
                        'name_en' => $questionData['name_en'],
                        'point'   => $questionData['point'] ?? 10,
                        'quiz_id' => $quiz->id,
                    ]);

                    if (isset($questionData['reponses']) && is_array($questionData['reponses'])) {
                        foreach ($questionData['reponses'] as $reponseData) {
                            ReponseQuestion::create([
                                'titre_ar'    => $reponseData['titre_ar'],
                                'titre_en'    => $reponseData['titre_en'],
                                'is_correcte' => $reponseData['is_correcte'] ?? false,
                                'question_id' => $question->id,
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('quizzes.index')->with('success', 'Quiz mis à jour avec succès !');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withErrors(['error' => 'Erreur lors de la mise à jour du quiz: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function addQuestion(Request $request, Quiz $quiz)
    {
        $validator = Validator::make($request->all(), [
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'point'   => 'nullable|integer|min:1|max:100',
            'reponses' => 'nullable|array',
            'reponses.*.titre_ar'   => 'required|string|max:255',
            'reponses.*.titre_en'   => 'required|string|max:255',
            'reponses.*.is_correcte' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $question = Question::create([
                'name_ar' => $request->name_ar,
                'name_en' => $request->name_en,
                'point'   => $request->point ?? 10,
                'quiz_id' => $quiz->id,
            ]);

            if ($request->has('reponses') && is_array($request->reponses)) {
                foreach ($request->reponses as $reponseData) {
                    ReponseQuestion::create([
                        'titre_ar'    => $reponseData['titre_ar'],
                        'titre_en'    => $reponseData['titre_en'],
                        'is_correcte' => $reponseData['is_correcte'] ?? false,
                        'question_id' => $question->id,
                    ]);
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Question ajoutée avec succès !');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withErrors(['error' => 'Erreur lors de l\'ajout de la question: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function importQuiz(Request $request)
    {
        try {
            $response = Http::get('https://upskillsacademy.com/api/test');

            if (!$response->successful()) {
                return redirect()->back()
                    ->withErrors(['error' => 'Erreur lors de la récupération des données depuis l\'API externe: HTTP ' . $response->status()]);
            }

            $externalData = $response->json();

            if (!is_array($externalData)) {
                return redirect()->back()
                    ->withErrors(['error' => 'Format de données invalide reçu de l\'API externe']);
            }

            DB::beginTransaction();

            $importedCount = 0;
            $skippedCount = 0;
            $totalQuestions = 0;
            $totalAnswers = 0;
            $importedQuizzes = [];

            foreach ($externalData as $quizData) {
                $existingQuiz = Quiz::where('name_ar', $quizData['name_ar'])
                    ->where('name_en', $quizData['name_en'])
                    ->first();

                if ($existingQuiz) {
                    $skippedCount++;
                    continue;
                }

                $quiz = Quiz::create([
                    'name_ar' => $quizData['name_ar'],
                    'name_en' => $quizData['name_en'],
                    'score'   => 100,
                    'type_id' => ($quizData['is_exam'] ?? 0) ? 1 : 2,
                ]);

                $questionsCount = 0;
                $answersCount = 0;

                if (isset($quizData['questions']) && is_array($quizData['questions'])) {
                    foreach ($quizData['questions'] as $questionData) {
                        $question = Question::create([
                            'name_ar' => $questionData['name_ar'],
                            'name_en' => $questionData['name_en'],
                            'point'   => 10,
                            'quiz_id' => $quiz->id,
                        ]);

                        $questionsCount++;

                        if (isset($questionData['answers']) && is_array($questionData['answers'])) {
                            foreach ($questionData['answers'] as $answerData) {
                                ReponseQuestion::create([
                                    'titre_ar'    => $answerData['name_ar'],
                                    'titre_en'    => $answerData['name_en'],
                                    'is_correcte' => (bool) $answerData['is_correct'],
                                    'question_id' => $question->id,
                                ]);
                                $answersCount++;
                            }
                        }
                    }
                }

                $importedCount++;
                $totalQuestions += $questionsCount;
                $totalAnswers += $answersCount;
                $importedQuizzes[] = [
                    'id'        => $quiz->id,
                    'name_ar'   => $quiz->name_ar,
                    'name_en'   => $quiz->name_en,
                    'questions' => $questionsCount,
                    'answers'   => $answersCount,
                ];
            }

            DB::commit();

            $message = "Importation réussie ! ";
            $message .= "Quiz importés: {$importedCount}, ";
            $message .= "Quiz ignorés: {$skippedCount}, ";
            $message .= "Questions créées: {$totalQuestions}, ";
            $message .= "Réponses créées: {$totalAnswers}";
            echo "je susi ici";exit();
           /* return redirect()->route('quizzes.index')
                ->with('success', $message)
                ->with('imported_quizzes', $importedQuizzes);*/

        } catch (\Exception $e) {
            DB::rollBack();
            echo $e->getMessage();exit();
            return redirect()->back()
                ->withErrors(['error' => 'Erreur lors de l\'importation: ' . $e->getMessage()]);
        }
    }
}
