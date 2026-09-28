<?php

namespace App\Console\Commands;

use App\Models\StudentSuccess;
use App\Models\Student;
use Illuminate\Console\Command;

class CheckCertificatesDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'check:certificates-data';

    /**
     * The console command description.
     */
    protected $description = 'Vérifier les données des certificats';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Vérification des données des certificats');
        $this->newLine();

        // Vérifier toutes les données dans student_successes
        $totalRecords = StudentSuccess::count();
        $this->info("📊 Total enregistrements dans student_successes: {$totalRecords}");

        if ($totalRecords > 0) {
            $this->info("📋 Détails des enregistrements:");
            
            $records = StudentSuccess::with(['product', 'student'])->get();
            foreach ($records as $record) {
                $this->info("   • ID: {$record->id}");
                $this->info("     Student ID: {$record->student_id}");
                $this->info("     Product ID: {$record->product_id}");
                $this->info("     Product Title: " . ($record->product ? $record->product->titre : 'N/A'));
                $this->info("     Success: {$record->success} (" . $this->getSuccessText($record->success) . ")");
                $this->info("     Submitted At: " . ($record->submitted_at ? $record->submitted_at->format('Y-m-d H:i:s') : 'N/A'));
                $this->info("     Validated At: " . ($record->validated_at ? $record->validated_at->format('Y-m-d H:i:s') : 'N/A'));
                $this->info("     Video Link: " . ($record->lien_video ? 'Yes' : 'No'));
                $this->info("     Admin Notes: " . ($record->admin_notes ?: 'None'));
                $this->newLine();
            }

            // Statistiques
            $this->info("📈 Statistiques:");
            $this->info("   • Approuvés (success=1): " . $records->where('success', 1)->count());
            $this->info("   • En attente (success=0): " . $records->where('success', 0)->count());
            $this->info("   • Rejetés (success=-1): " . $records->where('success', -1)->count());

            // Vérifier les étudiants
            $this->newLine();
            $this->info("👥 Étudiants avec des certificats:");
            $studentIds = $records->pluck('student_id')->unique();
            foreach ($studentIds as $studentId) {
                $student = Student::find($studentId);
                if ($student) {
                    $studentRecords = $records->where('student_id', $studentId);
                    $this->info("   • {$student->full_name} (ID: {$studentId}) - {$studentRecords->count()} certificats");
                } else {
                    $this->warn("   • Étudiant ID {$studentId} non trouvé");
                }
            }

        } else {
            $this->warn("⚠️ Aucun enregistrement trouvé dans student_successes");
            $this->info("💡 Création d'un certificat de test...");
            
            // Créer un certificat de test
            $student = Student::first();
            $product = \App\Models\Product::first();
            
            if ($student && $product) {
                $certificate = StudentSuccess::create([
                    'student_id' => $student->id,
                    'product_id' => $product->id,
                    'success' => 1, // Approuvé
                    'submitted_at' => now()->subDays(5),
                    'validated_at' => now()->subDays(3),
                    'admin_notes' => 'Test certificate created'
                ]);
                
                $this->info("✅ Certificat de test créé (ID: {$certificate->id})");
                $this->info("   Student: {$student->full_name}");
                $this->info("   Product: {$product->titre}");
            } else {
                $this->error("❌ Impossible de créer un certificat de test - étudiant ou produit manquant");
            }
        }

        return 0;
    }

    private function getSuccessText($success)
    {
        switch ($success) {
            case 1: return 'Approved';
            case -1: return 'Rejected';
            default: return 'Pending';
        }
    }
}
