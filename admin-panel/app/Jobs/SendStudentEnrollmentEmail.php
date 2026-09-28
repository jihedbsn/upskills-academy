<?php

namespace App\Jobs;

use App\Models\Student;
use App\Models\Product;
use App\Mail\StudentAccountCreated;
use App\Mail\StudentCourseEnrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\EmailLog;

class SendStudentEnrollmentEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $student;
    protected $product;
    protected $emailType; // 'account_created' or 'course_enrollment'
    protected $password;

    /**
     * Create a new job instance.
     */
    public function __construct(Student $student, Product $product, string $emailType, ?string $password = null)
    {
        $this->student = $student;
        $this->product = $product;
        $this->emailType = $emailType;
        $this->password = $password;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            if ($this->emailType === 'account_created' && $this->password) {
                Mail::to($this->student->email)->send(
                    new StudentAccountCreated($this->student, $this->product, $this->password)
                );
                Log::info("Account created email sent to {$this->student->email}");

                EmailLog::logSent(
                    $this->student->email,
                    'welcome',
                    'Account Created: ' . $this->product->titre,
                    $this->student->id,
                    ($this->student->first_name ?? '') . ' ' . ($this->student->last_name ?? ''),
                    'Product',
                    $this->product->id
                );
            } elseif ($this->emailType === 'course_enrollment') {
                Mail::to($this->student->email)->send(
                    new StudentCourseEnrollment($this->student, $this->product)
                );
                Log::info("Course enrollment email sent to {$this->student->email}");

                EmailLog::logSent(
                    $this->student->email,
                    'student_enrollment',
                    'Course Enrollment: ' . $this->product->titre,
                    $this->student->id,
                    ($this->student->first_name ?? '') . ' ' . ($this->student->last_name ?? ''),
                    'Product',
                    $this->product->id
                );
            }
        } catch (\Exception $e) {
            Log::error("Failed to send {$this->emailType} email to {$this->student->email}: {$e->getMessage()}");

            EmailLog::logFailed(
                $this->student->email,
                $this->emailType === 'account_created' ? 'welcome' : 'student_enrollment',
                ($this->emailType === 'account_created' ? 'Account Created: ' : 'Course Enrollment: ') . $this->product->titre,
                $e->getMessage(),
                $this->student->id,
                ($this->student->first_name ?? '') . ' ' . ($this->student->last_name ?? ''),
                'Product',
                $this->product->id
            );

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Student enrollment email job failed for {$this->student->email}: {$exception->getMessage()}");
    }
}
