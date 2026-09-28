<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\EvaluationService;
use App\Mail\PendingEvaluationReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SendEvaluationReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'evaluations:send-reminders
                            {--student-id= : Send reminder to specific student ID}
                            {--student-email= : Send reminder to specific student email}
                            {--dry-run : Show who would receive emails without sending}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send evaluation reminder emails to students with pending course evaluations';

    private $evaluationService;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->evaluationService = new EvaluationService();

        $this->info('🔔 Sending Evaluation Reminder Emails');
        $this->info('=====================================');
        $this->newLine();

        // Get students to notify
        $students = $this->getStudentsToNotify();

        if ($students->isEmpty()) {
            $this->info('✓ No students with pending evaluations found.');
            return 0;
        }

        $this->info("Found {$students->count()} student(s) with pending evaluations");
        $this->newLine();

        $sentCount = 0;
        $errorCount = 0;

        foreach ($students as $student) {
            try {
                // Get pending evaluations for this student
                Auth::login($student);
                $pendingCourses = $this->evaluationService->getPendingEvaluations();
                Auth::logout();

                if ($pendingCourses->isEmpty()) {
                    continue;
                }

                $this->line("📧 {$student->email} - {$pendingCourses->count()} pending evaluation(s)");

                if (!$this->option('dry-run')) {
                    Mail::to($student->email)->send(new PendingEvaluationReminder($student, $pendingCourses));
                    $this->info("   ✓ Email sent successfully");
                    $sentCount++;

                    // Update last reminder date
                    $student->update(['last_evaluation_reminder' => now()]);

                    Log::info('Evaluation reminder sent', [
                        'student_id' => $student->id,
                        'student_email' => $student->email,
                        'pending_count' => $pendingCourses->count(),
                    ]);
                } else {
                    $this->info("   → Would send email (dry-run mode)");
                    $sentCount++;
                }

            } catch (\Exception $e) {
                $this->error("   ✗ Failed to send email: {$e->getMessage()}");
                $errorCount++;

                Log::error('Evaluation reminder failed', [
                    'student_id' => $student->id,
                    'student_email' => $student->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->newLine();
        $this->info("📊 Summary:");
        $this->table(
            ['Status', 'Count'],
            [
                ['Emails Sent', $sentCount],
                ['Errors', $errorCount],
                ['Total Processed', $students->count()],
            ]
        );

        if ($this->option('dry-run')) {
            $this->warn('⚠️  This was a dry-run. No emails were actually sent.');
            $this->info('Run without --dry-run to send emails.');
        }

        return 0;
    }

    /**
     * Get students who should receive reminders
     */
    private function getStudentsToNotify()
    {
        // Specific student by ID
        if ($studentId = $this->option('student-id')) {
            $student = User::find($studentId);
            return $student ? collect([$student]) : collect();
        }

        // Specific student by email
        if ($studentEmail = $this->option('student-email')) {
            $student = User::where('email', $studentEmail)->first();
            return $student ? collect([$student]) : collect();
        }

        $today = now();
        $studentsToNotify = collect();

        // Get students who completed courses exactly 3 days ago (first reminder)
        $threeDaysAgo = $today->copy()->subDays(3)->startOfDay();
        $threeDaysAgoEnd = $threeDaysAgo->copy()->endOfDay();

        $recentCompletions = \App\Models\StudentSuccess::where('success', 1)
            ->whereBetween('validated_at', [$threeDaysAgo, $threeDaysAgoEnd])
            ->with('student')
            ->get();

        foreach ($recentCompletions as $completion) {
            if ($completion->student) {
                $studentsToNotify->push($completion->student);
                $this->info("✓ Added student for 3-day reminder: {$completion->student->email}");
            }
        }

        // Get students who need monthly reminders
        // Find students who completed courses and haven't received a reminder in the last 30 days
        $thirtyDaysAgo = $today->copy()->subDays(30);

        $monthlyReminderStudents = \App\Models\StudentSuccess::where('success', 1)
            ->where('validated_at', '<=', $thirtyDaysAgo)
            ->whereDoesntHave('student', function ($query) use ($thirtyDaysAgo) {
                $query->where('last_evaluation_reminder', '>=', $thirtyDaysAgo);
            })
            ->with('student')
            ->get()
            ->unique('student_id');

        foreach ($monthlyReminderStudents as $completion) {
            if ($completion->student && !$studentsToNotify->contains('id', $completion->student->id)) {
                // Check if student still has pending evaluations
                Auth::login($completion->student);
                $pendingCourses = $this->evaluationService->getPendingEvaluations();
                Auth::logout();

                if ($pendingCourses->isNotEmpty()) {
                    $studentsToNotify->push($completion->student);
                    $this->info("✓ Added student for monthly reminder: {$completion->student->email}");
                }
            }
        }

        return $studentsToNotify;
    }
}
