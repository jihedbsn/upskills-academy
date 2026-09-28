<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\ReponseQuestion;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class QuizApiController extends Controller
{
    /**
     * Créer un quiz complet avec ses questions et réponses
     * Format compatible avec l'API existante
     */
    public function storeQuiz(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'quiz_id' => 'sometimes|integer',
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'is_exam' => 'sometimes|boolean',
            'score' => 'sometimes|integer|min:0',
            'type_id' => 'sometimes|integer|exists:type_quizzes,id',
            'questions' => 'required|array|min:1',
            'questions.*.question_id' => 'sometimes|integer',
            'questions.*.name_ar' => 'required|string|max:255',
            'questions.*.name_en' => 'required|string|max:255',
            'questions.*.point' => 'sometimes|integer|min:0',
            'questions.*.answers' => 'required|array|min:2',
            'questions.*.answers.*.answer_id' => 'sometimes|integer',
            'questions.*.answers.*.name_ar' => 'required|string|max:255',
            'questions.*.answers.*.name_en' => 'required|string|max:255',
            'questions.*.answers.*.is_correct' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Créer le quiz
            $quizData = [
                'name_ar' => $request->input('name_ar'),
                'name_en' => $request->input('name_en'),
                'score' => $request->input('score', 100),
                'type_id' => $request->input('type_id', 1)
            ];

            $quiz = Quiz::create($quizData);

            // Créer les questions et leurs réponses
            foreach ($request->input('questions') as $questionData) {
                $question = Question::create([
                    'name_ar' => $questionData['name_ar'],
                    'name_en' => $questionData['name_en'],
                    'point' => $questionData['point'] ?? 10,
                    'quiz_id' => $quiz->id
                ]);

                // Créer les réponses pour cette question
                foreach ($questionData['answers'] as $answerData) {
                    ReponseQuestion::create([
                        'titre_ar' => $answerData['name_ar'],
                        'titre_en' => $answerData['name_en'],
                        'is_correcte' => $answerData['is_correct'],
                        'question_id' => $question->id
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Quiz créé avec succès',
                'data' => [
                    'quiz_id' => $quiz->id,
                    'quiz' => $quiz->load(['questions.reponses'])
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du quiz',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Insérer plusieurs quiz en une seule fois (format tableau)
     * Compatible avec le format de l'API existante
     */
    public function storeMultipleQuizzes(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            '*' => 'required|array',
            '*.*.quiz_id' => 'sometimes|integer',
            '*.*.name_ar' => 'required|string|max:255',
            '*.*.name_en' => 'required|string|max:255',
            '*.*.is_exam' => 'sometimes|boolean',
            '*.*.score' => 'sometimes|integer|min:0',
            '*.*.type_id' => 'sometimes|integer|exists:type_quizzes,id',
            '*.*.questions' => 'required|array|min:1',
            '*.*.questions.*.question_id' => 'sometimes|integer',
            '*.*.questions.*.name_ar' => 'required|string|max:255',
            '*.*.questions.*.name_en' => 'required|string|max:255',
            '*.*.questions.*.point' => 'sometimes|integer|min:0',
            '*.*.questions.*.answers' => 'required|array|min:2',
            '*.*.questions.*.answers.*.answer_id' => 'sometimes|integer',
            '*.*.questions.*.answers.*.name_ar' => 'required|string|max:255',
            '*.*.questions.*.answers.*.name_en' => 'required|string|max:255',
            '*.*.questions.*.answers.*.is_correct' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $createdQuizzes = [];

            foreach ($request->all() as $quizData) {
                // Créer le quiz
                $quiz = Quiz::create([
                    'name_ar' => $quizData['name_ar'],
                    'name_en' => $quizData['name_en'],
                    'score' => $quizData['score'] ?? 100,
                    'type_id' => $quizData['type_id'] ?? 1
                ]);

                // Créer les questions et leurs réponses
                foreach ($quizData['questions'] as $questionData) {
                    $question = Question::create([
                        'name_ar' => $questionData['name_ar'],
                        'name_en' => $questionData['name_en'],
                        'point' => $questionData['point'] ?? 10,
                        'quiz_id' => $quiz->id
                    ]);

                    // Créer les réponses pour cette question
                    foreach ($questionData['answers'] as $answerData) {
                        ReponseQuestion::create([
                            'titre_ar' => $answerData['name_ar'],
                            'titre_en' => $answerData['name_en'],
                            'is_correcte' => $answerData['is_correct'],
                            'question_id' => $question->id
                        ]);
                    }
                }

                $createdQuizzes[] = [
                    'quiz_id' => $quiz->id,
                    'name_ar' => $quiz->name_ar,
                    'name_en' => $quiz->name_en
                ];
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($createdQuizzes) . ' quiz créés avec succès',
                'data' => $createdQuizzes
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création des quiz',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Créer une question avec ses réponses pour un quiz existant
     */
    public function storeQuestion(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'question' => 'required|array',
            'question.name_ar' => 'required|string|max:255',
            'question.name_en' => 'required|string|max:255',
            'question.point' => 'integer|min:0',
            'question.quiz_id' => 'required|integer|exists:quizzes,id',
            'reponses' => 'required|array|min:2',
            'reponses.*.titre_ar' => 'required|string|max:255',
            'reponses.*.titre_en' => 'required|string|max:255',
            'reponses.*.is_correcte' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Créer la question
            $question = Question::create($request->input('question'));

            // Créer les réponses
            foreach ($request->input('reponses') as $reponseData) {
                $reponseData['question_id'] = $question->id;
                ReponseQuestion::create($reponseData);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Question créée avec succès',
                'data' => [
                    'question_id' => $question->id,
                    'question' => $question->load('reponses')
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la question',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Créer une réponse pour une question existante
     */
    public function storeReponse(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'titre_ar' => 'required|string|max:255',
            'titre_en' => 'required|string|max:255',
            'question_id' => 'required|integer|exists:questions,id',
            'is_correcte' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $reponse = ReponseQuestion::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Réponse créée avec succès',
                'data' => $reponse
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la réponse',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer tous les quiz avec leurs questions et réponses
     */
    public function getQuizzes(): JsonResponse
    {
        try {
            $quizzes = Quiz::with(['questions.reponses', 'type'])->get();

            return response()->json([
                'success' => true,
                'data' => $quizzes
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des quiz',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer un quiz spécifique avec ses questions et réponses
     */
    public function getQuiz($id): JsonResponse
    {
        try {
            $quiz = Quiz::with(['questions.reponses', 'type'])->find($id);

            if (!$quiz) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quiz non trouvé'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $quiz
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération du quiz',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mettre à jour un quiz
     */
    public function updateQuiz(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name_ar' => 'sometimes|string|max:255',
            'name_en' => 'sometimes|string|max:255',
            'score' => 'sometimes|integer|min:0',
            'type_id' => 'sometimes|integer|exists:type_quizzes,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $quiz = Quiz::find($id);

            if (!$quiz) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quiz non trouvé'
                ], 404);
            }

            $quiz->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Quiz mis à jour avec succès',
                'data' => $quiz
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du quiz',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer un quiz et toutes ses questions/réponses
     */
    public function deleteQuiz($id): JsonResponse
    {
        try {
            $quiz = Quiz::find($id);

            if (!$quiz) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quiz non trouvé'
                ], 404);
            }

            DB::beginTransaction();

            // Supprimer toutes les réponses des questions du quiz
            foreach ($quiz->questions as $question) {
                $question->reponses()->delete();
            }

            // Supprimer toutes les questions du quiz
            $quiz->questions()->delete();

            // Supprimer le quiz
            $quiz->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Quiz supprimé avec succès'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du quiz',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
