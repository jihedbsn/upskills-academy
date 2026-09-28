<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\ReponseQuestion;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class QuizImportController extends Controller
{
    /**
     * Importer tous les quiz depuis l'API externe
     */
    public function importFromExternalApi(): JsonResponse
    {
        try {
            // Récupérer les données depuis l'API externe
            $response = Http::timeout(30)->get('https://upskillsacademy.com/api/test');

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la récupération des données depuis l\'API externe',
                    'error' => 'HTTP ' . $response->status()
                ], 500);
            }

            $externalData = $response->json();

            if (!is_array($externalData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format de données invalide reçu de l\'API externe'
                ], 422);
            }

            DB::beginTransaction();

            $importedQuizzes = [];
            $totalQuestions = 0;
            $totalAnswers = 0;

            foreach ($externalData as $quizData) {
                // Vérifier si le quiz existe déjà
                $existingQuiz = Quiz::where('name_ar', $quizData['name_ar'])
                    ->where('name_en', $quizData['name_en'])
                    ->first();

                if ($existingQuiz) {
                    Log::info("Quiz déjà existant ignoré: {$quizData['name_ar']}");
                    continue;
                }

                // Créer le quiz
                $quiz = Quiz::create([
                    'name_ar' => $quizData['name_ar'],
                    'name_en' => $quizData['name_en'],
                    'score' => 100, // Valeur par défaut
                    'type_id' => 1  // Valeur par défaut
                ]);

                $questionsCount = 0;
                $answersCount = 0;

                // Créer les questions et réponses
                if (isset($quizData['questions']) && is_array($quizData['questions'])) {
                    foreach ($quizData['questions'] as $questionData) {
                        $question = Question::create([
                            'name_ar' => $questionData['name_ar'],
                            'name_en' => $questionData['name_en'],
                            'point' => 10, // Valeur par défaut
                            'quiz_id' => $quiz->id
                        ]);

                        $questionsCount++;

                        // Créer les réponses
                        if (isset($questionData['answers']) && is_array($questionData['answers'])) {
                            foreach ($questionData['answers'] as $answerData) {
                                ReponseQuestion::create([
                                    'titre_ar' => $answerData['name_ar'],
                                    'titre_en' => $answerData['name_en'],
                                    'is_correcte' => (bool) $answerData['is_correct'],
                                    'question_id' => $question->id
                                ]);
                                $answersCount++;
                            }
                        }
                    }
                }

                $importedQuizzes[] = [
                    'quiz_id' => $quiz->id,
                    'name_ar' => $quiz->name_ar,
                    'name_en' => $quiz->name_en,
                    'questions_count' => $questionsCount,
                    'answers_count' => $answersCount
                ];

                $totalQuestions += $questionsCount;
                $totalAnswers += $answersCount;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Importation réussie',
                'data' => [
                    'imported_quizzes' => count($importedQuizzes),
                    'total_questions' => $totalQuestions,
                    'total_answers' => $totalAnswers,
                    'quizzes' => $importedQuizzes
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erreur lors de l\'importation des quiz: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'importation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Importer un quiz spécifique par son ID depuis l'API externe
     */
    public function importSpecificQuiz(Request $request): JsonResponse
    {
        $validator = \Validator::make($request->all(), [
            'quiz_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Récupérer les données depuis l'API externe
            $response = Http::timeout(30)->get('https://upskillsacademy.com/api/test');

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la récupération des données depuis l\'API externe',
                    'error' => 'HTTP ' . $response->status()
                ], 500);
            }

            $externalData = $response->json();
            $targetQuizId = $request->input('quiz_id');

            // Trouver le quiz spécifique
            $quizData = null;
            foreach ($externalData as $quiz) {
                if ($quiz['quiz_id'] == $targetQuizId) {
                    $quizData = $quiz;
                    break;
                }
            }

            if (!$quizData) {
                return response()->json([
                    'success' => false,
                    'message' => "Quiz avec l'ID {$targetQuizId} non trouvé dans l'API externe"
                ], 404);
            }

            // Vérifier si le quiz existe déjà
            $existingQuiz = Quiz::where('name_ar', $quizData['name_ar'])
                ->where('name_en', $quizData['name_en'])
                ->first();

            if ($existingQuiz) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce quiz existe déjà dans la base de données',
                    'data' => [
                        'existing_quiz_id' => $existingQuiz->id,
                        'name_ar' => $existingQuiz->name_ar,
                        'name_en' => $existingQuiz->name_en
                    ]
                ], 409);
            }

            DB::beginTransaction();

            // Créer le quiz
            $quiz = Quiz::create([
                'name_ar' => $quizData['name_ar'],
                'name_en' => $quizData['name_en'],
                'score' => 100,
                'type_id' => 1
            ]);

            $questionsCount = 0;
            $answersCount = 0;

            // Créer les questions et réponses
            if (isset($quizData['questions']) && is_array($quizData['questions'])) {
                foreach ($quizData['questions'] as $questionData) {
                    $question = Question::create([
                        'name_ar' => $questionData['name_ar'],
                        'name_en' => $questionData['name_en'],
                        'point' => 10,
                        'quiz_id' => $quiz->id
                    ]);

                    $questionsCount++;

                    // Créer les réponses
                    if (isset($questionData['answers']) && is_array($questionData['answers'])) {
                        foreach ($questionData['answers'] as $answerData) {
                            ReponseQuestion::create([
                                'titre_ar' => $answerData['name_ar'],
                                'titre_en' => $answerData['name_en'],
                                'is_correcte' => (bool) $answerData['is_correct'],
                                'question_id' => $question->id
                            ]);
                            $answersCount++;
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Quiz importé avec succès',
                'data' => [
                    'quiz_id' => $quiz->id,
                    'name_ar' => $quiz->name_ar,
                    'name_en' => $quiz->name_en,
                    'questions_count' => $questionsCount,
                    'answers_count' => $answersCount,
                    'quiz' => $quiz->load(['questions.reponses'])
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erreur lors de l\'importation du quiz: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'importation du quiz',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vérifier les quiz disponibles dans l'API externe
     */
    public function checkExternalQuizzes(): JsonResponse
    {
        try {
            $response = Http::timeout(30)->get('https://upskillsacademy.com/api/test');

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la récupération des données depuis l\'API externe',
                    'error' => 'HTTP ' . $response->status()
                ], 500);
            }

            $externalData = $response->json();

            if (!is_array($externalData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format de données invalide reçu de l\'API externe'
                ], 422);
            }

            $availableQuizzes = [];
            $alreadyImported = [];

            foreach ($externalData as $quizData) {
                $quizInfo = [
                    'external_quiz_id' => $quizData['quiz_id'],
                    'name_ar' => $quizData['name_ar'],
                    'name_en' => $quizData['name_en'],
                    'is_exam' => $quizData['is_exam'] ?? 0,
                    'questions_count' => count($quizData['questions'] ?? [])
                ];

                // Vérifier si déjà importé
                $existingQuiz = Quiz::where('name_ar', $quizData['name_ar'])
                    ->where('name_en', $quizData['name_en'])
                    ->first();

                if ($existingQuiz) {
                    $quizInfo['status'] = 'already_imported';
                    $quizInfo['local_quiz_id'] = $existingQuiz->id;
                    $alreadyImported[] = $quizInfo;
                } else {
                    $quizInfo['status'] = 'available_for_import';
                    $availableQuizzes[] = $quizInfo;
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'total_external_quizzes' => count($externalData),
                    'available_for_import' => count($availableQuizzes),
                    'already_imported' => count($alreadyImported),
                    'available_quizzes' => $availableQuizzes,
                    'imported_quizzes' => $alreadyImported
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la vérification des quiz externes: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la vérification des quiz externes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Synchroniser les quiz (mettre à jour les quiz existants)
     */
    public function syncQuizzes(): JsonResponse
    {
        try {
            $response = Http::timeout(30)->get('https://upskillsacademy.com/api/test');

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la récupération des données depuis l\'API externe',
                    'error' => 'HTTP ' . $response->status()
                ], 500);
            }

            $externalData = $response->json();

            DB::beginTransaction();

            $syncedQuizzes = [];
            $newQuizzes = [];

            foreach ($externalData as $quizData) {
                // Chercher le quiz existant
                $existingQuiz = Quiz::where('name_ar', $quizData['name_ar'])
                    ->where('name_en', $quizData['name_en'])
                    ->first();

                if ($existingQuiz) {
                    // Mettre à jour le quiz existant
                    $existingQuiz->update([
                        'name_ar' => $quizData['name_ar'],
                        'name_en' => $quizData['name_en']
                    ]);

                    // Supprimer les anciennes questions et réponses
                    foreach ($existingQuiz->questions as $question) {
                        $question->reponses()->delete();
                    }
                    $existingQuiz->questions()->delete();

                    // Recréer les questions et réponses
                    $questionsCount = 0;
                    $answersCount = 0;

                    if (isset($quizData['questions']) && is_array($quizData['questions'])) {
                        foreach ($quizData['questions'] as $questionData) {
                            $question = Question::create([
                                'name_ar' => $questionData['name_ar'],
                                'name_en' => $questionData['name_en'],
                                'point' => 10,
                                'quiz_id' => $existingQuiz->id
                            ]);

                            $questionsCount++;

                            if (isset($questionData['answers']) && is_array($questionData['answers'])) {
                                foreach ($questionData['answers'] as $answerData) {
                                    ReponseQuestion::create([
                                        'titre_ar' => $answerData['name_ar'],
                                        'titre_en' => $answerData['name_en'],
                                        'is_correcte' => (bool) $answerData['is_correct'],
                                        'question_id' => $question->id
                                    ]);
                                    $answersCount++;
                                }
                            }
                        }
                    }

                    $syncedQuizzes[] = [
                        'quiz_id' => $existingQuiz->id,
                        'name_ar' => $existingQuiz->name_ar,
                        'name_en' => $existingQuiz->name_en,
                        'questions_count' => $questionsCount,
                        'answers_count' => $answersCount,
                        'action' => 'updated'
                    ];
                } else {
                    // Créer un nouveau quiz
                    $quiz = Quiz::create([
                        'name_ar' => $quizData['name_ar'],
                        'name_en' => $quizData['name_en'],
                        'score' => 100,
                        'type_id' => 1
                    ]);

                    $questionsCount = 0;
                    $answersCount = 0;

                    if (isset($quizData['questions']) && is_array($quizData['questions'])) {
                        foreach ($quizData['questions'] as $questionData) {
                            $question = Question::create([
                                'name_ar' => $questionData['name_ar'],
                                'name_en' => $questionData['name_en'],
                                'point' => 10,
                                'quiz_id' => $quiz->id
                            ]);

                            $questionsCount++;

                            if (isset($questionData['answers']) && is_array($questionData['answers'])) {
                                foreach ($questionData['answers'] as $answerData) {
                                    ReponseQuestion::create([
                                        'titre_ar' => $answerData['name_ar'],
                                        'titre_en' => $answerData['name_en'],
                                        'is_correcte' => (bool) $answerData['is_correct'],
                                        'question_id' => $question->id
                                    ]);
                                    $answersCount++;
                                }
                            }
                        }
                    }

                    $newQuizzes[] = [
                        'quiz_id' => $quiz->id,
                        'name_ar' => $quiz->name_ar,
                        'name_en' => $quiz->name_en,
                        'questions_count' => $questionsCount,
                        'answers_count' => $answersCount,
                        'action' => 'created'
                    ];
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Synchronisation réussie',
                'data' => [
                    'updated_quizzes' => count($syncedQuizzes),
                    'new_quizzes' => count($newQuizzes),
                    'synced_quizzes' => $syncedQuizzes,
                    'created_quizzes' => $newQuizzes
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erreur lors de la synchronisation des quiz: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la synchronisation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Importer uniquement les questions depuis la base de données pour création de quiz
     */
    public function importQuestions(Request $request): JsonResponse
    {
        try {
            // Récupérer l'ID du quiz sélectionné
            $quizId = $request->input('quiz_index', 0);

            // Fetch quiz from database with questions and answers
            $quiz = Quiz::with(['questions.reponses'])->find($quizId);

            if (!$quiz) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quiz non trouvé avec l\'ID spécifié'
                ], 404);
            }

            // Format questions and answers for the frontend
            $questions = [];

            foreach ($quiz->questions as $question) {
                $answers = [];

                foreach ($question->reponses as $answer) {
                    $answers[] = [
                        'name_ar' => $answer->titre_ar ?? '',
                        'name_en' => $answer->titre_en ?? '',
                        'is_correct' => (bool) ($answer->is_correcte ?? false)
                    ];
                }

                $questions[] = [
                    'name_ar' => $question->name_ar ?? '',
                    'name_en' => $question->name_en ?? '',
                    'answers' => $answers
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Questions importées avec succès depuis "' . ($quiz->name_en ?? $quiz->name_ar) . '"',
                'questions' => $questions,
                'total' => count($questions)
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'importation des questions: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'importation des questions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer la liste des quiz disponibles depuis la base de données
     */
    public function getAvailableQuizzes(): JsonResponse
    {
        try {
            // Fetch all quizzes from database with their questions count
            $dbQuizzes = Quiz::withCount('questions')->get();

            // Format quizzes for the dropdown
            $quizzes = [];
            foreach ($dbQuizzes as $index => $quiz) {
                $quizzes[] = [
                    'index' => $quiz->id, // Use quiz ID instead of array index
                    'name_ar' => $quiz->name_ar ?? 'Sans titre (AR)',
                    'name_en' => $quiz->name_en ?? 'Untitled (EN)',
                    'questions_count' => $quiz->questions_count ?? 0
                ];
            }

            return response()->json([
                'success' => true,
                'quizzes' => $quizzes,
                'total' => count($quizzes)
            ], 200)->header('Cache-Control', 'no-cache, no-store, must-revalidate');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des quiz disponibles: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des quiz disponibles',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Données d'exemple pour les quiz
     */
    private function getSampleQuizData(): array
    {
        return [
            [
                'name_ar' => 'اختبار اللغة العربية',
                'name_en' => 'Arabic Language Test',
                'questions' => [
                    [
                        'name_ar' => 'ما هي عاصمة المملكة العربية السعودية؟',
                        'name_en' => 'What is the capital of Saudi Arabia?',
                        'answers' => [
                            ['name_ar' => 'الرياض', 'name_en' => 'Riyadh', 'is_correct' => true],
                            ['name_ar' => 'جدة', 'name_en' => 'Jeddah', 'is_correct' => false],
                            ['name_ar' => 'مكة', 'name_en' => 'Mecca', 'is_correct' => false],
                            ['name_ar' => 'الدمام', 'name_en' => 'Dammam', 'is_correct' => false],
                        ]
                    ],
                    [
                        'name_ar' => 'كم عدد أحرف اللغة العربية؟',
                        'name_en' => 'How many letters are in the Arabic alphabet?',
                        'answers' => [
                            ['name_ar' => '26', 'name_en' => '26', 'is_correct' => false],
                            ['name_ar' => '28', 'name_en' => '28', 'is_correct' => true],
                            ['name_ar' => '30', 'name_en' => '30', 'is_correct' => false],
                            ['name_ar' => '32', 'name_en' => '32', 'is_correct' => false],
                        ]
                    ]
                ]
            ],
            [
                'name_ar' => 'اختبار الرياضيات',
                'name_en' => 'Mathematics Test',
                'questions' => [
                    [
                        'name_ar' => 'ما هو ناتج 5 + 7؟',
                        'name_en' => 'What is 5 + 7?',
                        'answers' => [
                            ['name_ar' => '10', 'name_en' => '10', 'is_correct' => false],
                            ['name_ar' => '11', 'name_en' => '11', 'is_correct' => false],
                            ['name_ar' => '12', 'name_en' => '12', 'is_correct' => true],
                            ['name_ar' => '13', 'name_en' => '13', 'is_correct' => false],
                        ]
                    ],
                    [
                        'name_ar' => 'ما هو الجذر التربيعي لـ 64؟',
                        'name_en' => 'What is the square root of 64?',
                        'answers' => [
                            ['name_ar' => '6', 'name_en' => '6', 'is_correct' => false],
                            ['name_ar' => '7', 'name_en' => '7', 'is_correct' => false],
                            ['name_ar' => '8', 'name_en' => '8', 'is_correct' => true],
                            ['name_ar' => '9', 'name_en' => '9', 'is_correct' => false],
                        ]
                    ],
                    [
                        'name_ar' => 'ما هو ناتج 12 × 12؟',
                        'name_en' => 'What is 12 × 12?',
                        'answers' => [
                            ['name_ar' => '120', 'name_en' => '120', 'is_correct' => false],
                            ['name_ar' => '124', 'name_en' => '124', 'is_correct' => false],
                            ['name_ar' => '144', 'name_en' => '144', 'is_correct' => true],
                            ['name_ar' => '148', 'name_en' => '148', 'is_correct' => false],
                        ]
                    ]
                ]
            ],
            [
                'name_ar' => 'اختبار العلوم',
                'name_en' => 'Science Test',
                'questions' => [
                    [
                        'name_ar' => 'ما هو الكوكب الأقرب إلى الشمس؟',
                        'name_en' => 'Which planet is closest to the Sun?',
                        'answers' => [
                            ['name_ar' => 'الزهرة', 'name_en' => 'Venus', 'is_correct' => false],
                            ['name_ar' => 'عطارد', 'name_en' => 'Mercury', 'is_correct' => true],
                            ['name_ar' => 'المريخ', 'name_en' => 'Mars', 'is_correct' => false],
                            ['name_ar' => 'الأرض', 'name_en' => 'Earth', 'is_correct' => false],
                        ]
                    ],
                    [
                        'name_ar' => 'كم عدد العظام في جسم الإنسان البالغ؟',
                        'name_en' => 'How many bones are in an adult human body?',
                        'answers' => [
                            ['name_ar' => '196', 'name_en' => '196', 'is_correct' => false],
                            ['name_ar' => '206', 'name_en' => '206', 'is_correct' => true],
                            ['name_ar' => '216', 'name_en' => '216', 'is_correct' => false],
                            ['name_ar' => '226', 'name_en' => '226', 'is_correct' => false],
                        ]
                    ]
                ]
            ]
        ];
    }
}
