<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\StudentAccountCreated;
use App\Mail\StudentCourseEnrollment;
use App\Mail\BirthdayGreeting;
use App\Mail\CertificateReady;
use App\Mail\QuizPassed;
use App\Mail\StageValidated;
use App\Mail\StageRejected;
use App\Mail\StudentSuccessApproved;
use App\Mail\StudentSuccessRejected;
use App\Mail\VideoExamValidated;
use App\Mail\VideoExamRejected;
use App\Mail\AdminNewPurchase;
use App\Mail\ScheduledTask;
use Illuminate\Support\Facades\Mail;

class TestAllEmails extends Command
{
    protected $signature = 'email:test-all {email?}';
    protected $description = 'Test all admin email templates with professional design';

    public function handle()
    {
        $email = $this->argument('email');

        if (!$email) {
            $email = $this->ask('Enter the email address to send tests to');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email address!');
            return 1;
        }

        $this->info('====================================');
        $this->info('UPSKILLS ACADEMY ADMIN EMAIL TESTING');
        $this->info('====================================');
        $this->line('');

        $tests = [
            [
                'name' => 'Student Account Created',
                'mail' => new StudentAccountCreated([
                    'student_name' => 'John Doe',
                    'student_email' => $email,
                    'student_password' => 'TempPass123!',
                    'course_name' => 'IELTS Exam Preparation',
                    'login_url' => 'https://upskillsacademy.com/login',
                    'website_url' => 'https://upskillsacademy.com'
                ]),
                'description' => 'Account creation with login credentials'
            ],
            [
                'name' => 'Student Course Enrollment',
                'mail' => new StudentCourseEnrollment([
                    'student_name' => 'John Doe',
                    'course_name' => 'IELTS Exam Preparation',
                    'enrollment_date' => now()->format('F d, Y'),
                    'course_url' => 'https://upskillsacademy.com/courses/1',
                    'dashboard_url' => 'https://upskillsacademy.com/dashboard'
                ]),
                'description' => 'Course enrollment confirmation'
            ],
            [
                'name' => 'Birthday Greeting',
                'mail' => new BirthdayGreeting([
                    'student_name' => 'John Doe',
                    'member_since' => 'January 2024',
                    'courses_completed' => 3,
                    'learning_hours' => 120,
                    'special_offer' => '20% discount on your next course purchase!'
                ]),
                'description' => 'Birthday wishes to student'
            ],
            [
                'name' => 'Certificate Ready',
                'mail' => new CertificateReady([
                    'student_name' => 'John Doe',
                    'course_name' => 'IELTS Exam Preparation',
                    'completion_date' => now()->format('F d, Y'),
                    'certificate_number' => 'CERT-2024-001234',
                    'final_score' => 95,
                    'certificate_url' => 'https://upskillsacademy.com/certificates/download/123',
                    'catalog_url' => 'https://upskillsacademy.com/courses'
                ]),
                'description' => 'Certificate download notification'
            ],
            [
                'name' => 'Quiz Passed',
                'mail' => new QuizPassed([
                    'student_name' => 'John Doe',
                    'course_name' => 'IELTS Exam Preparation',
                    'quiz_name' => 'Final Assessment',
                    'quizType' => 'Final Exam',
                    'score' => 92,
                    'completion_date' => now()->format('F d, Y'),
                    'admin_notes' => 'Excellent performance! Keep up the great work.'
                ]),
                'description' => 'Quiz passed notification'
            ],
            [
                'name' => 'Stage Validated',
                'mail' => new StageValidated([
                    'student_name' => 'John Doe',
                    'course_name' => 'IELTS Exam Preparation',
                    'stage_name' => 'Practical Training Module',
                    'validation_date' => now()->format('F d, Y'),
                    'admin_notes' => 'Your practical skills demonstration was excellent.',
                    'next_steps' => 'Continue to the final assessment module.'
                ]),
                'description' => 'Internship stage validation'
            ],
            [
                'name' => 'Stage Rejected',
                'mail' => new StageRejected([
                    'student_name' => 'John Doe',
                    'course_name' => 'IELTS Exam Preparation',
                    'stage_name' => 'Practical Training Module',
                    'rejection_date' => now()->format('F d, Y'),
                    'admin_notes' => 'Please review the requirements and resubmit your work.',
                    'improvements_needed' => 'Focus on technique and documentation quality.'
                ]),
                'description' => 'Internship stage rejection with feedback'
            ],
            [
                'name' => 'Success Approved',
                'mail' => new StudentSuccessApproved([
                    'student_name' => 'John Doe',
                    'course_name' => 'IELTS Exam Preparation',
                    'approval_date' => now()->format('F d, Y'),
                    'final_score' => 95,
                    'admin_notes' => 'Congratulations on your outstanding achievement!'
                ]),
                'description' => 'Final success approval notification'
            ],
            [
                'name' => 'Success Rejected',
                'mail' => new StudentSuccessRejected([
                    'student_name' => 'John Doe',
                    'course_name' => 'IELTS Exam Preparation',
                    'rejection_date' => now()->format('F d, Y'),
                    'admin_notes' => 'Additional requirements needed before final approval.',
                    'requirements' => 'Complete missing assignments and resubmit practical demonstration.'
                ]),
                'description' => 'Success rejection with requirements'
            ],
            [
                'name' => 'Video Exam Validated',
                'mail' => new VideoExamValidated([
                    'student_name' => 'John Doe',
                    'course_name' => 'IELTS Exam Preparation',
                    'exam_name' => 'Practical Demonstration Video',
                    'validation_date' => now()->format('F d, Y'),
                    'score' => 88,
                    'admin_notes' => 'Your video demonstration shows good understanding of the concepts.'
                ]),
                'description' => 'Video exam validation'
            ],
            [
                'name' => 'Video Exam Rejected',
                'mail' => new VideoExamRejected([
                    'student_name' => 'John Doe',
                    'course_name' => 'IELTS Exam Preparation',
                    'exam_name' => 'Practical Demonstration Video',
                    'rejection_date' => now()->format('F d, Y'),
                    'admin_notes' => 'Please rerecord your video with better lighting and audio quality.',
                    'improvements_needed' => 'Ensure all required elements are clearly visible and explained.'
                ]),
                'description' => 'Video exam rejection with feedback'
            ],
            [
                'name' => 'Admin - New Purchase',
                'mail' => $this->createTestPurchaseMail(),
                'description' => 'Admin notification for new purchase'
            ],
            [
                'name' => 'Scheduled Task',
                'mail' => new ScheduledTask([
                    'student_name' => 'John Doe',
                    'course_name' => 'IELTS Exam Preparation',
                    'task_message' => 'Complete Chapter 5 Quiz',
                    'task_date' => now()->addDay()->format('F d, Y'),
                    'task_time' => '14:00',
                    'timing' => 'tomorrow'
                ]),
                'description' => 'Task reminder notification'
            ]
        ];

        $testCount = count($tests);
        $this->info("Testing {$testCount} email template(s)...");
        $this->line('');

        $successCount = 0;
        $failCount = 0;

        foreach ($tests as $index => $test) {
            $num = $index + 1;
            $testName = $test['name'];
            $this->line("[{$num}/{$testCount}] Testing: {$testName}");

            try {
                Mail::to($email)->send($test['mail']);
                $this->info('  ✓ Sent successfully');
                $testDesc = $test['description'];
                $this->line("  → {$testDesc}");
                $successCount++;
            } catch (\Exception $e) {
                $this->error('  ✗ Failed: ' . $e->getMessage());
                $failCount++;
            }

            $this->line('');

            if ($num < $testCount) {
                sleep(2);
            }
        }

        $this->info('====================================');
        $this->info('TEST SUMMARY');
        $this->info('====================================');
        $this->line("Successful: {$successCount}");
        if ($failCount > 0) {
            $this->error("Failed: {$failCount}");
        }
        $this->line('');

        $this->line('Check your inbox (' . $email . ') to verify:');
        $this->line('  • Professional header design with no emojis');
        $this->line('  • Consistent Upskills Academy branding');
        $this->line('  • Clean info cards with proper colors');
        $this->line('  • Professional footer with links');
        $this->line('  • Mobile-responsive layout');

        return $successCount === count($tests) ? 0 : 1;
    }

    private function createTestPurchaseMail()
    {
        $order = (object) [
            'id' => 12345,
            'created_at' => now()
        ];

        $student = (object) [
            'phone' => '+46 70 123 4567',
            'country' => 'Sweden'
        ];

        $orderDetails = [
            'order_id' => '12345',
            'order_date' => now()->format('F d, Y H:i'),
            'student_name' => 'John Doe',
            'student_email' => 'john.doe@example.com',
            'total_amount' => '299.00',
            'currency' => 'SEK',
            'payment_status' => 'Completed'
        ];

        return new AdminNewPurchase($order, $student, $orderDetails);
    }
}
