<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\Student;
use App\Models\Product;
use App\Models\Order;
use App\Models\StudentSuccess;
use App\Models\CertifStudent;
use App\Models\Ticket;
use App\Models\StudentStageCourse;
use App\Models\StudentVideoExam;
use App\Models\ResultatQuiz;
use App\Mail\StudentAccountCreated;
use App\Mail\BirthdayGreeting;
use App\Mail\CertificateReady;
use App\Mail\QuizPassed;
use App\Mail\StageValidated;
use App\Mail\StageRejected;
use App\Mail\StudentCourseEnrollment;
use App\Mail\StudentSuccessApproved;
use App\Mail\StudentSuccessRejected;
use App\Mail\VideoExamValidated;
use App\Mail\VideoExamRejected;
use App\Mail\TicketStatusChanged;
use App\Mail\AdminNewPurchase;
use App\Mail\AdminTicketResponse;
use Carbon\Carbon;

class TestAllAdminEmails extends Command
{
    protected $signature = 'test:all-admin-emails {email?}';
    protected $description = 'Test all admin email templates';

    public function handle()
    {
        $testEmail = $this->argument('email') ?: config('mail.test_recipient');
        if (!$testEmail) {
            $this->error('Pass an email argument or set MAIL_TEST_RECIPIENT in .env');
            return 1;
        }
        
        $this->info("Testing all admin email templates...");
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

        // Test 1: Student Account Created
        try {
            $this->info('1. Sending Student Account Created...');
            Mail::to($testEmail)->send(new StudentAccountCreated($student, $product, 'testpassword123'));
            $this->line("   ✓ Student account created email sent");
            sleep(2);
        } catch (\Exception $e) {
            $this->error("   ✗ Failed: " . $e->getMessage());
        }

        // Test 2: Birthday Greeting
        try {
            $this->info('2. Sending Birthday Greeting...');
            Mail::to($testEmail)->send(new BirthdayGreeting($student));
            $this->line("   ✓ Birthday greeting sent");
            sleep(2);
        } catch (\Exception $e) {
            $this->error("   ✗ Failed: " . $e->getMessage());
        }

        // Test 3: Certificate Ready
        try {
            $this->info('3. Sending Certificate Ready...');
            $studentSuccess = StudentSuccess::with(['student', 'product'])->first();
            if (!$studentSuccess) {
                $studentSuccess = new StudentSuccess();
                $studentSuccess->student_id = $student->id;
                $studentSuccess->product_id = $product->id;
                $studentSuccess->success = 1;
                $studentSuccess->setRelation('student', $student);
                $studentSuccess->setRelation('product', $product);
            }
            
            $certificate = CertifStudent::first();
            if (!$certificate) {
                $certificate = new CertifStudent();
                $certificate->id = 1;
                $certificate->student_id = $student->id;
                $certificate->product_id = $product->id;
                $certificate->serial_number = 'TEST-123456';
            }
            
            Mail::to($testEmail)->send(new CertificateReady($studentSuccess, $certificate));
            $this->line("   ✓ Certificate ready email sent");
            sleep(2);
        } catch (\Exception $e) {
            $this->error("   ✗ Failed: " . $e->getMessage());
        }

        // Test 4: Quiz Passed
        try {
            $this->info('4. Sending Quiz Passed...');
            $resultatQuiz = ResultatQuiz::with(['student', 'quiz', 'product'])->first();
            if ($resultatQuiz) {
                Mail::to($testEmail)->send(new QuizPassed($resultatQuiz));
                $this->line("   ✓ Quiz passed email sent");
            } else {
                $this->line("   - Skipped (no quiz result found)");
            }
            sleep(2);
        } catch (\Exception $e) {
            $this->error("   ✗ Failed: " . $e->getMessage());
        }

        // Test 5: Student Course Enrollment
        try {
            $this->info('5. Sending Student Course Enrollment...');
            Mail::to($testEmail)->send(new StudentCourseEnrollment($student, $product));
            $this->line("   ✓ Course enrollment email sent");
            sleep(2);
        } catch (\Exception $e) {
            $this->error("   ✗ Failed: " . $e->getMessage());
        }

        // Test 6: Stage Validated
        try {
            $this->info('6. Sending Stage Validated...');
            $studentStageCourse = StudentStageCourse::with(['student', 'product'])->first();
            if ($studentStageCourse) {
                Mail::to($testEmail)->send(new StageValidated($studentStageCourse));
                $this->line("   ✓ Stage validated email sent");
            } else {
                $this->line("   - Skipped (no stage course found)");
            }
            sleep(2);
        } catch (\Exception $e) {
            $this->error("   ✗ Failed: " . $e->getMessage());
        }

        // Test 7: Stage Rejected
        try {
            $this->info('7. Sending Stage Rejected...');
            $studentStageCourse = StudentStageCourse::with(['student', 'product'])->first();
            if ($studentStageCourse) {
                Mail::to($testEmail)->send(new StageRejected($studentStageCourse));
                $this->line("   ✓ Stage rejected email sent");
            } else {
                $this->line("   - Skipped (no stage course found)");
            }
            sleep(2);
        } catch (\Exception $e) {
            $this->error("   ✗ Failed: " . $e->getMessage());
        }

        // Test 8: Student Success Approved
        try {
            $this->info('8. Sending Student Success Approved...');
            $studentSuccess = StudentSuccess::with(['student', 'product'])->first();
            if (!$studentSuccess) {
                $studentSuccess = new StudentSuccess();
                $studentSuccess->student_id = $student->id;
                $studentSuccess->product_id = $product->id;
                $studentSuccess->success = 1;
                $studentSuccess->setRelation('student', $student);
                $studentSuccess->setRelation('product', $product);
            }
            
            Mail::to($testEmail)->send(new StudentSuccessApproved($studentSuccess));
            $this->line("   ✓ Student success approved email sent");
            sleep(2);
        } catch (\Exception $e) {
            $this->error("   ✗ Failed: " . $e->getMessage());
        }

        // Test 9: Student Success Rejected
        try {
            $this->info('9. Sending Student Success Rejected...');
            $studentSuccess = StudentSuccess::with(['student', 'product'])->first();
            if (!$studentSuccess) {
                $studentSuccess = new StudentSuccess();
                $studentSuccess->student_id = $student->id;
                $studentSuccess->product_id = $product->id;
                $studentSuccess->success = 0;
                $studentSuccess->setRelation('student', $student);
                $studentSuccess->setRelation('product', $product);
            }
            
            Mail::to($testEmail)->send(new StudentSuccessRejected($studentSuccess));
            $this->line("   ✓ Student success rejected email sent");
            sleep(2);
        } catch (\Exception $e) {
            $this->error("   ✗ Failed: " . $e->getMessage());
        }

        // Test 10: Video Exam Validated
        try {
            $this->info('10. Sending Video Exam Validated...');
            $studentVideoExam = StudentVideoExam::with(['student', 'product'])->first();
            if ($studentVideoExam) {
                Mail::to($testEmail)->send(new VideoExamValidated($studentVideoExam));
                $this->line("   ✓ Video exam validated email sent");
            } else {
                $this->line("   - Skipped (no video exam found)");
            }
            sleep(2);
        } catch (\Exception $e) {
            $this->error("   ✗ Failed: " . $e->getMessage());
        }

        // Test 11: Video Exam Rejected
        try {
            $this->info('11. Sending Video Exam Rejected...');
            $studentVideoExam = StudentVideoExam::with(['student', 'product'])->first();
            if ($studentVideoExam) {
                Mail::to($testEmail)->send(new VideoExamRejected($studentVideoExam));
                $this->line("   ✓ Video exam rejected email sent");
            } else {
                $this->line("   - Skipped (no video exam found)");
            }
            sleep(2);
        } catch (\Exception $e) {
            $this->error("   ✗ Failed: " . $e->getMessage());
        }

        // Test 12: Ticket Status Changed
        try {
            $this->info('12. Sending Ticket Status Changed...');
            $ticket = Ticket::with('student')->first();
            if ($ticket) {
                Mail::to($testEmail)->send(new TicketStatusChanged($ticket));
                $this->line("   ✓ Ticket status changed email sent");
            } else {
                $this->line("   - Skipped (no ticket found)");
            }
            sleep(2);
        } catch (\Exception $e) {
            $this->error("   ✗ Failed: " . $e->getMessage());
        }

        // Test 13: Admin New Purchase
        try {
            $this->info('13. Sending Admin New Purchase...');
            $order = Order::with('student')->first();
            if ($order) {
                Mail::to($testEmail)->send(new AdminNewPurchase($order));
                $this->line("   ✓ Admin new purchase email sent");
            } else {
                $this->line("   - Skipped (no order found)");
            }
            sleep(2);
        } catch (\Exception $e) {
            $this->error("   ✗ Failed: " . $e->getMessage());
        }

        // Test 14: Admin Ticket Response
        try {
            $this->info('14. Sending Admin Ticket Response...');
            $ticket = Ticket::with('student')->first();
            if ($ticket) {
                Mail::to($testEmail)->send(new AdminTicketResponse($ticket, 'This is a test response from admin.'));
                $this->line("   ✓ Admin ticket response email sent");
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
