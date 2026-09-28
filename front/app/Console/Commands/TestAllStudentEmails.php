<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\Student;
use App\Models\Product;
use App\Models\ProductStudent;
use App\Models\Quiz;
use App\Models\ResultatQuiz;
use App\Models\Ticket;
use App\Models\ResponseTicket;
use App\Models\StudentSuccess;
use App\Models\CertifStudent;
use App\Mail\WelcomeEmail;
use App\Mail\OrderConfirmationEmail;
use App\Mail\CourseEnrollmentEmail;
use App\Mail\CourseExpirationReminder;
use App\Mail\CourseExpired;
use App\Mail\CourseCompletedNotification;
use App\Mail\QuizRetakeOpportunity;
use App\Mail\PasswordResetCodeMail;
use App\Mail\AdminNewCommentNotification;
use App\Mail\AdminNewPurchaseNotification;
use App\Mail\AdminNewRatingNotification;
use App\Mail\AdminNewTicketNotification;
use App\Mail\AdminTicketResponse;
use App\Mail\StudentTicketResponse;
use App\Models\Discussion;
use App\Models\Order;
use App\Models\CourseRating;
use Carbon\Carbon;

class TestAllStudentEmails extends Command
{
    protected $signature = 'test:all-student-emails {email?}';
    protected $description = 'Test all email templates (student and admin notifications)';

