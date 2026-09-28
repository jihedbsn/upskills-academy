<?php

namespace App\Console\Commands;

use App\Models\CourseRating;
use App\Models\Student;
use App\Models\Product;
use App\Mail\AdminNewRatingNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestRatingEmail extends Command
{
    protected $signature = 'test:rating-email {email?}';
    protected $description = 'Send test rating email to see the template';

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

        $testEmail = $this->argument('email') ?: config('mail.admin_email', env('ADMIN_EMAIL'));

        // Create a mock rating for testing
        $rating = new CourseRating();
        $rating->id = 999;
        $rating->student_id = $student->id;
        $rating->product_id = $product->id;
        $rating->rating = 5;
        $rating->commentaire = "This course was absolutely amazing! The instructor explained everything clearly and the content was well-structured. I learned so much and feel confident in applying these skills in my daily work. The practical examples really helped me understand the concepts better. Highly recommended for anyone looking to master this subject!";
        $rating->is_approved = false;
        $rating->created_at = now();

        // Load relationships
        $rating->setRelation('student', $student);
        $rating->setRelation('product', $product);

        $this->info('Sending test rating email...');

        try {
            Mail::to($testEmail)->send(new AdminNewRatingNotification($rating));
            $this->info("✓ Test rating email sent to: {$testEmail}");
            $this->info('');
            $this->info('Check your email inbox (or Mailtrap) to see the rating notification template!');
        } catch (\Exception $e) {
            $this->error('Failed to send email: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
