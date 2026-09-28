<?php

namespace App\Console\Commands;

use App\Models\ZoomMeeting;
use App\Mail\ZoomMeetingFollowUp;
use App\Services\OutboundEmailLogger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendZoomFollowUpNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zoom:send-followup-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send follow-up notification emails to students one hour after Zoom meetings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        // Get meetings that ended approximately 1 hour ago (within a 5-minute window)
        // This accounts for meetings that ended between 55 and 65 minutes ago
        $oneHourAgo = $now->copy()->subHour();

        $completedMeetings = ZoomMeeting::where('status', ZoomMeeting::STATUS_SCHEDULED)
            ->where(function($query) use ($oneHourAgo) {
                // Get meetings where start_time + duration ended around 1 hour ago
                $query->whereRaw('DATE_ADD(start_time, INTERVAL duration MINUTE) BETWEEN ? AND ?', [
                    $oneHourAgo->copy()->subMinutes(5)->format('Y-m-d H:i:s'),
                    $oneHourAgo->copy()->addMinutes(5)->format('Y-m-d H:i:s')
                ]);
            })
            ->get();

        if ($completedMeetings->isEmpty()) {
            $this->info('No meetings requiring follow-up notifications at this time.');
            Log::info('Zoom follow-up: No meetings requiring notifications.');
            return 0;
        }

        $sentCount = 0;
        $failedCount = 0;

        foreach ($completedMeetings as $meeting) {
            // Get all enrolled students for this meeting's course
            $students = $meeting->getEnrolledStudents();

            if ($students->isEmpty()) {
                $this->warn("No enrolled students found for meeting: {$meeting->topic}");
                Log::warning("Zoom follow-up: No students enrolled in course for meeting ID {$meeting->id}");
                continue;
            }

            foreach ($students as $student) {
                try {
                    Mail::to($student->email)->send(new ZoomMeetingFollowUp($meeting, $student));

                    OutboundEmailLogger::logSent(
                        $student->email,
                        'zoom_meeting_followup',
                        'Follow-up: ' . $meeting->topic,
                        $student->id,
                        trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')),
                        'ZoomMeeting',
                        $meeting->id
                    );

                    $sentCount++;
                    $this->info("Follow-up email sent to: {$student->email} for meeting: {$meeting->topic}");

                } catch (\Exception $e) {
                    $failedCount++;

                    OutboundEmailLogger::logFailed(
                        $student->email,
                        'zoom_meeting_followup',
                        'Follow-up: ' . $meeting->topic,
                        $e->getMessage(),
                        $student->id,
                        trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')),
                        'ZoomMeeting',
                        $meeting->id
                    );

                    $this->error("Failed to send follow-up email to: {$student->email}");
                    Log::error("Zoom follow-up email failed for student {$student->id}: " . $e->getMessage());
                }
            }

            // Mark meeting as completed after sending follow-ups
            $meeting->markAsCompleted();
            Log::info("Meeting ID {$meeting->id} marked as completed after follow-up notifications.");
        }

        $this->info("\n=== Zoom Follow-Up Notification Summary ===");
        $this->info("Meetings processed: " . $completedMeetings->count());
        $this->info("Emails sent successfully: {$sentCount}");
        $this->info("Emails failed: {$failedCount}");

        Log::info("Zoom follow-up notifications completed: {$sentCount} sent, {$failedCount} failed");

        return 0;
    }
}
