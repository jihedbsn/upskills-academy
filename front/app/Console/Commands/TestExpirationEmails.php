<?php

namespace App\Console\Commands;

use App\Models\ProductStudent;
use App\Models\Student;
use App\Models\Product;
use App\Mail\CourseExpired;
use App\Mail\CourseExpirationReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestExpirationEmails extends Command
{
    protected $signature = 'test:expiration-emails {email?}';
    protected $description = 'Send test expiration emails to see the templates';

    public function handle()
    {
        $student = Student::first();

        if (!$student) {
            $this->error('No students found in database!');
            return 1;
        }

        $product = Product::first();

        if (!$product) {
            $this->error('No products found in database!');
            return 1;
        }

        $testEmail = $this->argument('email') ?: $student->email;

        // Create a mock enrollment for testing
        $enrollment = new ProductStudent([
            'product_id' => $product->id,
            'student_id' => $student->id,
            'date' => now()->toDateString(),
            'is_active' => true,
            'access_granted_at' => now(),
            'expiration_date' => now()->addDays(10),
            'is_expired' => false
        ]);

        // Load relationships
        $enrollment->setRelation('student', $student);
        $enrollment->setRelation('product', $product);

        // Test 1: Expiration Reminder (10 days before)
        $this->info('Sending expiration reminder email (10 days before expiration)...');
        try {
            Mail::to($testEmail)->send(new CourseExpirationReminder($enrollment, 10));
            $this->info("✓ Expiration reminder sent to: {$testEmail}");
        } catch (\Exception $e) {
            $this->error('Failed to send reminder: ' . $e->getMessage());
        }

        // Test 2: Course Expired
        $this->info('');
        $this->info('Sending course expired email...');
        $enrollment->expiration_date = now()->subDays(1);
        $enrollment->is_expired = true;

        try {
            Mail::to($testEmail)->send(new CourseExpired($enrollment));
            $this->info("✓ Expired course email sent to: {$testEmail}");
        } catch (\Exception $e) {
            $this->error('Failed to send expired email: ' . $e->getMessage());
        }

        $this->info('');
        $this->info('Check your email inbox (or Mailtrap) to see the templates!');

        return 0;
    }
}
