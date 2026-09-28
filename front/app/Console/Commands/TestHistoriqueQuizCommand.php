<?php

namespace App\Console\Commands;

use App\Models\HistoriqueQuiz;
use App\Models\Product;
use App\Models\User;
use Illuminate\Console\Command;

class TestHistoriqueQuizCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:historique-quiz';

    /**
     * The console command description.
     */
    protected $description = 'Tester le système d\'historique des quiz';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Test du système d\'historique des quiz');
        $this->newLine();

        // Test 1: Vérifier que la table existe
        $this->info('1️⃣ Test de l\'existence de la table...');
        try {
            $count = HistoriqueQuiz::count();
            $this->info("   ✅ Table 'historique_quizzes' existe avec {$count} enregistrements");
        } catch (\Exception $e) {
            $this->error("   ❌ Erreur: " . $e->getMessage());
            return 1;
        }

        // Test 2: Vérifier les relations
        $this->info('2️⃣ Test des relations...');
        try {
            $student = \App\Models\Student::first();
            if ($student) {
                $historiqueCount = $student->historiqueQuizzes()->count();
                $this->info("   ✅ Relation Student->historiqueQuizzes: {$historiqueCount} enregistrements");
            } else {
                $this->warn("   ⚠️  Aucun étudiant trouvé");
            }

            $product = Product::first();
            if ($product) {
                $historiqueCount = $product->historiqueQuizzes()->count();
                $this->info("   ✅ Relation Product->historiqueQuizzes: {$historiqueCount} enregistrements");
            } else {
                $this->warn("   ⚠️  Aucun produit trouvé");
            }
        } catch (\Exception $e) {
            $this->error("   ❌ Erreur dans les relations: " . $e->getMessage());
        }

        // Test 3: Tester les méthodes statiques
        $this->info('3️⃣ Test des méthodes statiques...');
        try {
            if ($student && $product) {
                $stats = HistoriqueQuiz::getStudentStats($student->id, $product->id);
                $this->info("   ✅ getStudentStats(): " . json_encode($stats, JSON_PRETTY_PRINT));
            }

            if ($product) {
                $stats = HistoriqueQuiz::getProductStats($product->id);
                $this->info("   ✅ getProductStats(): " . json_encode($stats, JSON_PRETTY_PRINT));
            }
        } catch (\Exception $e) {
            $this->error("   ❌ Erreur dans les méthodes statiques: " . $e->getMessage());
        }

        // Test 4: Tester les scopes
        $this->info('4️⃣ Test des scopes...');
        try {
            $recentCount = HistoriqueQuiz::recent(30)->count();
            $this->info("   ✅ Scope recent(30): {$recentCount} enregistrements");

            $successfulCount = HistoriqueQuiz::successful()->count();
            $this->info("   ✅ Scope successful(): {$successfulCount} enregistrements");

            $failedCount = HistoriqueQuiz::failed()->count();
            $this->info("   ✅ Scope failed(): {$failedCount} enregistrements");
        } catch (\Exception $e) {
            $this->error("   ❌ Erreur dans les scopes: " . $e->getMessage());
        }

        $this->newLine();
        $this->info('🎉 Tests terminés !');
        
        return 0;
    }
}
