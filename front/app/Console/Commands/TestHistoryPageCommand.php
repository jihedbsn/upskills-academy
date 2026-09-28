<?php

namespace App\Console\Commands;

use App\Models\HistoriqueQuiz;
use App\Models\Student;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class TestHistoryPageCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:history-page-direct {--student-id=}';

    /**
     * The console command description.
     */
    protected $description = 'Tester directement la page d\'historique';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $studentId = $this->option('student-id') ?? 1;
        
        $this->info("🧪 Test direct de la page d'historique pour l'étudiant ID: {$studentId}");
        $this->newLine();

        // Simuler l'authentification
        $student = Student::find($studentId);
        if (!$student) {
            $this->error("❌ Étudiant ID {$studentId} non trouvé");
            return 1;
        }

        // Simuler Auth::user() = $student
        Auth::setUser($student);
        
        $this->info("👤 Utilisateur connecté: {$student->full_name} (ID: {$student->id})");

        // Simuler la logique du contrôleur
        $this->newLine();
        $this->info("🔄 Simulation de StudentDashboardController::historyIndex()...");
        
        // Récupérer l'historique des quiz avec les détails des réponses
        $query = \App\Models\HistoriqueQuiz::with(['product', 'quiz'])
            ->where('student_id', $student->id)
            ->whereNull('question_id') // Seulement les résumés de tentative
            ->orderBy('completed_at', 'desc');

        $historique = $query->get();
        
        $this->info("📊 Résultats de la requête:");
        $this->info("   • Nombre d'enregistrements trouvés: {$historique->count()}");
        
        if ($historique->count() > 0) {
            $this->info("   ✅ Des données ont été trouvées !");
            
            foreach ($historique as $attempt) {
                $quizType = $attempt->quiz_type == 1 ? 'Examen' : 'Quiz';
                $status = $attempt->success ? 'Réussi' : 'Échoué';
                $this->info("   📝 Quiz: {$quizType} - Score: {$attempt->score_percentage}% - {$status}");
                $productTitle = $attempt->product ? $attempt->product->titre : 'N/A';
                $quizName = $attempt->quiz ? $attempt->quiz->name_en : 'N/A';
                $this->info("      Product: {$productTitle}");
                $this->info("      Quiz: {$quizName}");
            }
            
            // Pour chaque tentative, récupérer les détails des réponses
            foreach ($historique as $attempt) {
                // Récupérer les détails des questions depuis HistoriqueQuiz (pas ResponseQuiz)
                $responses = \App\Models\HistoriqueQuiz::with(['question', 'response'])
                    ->where('student_id', $student->id)
                    ->where('quiz_id', $attempt->quiz_id)
                    ->where('product_id', $attempt->product_id)
                    ->where('attempt_number', $attempt->attempt_number)
                    ->whereNotNull('question_id') // Seulement les détails, pas le résumé
                    ->get();
                
                $attempt->responses = $responses;
                
                $this->info("   🔍 Détails pour la tentative {$attempt->attempt_number}:");
                $this->info("      • Réponses détaillées trouvées: {$responses->count()}");
            }
            
            // Statistiques
            $stats = \App\Models\HistoriqueQuiz::getStudentStats($student->id);
            $this->newLine();
            $this->info("📈 Statistiques calculées:");
            $this->info("   • Total tentatives: {$stats['total_attempts']}");
            $this->info("   • Score moyen: {$stats['average_score']}%");
            $this->info("   • Taux de réussite: {$stats['success_rate']}%");
            
            $this->newLine();
            $this->info("✅ La page d'historique devrait afficher des données !");
            
        } else {
            $this->error("❌ Aucune donnée trouvée - la page affichera 'No History Found'");
            
            // Diagnostic
            $this->newLine();
            $this->info("🔍 Diagnostic:");
            
            $allRecords = \App\Models\HistoriqueQuiz::where('student_id', $student->id)->get();
            $this->info("   • Tous les enregistrements de l'étudiant: {$allRecords->count()}");
            
            $summaryRecords = \App\Models\HistoriqueQuiz::where('student_id', $student->id)->whereNull('question_id')->get();
            $this->info("   • Enregistrements de résumé (question_id = NULL): {$summaryRecords->count()}");
            
            if ($summaryRecords->count() > 0) {
                $this->info("   • Les enregistrements de résumé existent mais ne sont pas récupérés par la requête");
                $this->info("   • Vérifiez les relations 'product' et 'quiz'");
                
                foreach ($summaryRecords as $record) {
                    $this->info("     - Record ID: {$record->id}, product_id: {$record->product_id}, quiz_id: {$record->quiz_id}");
                    $this->info("       Product exists: " . ($record->product ? 'YES' : 'NO'));
                    $this->info("       Quiz exists: " . ($record->quiz ? 'YES' : 'NO'));
                }
            }
        }

        return 0;
    }
}