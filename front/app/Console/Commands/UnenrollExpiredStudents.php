<?php

namespace App\Console\Commands;

use App\Models\ProductStudent;
use App\Models\CertifStudent;
use App\Models\StudentSuccess;
use App\Models\CourseExtensionOrder;
use App\Mail\CourseUnenrollment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UnenrollExpiredStudents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'courses:unenroll-expired {--dry-run : Run without actually unenrolling}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Unenroll students from expired courses after 30 days without renewal payment (only for duration-limited courses without certificates)';

    /**
     * Grace period in days after expiration before unenrollment.
     */
    const GRACE_PERIOD_DAYS = 30;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $this->info('Checking for students to unenroll from expired courses...');

        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No changes will be made.');
        }

        $cutoffDate = Carbon::now()->subDays(self::GRACE_PERIOD_DAYS);
        $unenrolledCount = 0;
        $skippedCount = 0;

        // Find enrollments that:
        // 1. Are expired (is_expired = true)
        // 2. Expired more than 30 days ago
        // 3. Course has duration limit (validity_months is set)
        $expiredEnrollments = ProductStudent::with(['student', 'product.variations'])
            ->where('is_expired', true)
            ->whereNotNull('expiration_date')
            ->where('expiration_date', '<=', $cutoffDate)
            ->get();

        $this->info("Found {$expiredEnrollments->count()} enrollments expired for more than " . self::GRACE_PERIOD_DAYS . " days.");

        foreach ($expiredEnrollments as $enrollment) {
            $student = $enrollment->student;
            $product = $enrollment->product;

            if (!$student || !$product) {
                $this->warn("Skipping enrollment #{$enrollment->id}: Missing student or product.");
                $skippedCount++;
                continue;
            }

            // Only process courses with duration limits
            if (!$product->validity_months) {
                $skippedCount++;
                continue;
            }

            $courseName = $product->current_variation
                ? $product->current_variation->name
                : $product->titre;

            // Check if student has a certificate for this course
            $hasCertificate = CertifStudent::where('student_id', $enrollment->student_id)
                ->where('product_id', $enrollment->product_id)
                ->where('is_valid', true)
                ->exists();

            if ($hasCertificate) {
                $this->line("  Skipping: {$student->email} - '{$courseName}' (certificate issued)");
                $skippedCount++;
                continue;
            }

            // Check if student has completed/passed the course (success validated)
            $hasCompleted = StudentSuccess::where('student_id', $enrollment->student_id)
                ->where('product_id', $enrollment->product_id)
                ->where('success', 1)
                ->exists();

            if ($hasCompleted) {
                $this->line("  Skipping: {$student->email} - '{$courseName}' (course completed)");
                $skippedCount++;
                continue;
            }

            // Check if there's an approved/pending extension order (student paid to renew)
            $hasRenewal = CourseExtensionOrder::where('student_id', $enrollment->student_id)
                ->where('product_id', $enrollment->product_id)
                ->whereIn('payment_status', ['pending', 'approved'])
                ->where('created_at', '>=', $enrollment->expiration_date)
                ->exists();

            if ($hasRenewal) {
                $this->line("  Skipping: {$student->email} - '{$courseName}' (renewal payment exists)");
                $skippedCount++;
                continue;
            }

            // All checks passed - unenroll the student
            $this->info("  Unenrolling: {$student->email} from '{$courseName}' (expired {$enrollment->expiration_date->format('Y-m-d')})");

            if (!$isDryRun) {
                try {
                    DB::beginTransaction();

                    // Remove the enrollment
                    $enrollment->delete();

                    DB::commit();

                    // Send unenrollment notification email
                    $this->sendUnenrollmentEmail($student, $product, $enrollment, $courseName);

                    $unenrolledCount++;

                    Log::info('Student unenrolled from expired course', [
                        'student_id' => $student->id,
                        'student_email' => $student->email,
                        'product_id' => $product->id,
                        'course_name' => $courseName,
                        'expiration_date' => $enrollment->expiration_date->format('Y-m-d'),
                        'days_since_expiry' => Carbon::now()->diffInDays($enrollment->expiration_date),
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->error("  Failed to unenroll: {$e->getMessage()}");
                    Log::error('Failed to unenroll expired student', [
                        'student_id' => $student->id,
                        'product_id' => $product->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            } else {
                $unenrolledCount++;
            }
        }

        $this->newLine();
        $this->info("=== Summary ===");
        $this->info("Unenrolled: {$unenrolledCount}");
        $this->info("Skipped: {$skippedCount} (completed/certified/renewed)");

        if ($isDryRun && $unenrolledCount > 0) {
            $this->warn("Run without --dry-run to apply changes.");
        }

        $this->info('Unenrollment check completed!');

        return 0;
    }

    /**
     * Send unenrollment notification email to student
     */
    private function sendUnenrollmentEmail($student, $product, $enrollment, $courseName)
    {
        try {
            Mail::to($student->email)->send(
                new CourseUnenrollment($student, $product, $enrollment)
            );

            $this->info("  ✓ Unenrollment email sent to {$student->email}");
            Log::info('Unenrollment email sent', [
                'student_id' => $student->id,
                'student_email' => $student->email,
                'course_name' => $courseName,
            ]);
        } catch (\Exception $e) {
            $this->error("  ✗ Failed to send email to {$student->email}: {$e->getMessage()}");
            Log::error('Unenrollment email failed', [
                'student_id' => $student->id,
                'student_email' => $student->email,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
