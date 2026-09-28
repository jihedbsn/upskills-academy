<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\ReponseQuestion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ImportQuizzesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quiz:import {--quiz-id= : ID du quiz spécifique à importer} {--all : Importer tous les quiz} {--sync : Synchroniser tous les quiz}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importer les quiz depuis l\'API externe https://upskillsacademy.com/api/test';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Début de l\'importation des quiz...');
        
        try {
            // Récupérer les données depuis l'API externe
            $this->info('📡 Récupération des données depuis l\'API externe...');
            $response = Http::timeout(30)->get('https://upskillsacademy.com/api/test');
            
            if (!$response->successful()) {
                $this->error('❌ Erreur lors de la récupération des données: HTTP ' . $response->status());
                return 1;
            }

            $externalData = $response->json();
            
            if (!is_array($externalData)) {
                $this->error('❌ Format de données invalide reçu de l\'API externe');
                return 1;
            }

            $this->info('✅ Données récupérées avec succès (' . count($externalData) . ' quiz trouvés)');

            // Traitement selon les options
            if ($this->option('quiz-id')) {
                $this->importSpecificQuiz($externalData, $this->option('quiz-id'));
            } elseif ($this->option('sync')) {
                $this->syncAllQuizzes($externalData);
            } elseif ($this->option('all')) {
                $this->importAllQuizzes($externalData);
            } else {
                $this->showAvailableQuizzes($externalData);
            }

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Erreur: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Afficher les quiz disponibles
     */
    private function showAvailableQuizzes($externalData)
    {
        $this->info('📋 Quiz disponibles dans l\'API externe:');
        $this->newLine();

        $headers = ['ID', 'Nom (AR)', 'Nom (EN)', 'Type', 'Questions', 'Statut'];
        $rows = [];

        foreach ($externalData as $quizData) {
            $existingQuiz = Quiz::where('name_ar', $quizData['name_ar'])
                ->where('name_en', $quizData['name_en'])
                ->first();

            $status = $existingQuiz ? '✅ Déjà importé (ID: ' . $existingQuiz->id . ')' : '⏳ Disponible';
            
            $rows[] = [
                $quizData['quiz_id'],
                $quizData['name_ar'],
                $quizData['name_en'],
                ($quizData['is_exam'] ?? 0) ? 'Examen' : 'Quiz',
                count($quizData['questions'] ?? []),
                $status
            ];
        }

        $this->table($headers, $rows);
        $this->newLine();
        $this->info('💡 Utilisez les options suivantes:');
        $this->line('  --all : Importer tous les nouveaux quiz');
        $this->line('  --sync : Synchroniser tous les quiz (mise à jour + création)');
        $this->line('  --quiz-id=ID : Importer un quiz spécifique');
    }

    /**
     * Importer tous les quiz
     */
    private function importAllQuizzes($externalData)
    {
        $this->info('📥 Importation de tous les nouveaux quiz...');
        
        DB::beginTransaction();
        
        try {
            $importedCount = 0;
            $skippedCount = 0;
            $totalQuestions = 0;
            $totalAnswers = 0;

            foreach ($externalData as $quizData) {
                // Vérifier si le quiz existe déjà
                $existingQuiz = Quiz::where('name_ar', $quizData['name_ar'])
                    ->where('name_en', $quizData['name_en'])
                    ->first();

                if ($existingQuiz) {
                    $this->warn("⏭️  Quiz ignoré (déjà existant): {$quizData['name_ar']}");
                    $skippedCount++;
                    continue;
                }

                $result = $this->importQuiz($quizData);
                if ($result) {
                    $importedCount++;
                    $totalQuestions += $result['questions'];
                    $totalAnswers += $result['answers'];
                    $this->info("✅ Quiz importé: {$quizData['name_ar']} (ID: {$result['quiz_id']})");
                }
            }

            DB::commit();

            $this->newLine();
            $this->info('🎉 Importation terminée!');
            $this->line("📊 Statistiques:");
            $this->line("  - Quiz importés: {$importedCount}");
            $this->line("  - Quiz ignorés: {$skippedCount}");
            $this->line("  - Questions créées: {$totalQuestions}");
            $this->line("  - Réponses créées: {$totalAnswers}");

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Synchroniser tous les quiz
     */
    private function syncAllQuizzes($externalData)
    {
        $this->info('🔄 Synchronisation de tous les quiz...');
        
        DB::beginTransaction();
        
        try {
            $updatedCount = 0;
            $createdCount = 0;
            $totalQuestions = 0;
            $totalAnswers = 0;

            foreach ($externalData as $quizData) {
                $existingQuiz = Quiz::where('name_ar', $quizData['name_ar'])
                    ->where('name_en', $quizData['name_en'])
                    ->first();

                if ($existingQuiz) {
                    // Mettre à jour le quiz existant
                    $result = $this->updateQuiz($existingQuiz, $quizData);
                    $updatedCount++;
                    $totalQuestions += $result['questions'];
                    $totalAnswers += $result['answers'];
                    $this->info("🔄 Quiz mis à jour: {$quizData['name_ar']} (ID: {$existingQuiz->id})");
                } else {
                    // Créer un nouveau quiz
                    $result = $this->importQuiz($quizData);
                    if ($result) {
                        $createdCount++;
                        $totalQuestions += $result['questions'];
                        $totalAnswers += $result['answers'];
                        $this->info("✅ Quiz créé: {$quizData['name_ar']} (ID: {$result['quiz_id']})");
                    }
                }
            }

            DB::commit();

            $this->newLine();
            $this->info('🎉 Synchronisation terminée!');
            $this->line("📊 Statistiques:");
            $this->line("  - Quiz mis à jour: {$updatedCount}");
            $this->line("  - Quiz créés: {$createdCount}");
            $this->line("  - Questions traitées: {$totalQuestions}");
            $this->line("  - Réponses traitées: {$totalAnswers}");

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Importer un quiz spécifique
     */
    private function importSpecificQuiz($externalData, $quizId)
    {
        $this->info("📥 Importation du quiz ID: {$quizId}...");
        
        // Trouver le quiz spécifique
        $quizData = null;
        foreach ($externalData as $quiz) {
            if ($quiz['quiz_id'] == $quizId) {
                $quizData = $quiz;
                break;
            }
        }

        if (!$quizData) {
            $this->error("❌ Quiz avec l'ID {$quizId} non trouvé dans l'API externe");
            return;
        }

        // Vérifier si le quiz existe déjà
        $existingQuiz = Quiz::where('name_ar', $quizData['name_ar'])
            ->where('name_en', $quizData['name_en'])
            ->first();

        if ($existingQuiz) {
            $this->error("❌ Ce quiz existe déjà dans la base de données (ID: {$existingQuiz->id})");
            return;
        }

        DB::beginTransaction();
        
        try {
            $result = $this->importQuiz($quizData);
            
            if ($result) {
                DB::commit();
                $this->newLine();
                $this->info('🎉 Quiz importé avec succès!');
                $this->line("📊 Statistiques:");
                $this->line("  - Quiz ID: {$result['quiz_id']}");
                $this->line("  - Questions créées: {$result['questions']}");
                $this->line("  - Réponses créées: {$result['answers']}");
            }
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Importer un quiz
     */
    private function importQuiz($quizData)
    {
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

        return [
            'quiz_id' => $quiz->id,
            'questions' => $questionsCount,
            'answers' => $answersCount
        ];
    }

    /**
     * Mettre à jour un quiz existant
     */
    private function updateQuiz($existingQuiz, $quizData)
    {
        // Mettre à jour le quiz
        $existingQuiz->update([
            'name_ar' => $quizData['name_ar'],
            'name_en' => $quizData['name_en']
        ]);

        // Supprimer les anciennes questions et réponses
        foreach ($existingQuiz->questions as $question) {
            $question->reponses()->delete();
        }
        $existingQuiz->questions()->delete();

        $questionsCount = 0;
        $answersCount = 0;

        // Recréer les questions et réponses
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

        return [
            'questions' => $questionsCount,
            'answers' => $answersCount
        ];
    }
}
