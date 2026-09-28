<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StudentSuccess;
use App\Models\Product;
use App\Services\CertificateGeneratorService;
use App\Services\OutboundEmailLogger;
use App\Mail\CertificateGeneratedNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class GenerateAutomaticCertificates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'certificates:generate-automatic';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate and send automatic certificates for validated student successes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting automatic certificate generation...');
        Log::info('Automatic certificate generation cron started');

        // Find all validated StudentSuccess records without certificates for products with automatic mode
        $studentSuccesses = StudentSuccess::whereHas('product', function ($query) {
            $query->where('certificate_generation_mode', 'automatic')
                  ->whereNotNull('certif_id');
        })
        ->where('success', 1)
        ->whereNotNull('validated_at')
        ->whereDoesntHave('certificates')
        ->with(['product', 'student'])
        ->get();

        if ($studentSuccesses->isEmpty()) {
            $this->info('No pending automatic certificates found.');
            Log::info('No pending automatic certificates found');
            return 0;
        }

        $this->info("Found {$studentSuccesses->count()} student successes requiring automatic certificates.");
        Log::info("Found {$studentSuccesses->count()} student successes requiring automatic certificates");

        $certificateService = app(CertificateGeneratorService::class);
        $successCount = 0;
        $errorCount = 0;

        foreach ($studentSuccesses as $studentSuccess) {
            try {
                // Generate certificate
                $certificate = $certificateService->generateCertificate($studentSuccess);

                $this->info("Certificate generated for student {$studentSuccess->student->fullname} - Product: {$studentSuccess->product->titre}");
                Log::info('Certificate generated automatically by cron', [
                    'student_success_id' => $studentSuccess->id,
                    'student_id' => $studentSuccess->student_id,
                    'product_id' => $studentSuccess->product_id,
                    'certificate_id' => $certificate->id
                ]);

                // Send email
                if ($studentSuccess->student && $studentSuccess->student->email) {
                    Mail::to($studentSuccess->student->email)->send(
                        new CertificateGeneratedNotification($certificate, $studentSuccess)
                    );

                    OutboundEmailLogger::logSent(
                        $studentSuccess->student->email,
                        'certificate_generated',
                        'Your Certificate is Ready',
                        $studentSuccess->student_id,
                        $studentSuccess->student->first_name . ' ' . $studentSuccess->student->last_name,
                        'Certificate',
                        $certificate->id
                    );

                    $this->info("Certificate email sent to {$studentSuccess->student->email}");
                    Log::info('Certificate email sent automatically by cron', [
                        'student_success_id' => $studentSuccess->id,
                        'student_id' => $studentSuccess->student_id,
                        'email' => $studentSuccess->student->email
                    ]);
                }

                $successCount++;
            } catch (\Exception $e) {
                $errorCount++;
                $this->error("Failed to process certificate for student success ID {$studentSuccess->id}: {$e->getMessage()}");
                Log::error('Failed to generate/send automatic certificate in cron', [
                    'student_success_id' => $studentSuccess->id,
                    'student_id' => $studentSuccess->student_id,
                    'product_id' => $studentSuccess->product_id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        $this->info("Automatic certificate generation completed: {$successCount} successful, {$errorCount} errors.");
        Log::info('Automatic certificate generation cron completed', [
            'success_count' => $successCount,
            'error_count' => $errorCount
        ]);

        return 0;
    }
}
