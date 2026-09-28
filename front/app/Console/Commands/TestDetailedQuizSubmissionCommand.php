<?php

namespace App\Console\Commands;

use App\Models\HistoriqueQuiz;
use App\Models\Product;
use App\Models\Student;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\ReponseQuestion;
use Illuminate\Console\Command;

class TestDetailedQuizSubmissionCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:detailed-quiz-submission {--student-id=} {--product-id=} {--quiz-id=}';

    /**
     * The console command description.
     */
    protected $description = 'Tester la soumission détaillée d\'un quiz avec historique complet';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Test de soumission détaillée de quiz');
        $this->newLine();

        // Récupérer les paramètres
        $studentId = $this->option('student-id') ?? 1;
        $productId = $this->option('product-id') ?? 11;
        $quizId = $this->option('quiz-id') ?? 110;

        // Vérifier que les entités existent
        $student = Student::find($studentId);
        $product = Product::find($productId);
        $quiz = Quiz::find($quizId);

        if (!$student) {
            $this->error("❌ Étudiant ID {$studentId} non trouvé");
            return 1;
        }

        if (!$product) {
            $this->error("❌ Produit ID {$productId} non trouvé");
            return 1;
        }

        if (!$quiz) {
            $this->error("❌ Quiz ID {$quizId} non trouvé");
            return 1;
        }

        $this->info("👤 Étudiant: {$student->full_name} (ID: {$student->id})");
        $this->info("📚 Cours: {$product->titre} (ID: {$product->id})");
        $this->info("📝 Quiz: {$quiz->name_en} (ID: {$quiz->id})");
        $this->newLine();

        // Simuler une soumission de quiz avec détails
        $this->info('🎯 Simulation de soumission détaillée de quiz...');
        
        try {
            $attemptNumber = 1;
            $totalQuestions = 3;
            $correctAnswers = 2;
            $score = ($correctAnswers / $totalQuestions) * 100;
            $success = $score >= 50;
            $timeSpent = 900; // 15 minutes
            $startTime = now()->subMinutes(15);
            $completedAt = now();

            // 1. Créer l'enregistrement de résumé
            $summaryRecord = HistoriqueQuiz::create([
                'product_id' => $product->id,
                'student_id' => $student->id,
                'quiz_id' => $quiz->id,
                'question_id' => null, // Résumé
                'response_id' => null, // Résumé
                'is_correct' => null, // Résumé
                'quiz_type' => $quiz->type_id,
                'attempt_number' => $attemptNumber,
                'total_questions' => $totalQuestions,
                'correct_answers' => $correctAnswers,
                'score_percentage' => $score,
                'success' => $success,
                'time_spent' => $timeSpent,
                'started_at' => $startTime,
                'completed_at' => $completedAt
            ]);

            $this->info("✅ Résumé créé (ID: {$summaryRecord->id})");

            // 2. Utiliser des questions existantes
            $existingQuestions = Question::where('quiz_id', $quiz->id)->take($totalQuestions)->get();
            
            if ($existingQuestions->count() < $totalQuestions) {
                $this->warn("⚠️ Seulement {$existingQuestions->count()} questions trouvées, on utilisera ce qui est disponible");
                $totalQuestions = $existingQuestions->count();
            }

            $questions = [];
            $responses = [];

            foreach ($existingQuestions as $i => $question) {
                $questions[] = $question;

                // Récupérer les réponses existantes pour cette question
                $questionResponses = ReponseQuestion::where('question_id', $question->id)->get();
                
                if ($questionResponses->count() >= 2) {
                    $correctResponse = $questionResponses->where('is_correcte', true)->first();
                    $wrongResponse = $questionResponses->where('is_correcte', false)->first();
                } else {
                    $this->warn("⚠️ Question {$question->id} n'a pas assez de réponses, on utilisera les réponses disponibles");
                    $correctResponse = $questionResponses->first();
                    $wrongResponse = $questionResponses->last() ?: $questionResponses->first();
                }

                $responses[] = [
                    'question' => $question,
                    'correct' => $correctResponse,
                    'wrong' => $wrongResponse
                ];
            }

            $this->info("✅ Questions et réponses créées");

            // 3. Créer les enregistrements détaillés
            for ($i = 0; $i < $totalQuestions; $i++) {
                $isCorrect = $i < $correctAnswers; // Les 2 premières sont correctes
                $selectedResponse = $isCorrect ? $responses[$i]['correct'] : $responses[$i]['wrong'];

                $detailRecord = HistoriqueQuiz::create([
                    'product_id' => $product->id,
                    'student_id' => $student->id,
                    'quiz_id' => $quiz->id,
                    'question_id' => $questions[$i]->id,
                    'response_id' => $selectedResponse->id,
                    'is_correct' => $isCorrect,
                    'quiz_type' => $quiz->type_id,
                    'attempt_number' => $attemptNumber,
                    'total_questions' => $totalQuestions,
                    'correct_answers' => $correctAnswers,
                    'score_percentage' => $score,
                    'success' => $success,
                    'time_spent' => $timeSpent,
                    'started_at' => $startTime,
                    'completed_at' => $completedAt
                ]);

                $this->info("✅ Détail " . ($i + 1) . " créé (ID: {$detailRecord->id}) - " . ($isCorrect ? 'Correct' : 'Incorrect'));
            }

            // 4. Vérifier les statistiques
            $stats = HistoriqueQuiz::getStudentStats($student->id, $product->id);
            $this->newLine();
            $this->info('📊 Statistiques de l\'étudiant:');
            $this->info("   • Total tentatives: {$stats['total_attempts']}");
            $this->info("   • Quiz uniques: {$stats['unique_quizzes_taken']}");
            $this->info("   • Cours uniques: {$stats['unique_courses']}");
            $this->info("   • Score moyen: {$stats['average_score']}%");
            $this->info("   • Taux de réussite: {$stats['success_rate']}%");

            // 5. Vérifier les enregistrements
            $summaryRecords = HistoriqueQuiz::where('student_id', $student->id)
                ->whereNull('question_id')
                ->count();
            
            $detailRecords = HistoriqueQuiz::where('student_id', $student->id)
                ->whereNotNull('question_id')
                ->count();

            $this->newLine();
            $this->info('🔍 Vérification des enregistrements:');
            $this->info("   • Résumés: {$summaryRecords}");
            $this->info("   • Détails: {$detailRecords}");

            $this->newLine();
            $this->info('🎉 Test terminé avec succès !');
            
            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Erreur lors de la création: " . $e->getMessage());
            $this->error("   Trace: " . $e->getTraceAsString());
            return 1;
        }
    }
}
