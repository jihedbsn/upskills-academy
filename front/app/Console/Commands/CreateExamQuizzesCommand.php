<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class CreateExamQuizzesCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'exam:create-quizzes {--product-id= : ID spécifique du produit à traiter}';

    /**
     * The console command description.
     */
    protected $description = 'Créer automatiquement les quiz d\'examen pour tous les cours qui n\'en ont pas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $productId = $this->option('product-id');
        
        if ($productId) {
            $products = Product::where('id', $productId)->get();
        } else {
            $products = Product::all();
        }
        
        $created = 0;
        $skipped = 0;
        
        foreach ($products as $product) {
            $this->info("Traitement du cours: {$product->id} - {$product->titre}");
            
            // Vérifier s'il y a des quiz de type 2 (quiz de cours)
            $quizQuizzes = $product->quizzes()->where('type_id', 2)->get();
            
            if ($quizQuizzes->isEmpty()) {
                $this->warn("  ⚠️  Aucun quiz de cours (type 2) trouvé. Ignoré.");
                $skipped++;
                continue;
            }
            
            // Vérifier s'il y a déjà un quiz d'examen
            $existingExam = $product->quizzes()->where('type_id', 1)->first();
            
            if ($existingExam) {
                $this->info("  ✅ Quiz d'examen déjà existant: {$existingExam->name_en}");
                $skipped++;
                continue;
            }
            
            // Créer le quiz d'examen
            $examQuiz = $product->createExamQuizIfNotExists();
            
            if ($examQuiz) {
                $this->info("  🎯 Quiz d'examen créé: {$examQuiz->name_en}");
                $this->info("     📊 {$quizQuizzes->count()} quiz de cours → {$quizQuizzes->count()} questions d'examen");
                $created++;
            } else {
                $this->error("  ❌ Erreur lors de la création du quiz d'examen");
            }
        }
        
        $this->newLine();
        $this->info("📈 Résumé:");
        $this->info("  ✅ Quiz d'examen créés: {$created}");
        $this->info("  ⏭️  Cours ignorés: {$skipped}");
        
        return 0;
    }
}
