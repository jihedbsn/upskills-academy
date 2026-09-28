<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StudentSuccess;
use App\Models\CertifStudent;
use App\Models\User;
use App\Models\Product;
use App\Mail\CertificateReady;
use Illuminate\Support\Facades\Mail;

class TestSuccessEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:success-email {--student-success-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test course completion success email notification with certificate attachment';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Course Completion Success Email with Certificate...');
        $this->info('---------------------------------------------------');

        // Try to find an actual StudentSuccess with certificate
        $studentSuccessId = $this->option('student-success-id');

        if ($studentSuccessId) {
            $studentSuccess = StudentSuccess::with(['student', 'product', 'certificates'])->find($studentSuccessId);

            if (!$studentSuccess) {
                $this->error("StudentSuccess with ID {$studentSuccessId} not found!");
                return 1;
            }

            if ($studentSuccess->certificates->isEmpty()) {
                $this->error("No certificate found for this StudentSuccess!");
                $this->info("Please use a StudentSuccess that has a generated certificate.");
                return 1;
            }

            $certificate = $studentSuccess->certificates->first();
            $student = $studentSuccess->student;

        } else {
            // Find any StudentSuccess with a certificate
            $studentSuccess = StudentSuccess::whereHas('certificates')
                ->with(['student', 'product', 'certificates'])
                ->where('success', 1)
                ->first();

            if (!$studentSuccess) {
                $this->error("No StudentSuccess with certificate found in database!");
                $this->info("Please generate a certificate for a student first or specify --student-success-id");
                return 1;
            }

            $certificate = $studentSuccess->certificates->first();
            $student = $studentSuccess->student;
        }

        $this->info("Found StudentSuccess:");
        $this->line("  - Student: {$student->first_name} {$student->last_name} ({$student->email})");
        $this->line("  - Course: {$studentSuccess->product->name_en}");
        $this->line("  - Certificate Serial: {$certificate->serial_number}");
        $this->line("  - Certificate File: {$certificate->file_path}");

        // Check if certificate file exists
        $filePath = public_path($certificate->file_path);
        if (!file_exists($filePath)) {
            $this->warn("⚠️  Certificate file not found at: {$filePath}");
            $this->info("Email will be sent but without attachment.");
        } else {
            $fileSize = filesize($filePath);
            $this->info("✓ Certificate file found (" . round($fileSize / 1024, 2) . " KB)");
        }

        $this->newLine();
        $this->info("Sending test email to: {$student->email}");

        try {
            Mail::to($student->email)->send(new CertificateReady($studentSuccess, $certificate));

            $this->newLine();
            $this->info('✓ Success email sent successfully!');
            $this->info("---------------------------------------------------");
            $this->info("Check your email inbox at: {$student->email}");
            $this->info("The certificate should be attached to the email.");

            return 0;
        } catch (\Exception $e) {
            $this->error('✗ Error sending email: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}
