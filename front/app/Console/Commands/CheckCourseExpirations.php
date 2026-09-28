<?php

namespace App\Console\Commands;

use App\Models\ProductStudent;
use App\Mail\CourseExpired;
use App\Mail\CourseExpirationReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CheckCourseExpirations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'courses:check-expirations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for expired courses and send expiration reminders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking course expirations...');

        $today = Carbon::today();
        $remindersCount = 0;
        $expiredCount = 0;

        // Reminder intervals in days
        $reminderIntervals = [30, 20, 10, 5, 2, 1];

        // Get all active enrollments with expiration dates
        $enrollments = ProductStudent::with(['student', 'product.variations'])
            ->where('is_active', true)
            ->where('is_expired', false)
            ->whereNotNull('expiration_date')
            ->get();

        $this->info("Found {$enrollments->count()} active enrollments with expiration dates");

        foreach ($enrollments as $enrollment) {
            $expirationDate = Carbon::parse($enrollment->expiration_date);
            $daysUntilExpiration = $today->diffInDays($expirationDate, false);

            // Check if course has expired
            if ($daysUntilExpiration < 0) {
                $this->handleExpiredCourse($enrollment);
                $expiredCount++;
                continue;
            }

            // Check if course expires today
            if ($daysUntilExpiration == 0) {
                $this->handleExpiredCourse($enrollment);
                $expiredCount++;
                continue;
            }

            // Check if we need to send a reminder
            if (in_array($daysUntilExpiration, $reminderIntervals)) {
                // Only send if we haven't sent this interval reminder yet
                if ($enrollment->last_reminder_interval != $daysUntilExpiration) {
                    $this->sendExpirationReminder($enrollment, $daysUntilExpiration);
                    $remindersCount++;
                }
            }
        }

        $this->info("Sent {$remindersCount} expiration reminders");
        $this->info("Marked {$expiredCount} courses as expired");
        $this->info('Course expiration check completed!');

        return 0;
    }

    /**
     * Handle expired course
     */
    private function handleExpiredCourse($enrollment)
    {
        // Mark as expired
        $enrollment->update([
            'is_expired' => true,
            'is_active' => false
        ]);

        // Send expired notification
        try {
            Mail::to($enrollment->student->email)->send(new CourseExpired($enrollment));

            $courseName = $enrollment->product->current_variation
                ? $enrollment->product->current_variation->name
                : $enrollment->product->titre;

            $this->info("Sent expiration email to {$enrollment->student->email} for course: {$courseName}");
            Log::info("Course expired notification sent", [
                'student_id' => $enrollment->student_id,
                'product_id' => $enrollment->product_id,
                'expiration_date' => $enrollment->expiration_date
            ]);
        } catch (\Exception $e) {
            $this->error("Failed to send expiration email: {$e->getMessage()}");
            Log::error('Course expiration email error: ' . $e->getMessage());
        }
    }

    /**
     * Send expiration reminder
     */
    private function sendExpirationReminder($enrollment, $daysRemaining)
    {
        try {
            Mail::to($enrollment->student->email)->send(
                new CourseExpirationReminder($enrollment, $daysRemaining)
            );

            // Update reminder tracking
            $enrollment->update([
                'last_expiration_reminder' => now(),
                'last_reminder_interval' => $daysRemaining
            ]);

            $courseName = $enrollment->product->current_variation
                ? $enrollment->product->current_variation->name
                : $enrollment->product->titre;

            $this->info("Sent {$daysRemaining}-day reminder to {$enrollment->student->email} for course: {$courseName}");
            Log::info("Course expiration reminder sent", [
                'student_id' => $enrollment->student_id,
                'product_id' => $enrollment->product_id,
                'days_remaining' => $daysRemaining,
                'expiration_date' => $enrollment->expiration_date
            ]);
        } catch (\Exception $e) {
            $this->error("Failed to send reminder email: {$e->getMessage()}");
            Log::error('Course expiration reminder error: ' . $e->getMessage());
        }
    }
}
