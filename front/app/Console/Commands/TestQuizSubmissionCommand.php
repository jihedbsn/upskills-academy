<?php

namespace App\Console\Commands;

use App\Models\HistoriqueQuiz;
use App\Models\Product;
use App\Models\Student;
use App\Models\Quiz;
use Illuminate\Console\Command;

class TestQuizSubmissionCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:quiz-submission {--student-id=} {--product-id=} {--quiz-id=}';

    /**
     * The console command description.
     */
    protected $description = 'Tester la soumission d\'un quiz et l\'enregistrement dans l\'historique';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Test de soumission de quiz');
        $this->newLine();

        // Récupérer les paramètres
        $studentId = $this->option('student-id') ?? 1;
        $productId = $this->option('product-id') ?? 1;
        $quizId = $this->option('quiz-id') ?? 1;

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

        // Simuler une soumission de quiz
        $this->info('🎯 Simulation de soumission de quiz...');
        
        try {
            // Créer un enregistrement d'historique simulé
            $historique = HistoriqueQuiz::create([
                'product_id' => $product->id,
                'student_id' => $student->id,
                'quiz_id' => $quiz->id,
                'question_id' => null,
                'response_id' => null,
                'is_correct' => null,
                'quiz_type' => $quiz->type_id,
                'attempt_number' => 1,
                'total_questions' => 10,
                'correct_answers' => 7,
                'score_percentage' => 70.0,
                'success' => true,
                'time_spent' => 900, // 15 minutes
                'started_at' => now()->subMinutes(15),
                'completed_at' => now()
            ]);

            $this->info("✅ Historique créé avec succès (ID: {$historique->id})");
            
            // Vérifier les statistiques
            $stats = HistoriqueQuiz::getStudentStats($student->id, $product->id);
            $this->newLine();
            $this->info('📊 Statistiques de l\'étudiant:');
            $this->info("   • Total tentatives: {$stats['total_attempts']}");
            $this->info("   • Quiz uniques: {$stats['unique_quizzes_taken']}");
            $this->info("   • Cours uniques: {$stats['unique_courses']}");
            $this->info("   • Score moyen: {$stats['average_score']}%");
            $this->info("   • Taux de réussite: {$stats['success_rate']}%");
            $this->info("   • Temps moyen: {$stats['average_time_spent']}s");

            // Vérifier les scopes
            $this->newLine();
            $this->info('🔍 Test des scopes:');
            $this->info("   • Quiz réussis: " . HistoriqueQuiz::successful()->count());
            $this->info("   • Quiz échoués: " . HistoriqueQuiz::failed()->count());
            $this->info("   • Quiz récents (7 jours): " . HistoriqueQuiz::recent(7)->count());

            $this->newLine();
            $this->info('🎉 Test terminé avec succès !');
            
            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Erreur lors de la création: " . $e->getMessage());
            return 1;
        }
    }
}
