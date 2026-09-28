<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\WelcomeEmail;
use App\Mail\CourseEnrollmentEmail;
use App\Mail\PasswordResetCodeMail;
use App\Mail\OrderConfirmationEmail;
use App\Mail\CourseCompletedNotification;
use App\Mail\AdminNewCommentNotification;
use App\Mail\AdminNewPurchaseNotification;
use App\Mail\AdminNewRatingNotification;
use App\Mail\AdminNewTicketNotification;
use App\Mail\CourseExpirationReminder;
use App\Mail\CourseExpired;
use Illuminate\Support\Facades\Mail;
use App\Models\Discussion;
use App\Models\Order;
use App\Models\CourseRating;
use App\Models\Ticket;
use App\Models\StudentSuccess;
use App\Models\CertifStudent;

class TestAllEmails extends Command
{
    protected $signature = 'email:test-all {email?}';
    protected $description = 'Test all email templates with the new professional design';

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
        $this->info('UPSKILLS ACADEMY EMAIL TESTING SUITE');
        $this->info('====================================');
        $this->line('');

        $testStudent = (object) [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'prenom' => 'John',
            'nom' => 'Doe',
            'email' => $email,
            'country' => 'Sweden',
        ];

        $testCourse = (object) [
            'id' => 1,
            'titre' => 'Introduction to English Grammar',
            'name' => 'English Grammar Course',
            'current_variation' => (object) ['name' => 'English Grammar - Beginner Level']
        ];

        $testOrder = (object) [
            'id' => 12345,
            'price' => 299.00,
            'currency' => 'USD',
            'status' => 'completed',
            'created_at' => now(),
            'student' => $testStudent
        ];

        $testEnrollment = (object) [
            'id' => 1,
            'date' => now()->subMonths(3)->format('F d, Y'),
            'expiration_date' => now()->addDays(7),
            'student' => $testStudent,
            'product' => $testCourse
        ];

        $testExpiredEnrollment = (object) [
            'id' => 2,
            'date' => now()->subMonths(6)->format('F d, Y'),
            'expiration_date' => now()->subDays(3),
            'student' => $testStudent,
            'product' => $testCourse
        ];

        $tests = [
            [
                'name' => 'Welcome Email',
                'mail' => new WelcomeEmail($testStudent),
                'description' => 'Primary blue header, account info, professional greeting'
            ],
            [
                'name' => 'Course Enrollment',
                'mail' => new CourseEnrollmentEmail($testCourse, $testStudent),
                'description' => 'Primary blue header, course details, getting started guide'
            ],
            [
                'name' => 'Password Reset Code',
                'mail' => new PasswordResetCodeMail('123456'),
                'description' => 'Primary blue header, secure verification code display'
            ],
            [
                'name' => 'Order Confirmation',
                'mail' => new OrderConfirmationEmail($testStudent, [
                    ['type' => 'course', 'title' => 'Introduction to English', 'quantity' => 1, 'price' => 199.00],
                    ['type' => 'book', 'title' => 'English Grammar Guide', 'author' => 'Dr. Anna Svensson', 'quantity' => 1, 'price' => 49.00]
                ], 248.00, now()->format('F d, Y')),
                'description' => 'Primary blue header, professional invoice table, payment details'
            ],
            [
                'name' => 'Course Expiration Reminder',
                'mail' => new CourseExpirationReminder($testEnrollment, 7),
                'description' => 'Primary blue header, countdown display, extension option'
            ],
            [
                'name' => 'Course Expired',
                'mail' => new CourseExpired($testExpiredEnrollment),
                'description' => 'Primary blue header, expiration notice, extension purchase'
            ],
            [
                'name' => 'Admin - New Comment',
                'mail' => $this->createTestCommentMail(),
                'description' => 'Primary blue header, comment details, review action'
            ],
            [
                'name' => 'Admin - New Purchase',
                'mail' => $this->createTestPurchaseMail(),
                'description' => 'Primary blue header, order details, customer info'
            ],
            [
                'name' => 'Admin - New Rating',
                'mail' => $this->createTestRatingMail(),
                'description' => 'Primary blue header, rating details, student feedback'
            ],
            [
                'name' => 'Admin - New Ticket',
                'mail' => $this->createTestTicketMail(),
                'description' => 'Primary blue header, support ticket details, student info'
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
                sleep(2); // Rate limiting - increased to avoid Mailtrap limits
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
        $this->line('');

        if ($this->option('verbose')) {
            $this->line('Mail Configuration:');
            $this->line('  Mailer: ' . config('mail.default'));
            $this->line('  Host: ' . config('mail.mailers.smtp.host'));
        }

        return $successCount === count($tests) ? 0 : 1;
    }

    private function createTestCommentMail()
    {
        // Create a mock Discussion model instance
        $discussion = new Discussion();
        $discussion->id = 1;
        $discussion->commentaire = 'This is a test comment about the course content. I found it very helpful and informative.';
        $discussion->created_at = now();
        $discussion->is_approved = false;

        // Mock relationships
        $discussion->setRelation('student', (object) [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com'
        ]);

        $discussion->setRelation('product', (object) [
            'titre' => 'Introduction to English Grammar'
        ]);

        return new AdminNewCommentNotification($discussion);
    }

    private function createTestPurchaseMail()
    {
        // Create a mock Order model instance
        $order = new Order();
        $order->id = 12345;
        $order->price = 299.00;
        $order->currency = 'USD';
        $order->status = 'completed';
        $order->created_at = now();

        // Mock relationship
        $order->setRelation('student', (object) [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com'
        ]);

        return new AdminNewPurchaseNotification($order);
    }

    private function createTestRatingMail()
    {
        // Create a mock CourseRating model instance
        $rating = new CourseRating();
        $rating->id = 1;
        $rating->rating = 5;
        $rating->commentaire = 'Excellent course! I learned so much and the instructor was fantastic.';
        $rating->created_at = now();
        $rating->is_approved = false;

        // Mock relationships
        $rating->setRelation('student', (object) [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com'
        ]);

        $rating->setRelation('product', (object) [
            'titre' => 'Introduction to English Grammar'
        ]);

        return new AdminNewRatingNotification($rating);
    }

    private function createTestTicketMail()
    {
        // Create a mock Ticket model instance
        $ticket = new Ticket();
        $ticket->id = 789;
        $ticket->sujet = 'Unable to access course materials';
        $ticket->ticket_iscomplet = false;
        $ticket->created_at = now();

        // Mock relationships
        $ticket->setRelation('student', (object) [
            'nom' => 'Doe',
            'prenom' => 'John',
            'email' => 'john.doe@example.com'
        ]);

        $ticket->setRelation('responses', collect([
            (object) ['message' => 'I am having trouble accessing the video lessons in Chapter 3. The page keeps loading but nothing appears.']
        ]));

        return new AdminNewTicketNotification($ticket);
    }
}