    public function handle()
    {
        $testEmail = $this->argument('email') ?: config('mail.test_recipient');
        if (!$testEmail) {
            $this->error('Pass an email argument or set MAIL_TEST_RECIPIENT in .env');
            return 1;
        }
        
        $this->info("Testing all student email templates...");
        $this->info("Sending test emails to: {$testEmail}\n");

        // Get test data
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

        $enrollment = ProductStudent::with(['student', 'product'])->first();
        if (!$enrollment) {
            // Create a temporary enrollment
            $enrollment = new ProductStudent();
            $enrollment->student_id = $student->id;
            $enrollment->product_id = $product->id;
            $enrollment->date = now()->format('Y-m-d');
            $enrollment->expiration_date = now()->addDays(10);
            $enrollment->setRelation('student', $student);
            $enrollment->setRelation('product', $product);
        }

        // Test 1: Welcome Email
        try {
            $this->info('1. Sending Welcome Email...');
            Mail::to($testEmail)->send(new WelcomeEmail($student));
            $this->line("   ✓ Welcome email sent");
            sleep(2); // Avoid rate limiting
        } catch (\Exception $e) {
            $this->error("   ✗ Failed: " . $e->getMessage());
        }

        // Test 2: Order Confirmation Email
        try {
            $this->info('2. Sending Order Confirmation Email...');
            $items = [
                [
                    'type' => 'course',
                    'title' => $product->titre ?? 'Test Course',
                    'quantity' => 1,
                    'price' => 99.99,
                    'total' => 99.99
                ]
            ];
            Mail::to($testEmail)->send(new OrderConfirmationEmail($student, $items, 99.99, now()->format('F d, Y')));
            $this->line("   ✓ Order confirmation email sent");
            sleep(2);
        } catch (\Exception $e) {
            $this->error("   ✗ Failed: " . $e->getMessage());
        }

        // Test 3: Course Enrollment Email
        try {
            $this->info('3. Sending Course Enrollment Email...');
            Mail::to($testEmail)->send(new CourseEnrollmentEmail($product, $student));
            $this->line("   ✓ Course enrollment email sent");
            sleep(2);
        } catch (\Exception $e) {
            $this->error("   ✗ Failed: " . $e->getMessage());
        }

        // Test 4: Course Expiration Reminder
        try {
            $this->info('4. Sending Course Expiration Reminder...');
            Mail::to($testEmail)->send(new CourseExpirationReminder($enrollment, 10));
            $this->line("   ✓ Expiration reminder sent");
            sleep(2);
        } catch (\Exception $e) {
            $this->error("   ✗ Failed: " . $e->getMessage());
        }

        // Test 5: Course Expired
        try {
            $this->info('5. Sending Course Expired Email...');
            Mail::to($testEmail)->send(new CourseExpired($enrollment));
            $this->line("   ✓ Course expired email sent");
            sleep(2);
        } catch (\Exception $e) {
            $this->error("   ✗ Failed: " . $e->getMessage());
        }

        // Test 6: Course Completed Notification
        try {
            $this->info('6. Sending Course Completed Notification...');
            $studentSuccess = new StudentSuccess();
            $studentSuccess->student_id = $student->id;
            $studentSuccess->product_id = $product->id;
            $studentSuccess->success = 1;
            $studentSuccess->created_at = now();
            $studentSuccess->setRelation('student', $student);
            $studentSuccess->setRelation('product', $product);
            
            $certificate = new CertifStudent();
            $certificate->id = 1;
            $certificate->student_id = $student->id;
            $certificate->product_id = $product->id;
            
            Mail::to($testEmail)->send(new CourseCompletedNotification($studentSuccess, $certificate));
            $this->line("   ✓ Course completed notification sent");
            sleep(2);
        } catch (\Exception $e) {
            $this->error("   ✗ Failed: " . $e->getMessage());
        }

        // Test 7: Quiz Retake Opportunity
        try {
            $this->info('7. Sending Quiz Retake Opportunity...');
            $quiz = Quiz::first();
            if ($quiz) {
                Mail::to($testEmail)->send(new QuizRetakeOpportunity($quiz, $student, $product));
                $this->line("   ✓ Quiz retake opportunity email sent");
                sleep(2);
            } else {
                $this->line("   - Skipped (no quiz found)");
            }
        } catch (\Exception $e) {
            $this->error("   ✗ Failed: " . $e->getMessage());
        }

        // Test 8: Password Reset Code
        try {
            $this->info('8. Sending Password Reset Code...');
            Mail::to($testEmail)->send(new PasswordResetCodeMail('123456', $student));
            $this->line("   ✓ Password reset code email sent");
            sleep(2);
        } catch (\Exception $e) {
            $this->error("   ✗ Failed: " . $e->getMessage());
        }

        // Test 9: Admin New Comment Notification
        try {
            $this->info('9. Sending Admin New Comment Notification...');
            $discussion = Discussion::with(['student', 'product'])->first();
            if ($discussion) {
                Mail::to($testEmail)->send(new AdminNewCommentNotification($discussion));
                $this->line("   ✓ Admin new comment notification sent");
            } else {
                $this->line("   - Skipped (no discussion found)");
            }
            sleep(2);
        } catch (\Exception $e) {
            $this->error("   ✗ Failed: " . $e->getMessage());
        }

        // Test 10: Admin New Purchase Notification
        try {
            $this->info('10. Sending Admin New Purchase Notification...');
            $order = Order::with('student')->first();
            if ($order) {
                Mail::to($testEmail)->send(new AdminNewPurchaseNotification($order));
                $this->line("   ✓ Admin new purchase notification sent");
            } else {
                $this->line("   - Skipped (no order found)");
            }
            sleep(2);
        } catch (\Exception $e) {
            $this->error("   ✗ Failed: " . $e->getMessage());
        }

        // Test 11: Admin New Rating Notification
        try {
            $this->info('11. Sending Admin New Rating Notification...');
            $rating = CourseRating::with(['student', 'product'])->first();
            if ($rating) {
                Mail::to($testEmail)->send(new AdminNewRatingNotification($rating));
                $this->line("   ✓ Admin new rating notification sent");
            } else {
                $this->line("   - Skipped (no rating found)");
            }
            sleep(2);
        } catch (\Exception $e) {
            $this->error("   ✗ Failed: " . $e->getMessage());
        }

        // Test 12: Admin New Ticket Notification
        try {
            $this->info('12. Sending Admin New Ticket Notification...');
            $ticket = Ticket::with(['student', 'responses'])->first();
            if ($ticket) {
                Mail::to($testEmail)->send(new AdminNewTicketNotification($ticket));
                $this->line("   ✓ Admin new ticket notification sent");
            } else {
                $this->line("   - Skipped (no ticket found)");
            }
            sleep(2);
        } catch (\Exception $e) {
            $this->error("   ✗ Failed: " . $e->getMessage());
        }

        // Test 13: Admin Ticket Response
        try {
            $this->info('13. Sending Admin Ticket Response...');
            $ticket = Ticket::with('student')->first();
            if ($ticket) {
                Mail::to($testEmail)->send(new AdminTicketResponse($ticket, 'This is a test response from admin.'));
                $this->line("   ✓ Admin ticket response sent");
            } else {
                $this->line("   - Skipped (no ticket found)");
            }
            sleep(2);
        } catch (\Exception $e) {
            $this->error("   ✗ Failed: " . $e->getMessage());
        }

        // Test 14: Student Ticket Response
        try {
            $this->info('14. Sending Student Ticket Response...');
            $ticket = Ticket::with('student')->first();
            if ($ticket) {
                Mail::to($testEmail)->send(new StudentTicketResponse($ticket, 'This is a test response from student.'));
                $this->line("   ✓ Student ticket response sent");
            } else {
                $this->line("   - Skipped (no ticket found)");
            }
        } catch (\Exception $e) {
            $this->error("   ✗ Failed: " . $e->getMessage());
        }

        $this->info("\n✓ All test emails sent successfully!");
        $this->info("Check your email inbox (or Mailtrap) to review the templates.");
        
        return 0;
    }
}
