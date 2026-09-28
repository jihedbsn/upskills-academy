<?php

namespace App\Console\Commands;

use App\Models\HistoriqueQuiz;
use App\Models\Student;
use Illuminate\Console\Command;

class CheckHistoryDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'check:history-data {--student-id=}';

    /**
     * The console command description.
     */
    protected $description = 'Vérifier les données d\'historique';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $studentId = $this->option('student-id') ?? 1;
        
        $this->info("🔍 Vérification des données d'historique pour l'étudiant ID: {$studentId}");
        $this->newLine();

        // Vérifier l'étudiant
        $student = Student::find($studentId);
        if (!$student) {
            $this->error("❌ Étudiant ID {$studentId} non trouvé");
            return 1;
        }

        $this->info("👤 Étudiant: {$student->full_name}");

        // Vérifier les données dans historique_quizzes
        $totalRecords = HistoriqueQuiz::count();
        $summaryRecords = HistoriqueQuiz::whereNull('question_id')->count();
        $detailRecords = HistoriqueQuiz::whereNotNull('question_id')->count();

        $this->newLine();
        $this->info("📊 Données dans historique_quizzes:");
        $this->info("   • Total: {$totalRecords}");
        $this->info("   • Résumés (question_id = NULL): {$summaryRecords}");
        $this->info("   • Détails (question_id != NULL): {$detailRecords}");

        // Vérifier les données pour cet étudiant
        $studentRecords = HistoriqueQuiz::where('student_id', $studentId)->get();
        $studentSummaryRecords = HistoriqueQuiz::where('student_id', $studentId)->whereNull('question_id')->get();
        $studentDetailRecords = HistoriqueQuiz::where('student_id', $studentId)->whereNotNull('question_id')->get();

        $this->newLine();
        $this->info("👤 Données pour l'étudiant {$studentId}:");
        $this->info("   • Total: {$studentRecords->count()}");
        $this->info("   • Résumés: {$studentSummaryRecords->count()}");
        $this->info("   • Détails: {$studentDetailRecords->count()}");

        if ($studentSummaryRecords->count() > 0) {
            $this->newLine();
            $this->info("📝 Résumés trouvés:");
            foreach ($studentSummaryRecords as $record) {
                $this->info("   • Quiz ID: {$record->quiz_id}, Product ID: {$record->product_id}, Score: {$record->score_percentage}%, Completed: {$record->completed_at}");
            }
        }

        // Simuler la requête du contrôleur
        $this->newLine();
        $this->info("🔍 Simulation de la requête du contrôleur:");
        
        $controllerQuery = HistoriqueQuiz::with(['product', 'quiz'])
            ->where('student_id', $studentId)
            ->whereNull('question_id') // Seulement les résumés
            ->orderBy('completed_at', 'desc');

        $controllerResults = $controllerQuery->get();
        
        $this->info("   • Résultats trouvés: {$controllerResults->count()}");
        
        if ($controllerResults->count() > 0) {
            $this->info("   📋 Détails des résultats:");
            foreach ($controllerResults as $result) {
                $this->info("     - Quiz: {$result->quiz_id}, Product: {$result->product_id}, Score: {$result->score_percentage}%");
                if ($result->product) {
                    $this->info("       Product title: {$result->product->titre}");
                }
                if ($result->quiz) {
                    $this->info("       Quiz name: {$result->quiz->name_en}");
                }
            }
        } else {
            $this->warn("   ⚠️ Aucun résultat trouvé par la requête du contrôleur");
            
            // Vérifier pourquoi
            $this->newLine();
            $this->info("🔍 Diagnostic:");
            
            $allStudentRecords = HistoriqueQuiz::where('student_id', $studentId)->get();
            $this->info("   • Tous les enregistrements de l'étudiant: {$allStudentRecords->count()}");
            
            $nullQuestionRecords = HistoriqueQuiz::where('student_id', $studentId)->whereNull('question_id')->get();
            $this->info("   • Enregistrements avec question_id NULL: {$nullQuestionRecords->count()}");
            
            if ($nullQuestionRecords->count() == 0) {
                $this->info("   • Tous les enregistrements ont un question_id non-null");
                foreach ($allStudentRecords as $record) {
                    $this->info("     - Record ID: {$record->id}, question_id: " . ($record->question_id ?? 'NULL'));
                }
            }
        }

        return 0;
    }
}
