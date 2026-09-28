<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\Student;
use App\Models\StudentSuccess;
use App\Models\CertifStudent;
use App\Mail\CertificateReady;

class TestCertificateEmail extends Command
{
    protected $signature = 'test:certificate-email {email?}';
    protected $description = 'Test certificate ready email';

    public function handle()
    {
        $testEmail = $this->argument('email') ?: config('mail.test_recipient');
        if (!$testEmail) {
            $this->error('Pass an email argument or set MAIL_TEST_RECIPIENT in .env');
            return 1;
        }
        
        $this->info("Testing Certificate Ready email...");
        $this->info("Sending test email to: {$testEmail}\n");

        // Get test data
        $student = Student::first();
        if (!$student) {
            $this->error('No students found in database!');
            return 1;
        }

        $studentSuccess = StudentSuccess::with('product', 'student')->first();
        if (!$studentSuccess) {
            $this->error('No student successes found in database!');
            return 1;
        }

        $certificate = CertifStudent::where('student_success_id', $studentSuccess->id)->first();
        if (!$certificate) {
            // Create a test certificate
            $certificate = new CertifStudent();
            $certificate->student_success_id = $studentSuccess->id;
            $certificate->serial_number = 'CERT-TEST-' . rand(1000, 9999);
            $certificate->save();
        }

        try {
            Mail::to($testEmail)->send(new CertificateReady($studentSuccess, $certificate));
            $this->info('✓ Certificate Ready email sent successfully!');
            $this->info("\nCheck your email inbox (or Mailtrap) to review the template.");
            return 0;
        } catch (\Exception $e) {
            $this->error('✗ Failed: ' . $e->getMessage());
            return 1;
        }
    }
}
