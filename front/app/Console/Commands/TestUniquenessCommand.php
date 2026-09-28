<?php

namespace App\Console\Commands;

use App\Models\HistoriqueQuiz;
use Illuminate\Console\Command;

class TestUniquenessCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:uniqueness';

    /**
     * The console command description.
     */
    protected $description = 'Tester la contrainte d\'unicité';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Test de contrainte d\'unicité');
        $this->newLine();

        try {
            // Essayer de créer un doublon
            HistoriqueQuiz::create([
                'product_id' => 11,
                'student_id' => 1,
                'quiz_id' => 110,
                'question_id' => null,
                'response_id' => null,
                'is_correct' => null,
                'quiz_type' => 2,
                'attempt_number' => 1, // Même tentative que celle qui existe
                'total_questions' => 10,
                'correct_answers' => 5,
                'score_percentage' => 50.0,
                'success' => true,
                'time_spent' => 800,
                'started_at' => now()->subMinutes(10),
                'completed_at' => now()
            ]);

            $this->error('❌ Erreur: Le doublon aurait dû être bloqué !');
            return 1;

        } catch (\Exception $e) {
            $this->info('✅ Contrainte d\'unicité fonctionne correctement !');
            $this->info("   Message: {$e->getMessage()}");
            
            // Tester avec updateOrCreate (qui devrait fonctionner)
            $this->newLine();
            $this->info('🔄 Test updateOrCreate...');
            
            try {
                $historique = HistoriqueQuiz::updateOrCreate(
                    [
                        'student_id' => 1,
                        'quiz_id' => 110,
                        'attempt_number' => 1
                    ],
                    [
                        'product_id' => 11,
                        'question_id' => null,
                        'response_id' => null,
                        'is_correct' => null,
                        'quiz_type' => 2,
                        'total_questions' => 15,
                        'correct_answers' => 12,
                        'score_percentage' => 80.0,
                        'success' => true,
                        'time_spent' => 1200,
                        'started_at' => now()->subMinutes(20),
                        'completed_at' => now()
                    ]
                );

                $this->info("✅ updateOrCreate fonctionne (ID: {$historique->id})");
                $this->info("   Total questions mis à jour: {$historique->total_questions}");
                $this->info("   Score mis à jour: {$historique->score_percentage}%");
                
            } catch (\Exception $e2) {
                $this->error("❌ updateOrCreate a échoué: {$e2->getMessage()}");
                return 1;
            }
        }

        $this->newLine();
        $this->info('🎉 Tous les tests passent !');
        return 0;
    }
}
