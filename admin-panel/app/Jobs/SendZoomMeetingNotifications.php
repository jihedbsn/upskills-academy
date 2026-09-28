<?php

namespace App\Jobs;

use App\Models\ZoomMeeting;
use App\Models\Student;
use App\Mail\ZoomMeetingScheduled;
use App\Mail\ZoomMeetingUpdated;
use App\Mail\ZoomMeetingCancelled;
use App\Mail\ZoomMeetingFollowUp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\EmailLog;

class SendZoomMeetingNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $meetingData;
    protected $students;
    protected $action; // 'created', 'updated', 'cancelled'

    /**
     * Create a new job instance.
     */
    public function __construct($meetingOrData, $students, string $action = 'created')
    {
        // If it's a model, convert to array to avoid serialization issues when deleted
        if ($meetingOrData instanceof ZoomMeeting) {
            $this->meetingData = [
                'id' => $meetingOrData->id,
                'product_id' => $meetingOrData->product_id,
                'zoom_meeting_id' => $meetingOrData->zoom_meeting_id,
                'topic' => $meetingOrData->topic,
                'start_time' => $meetingOrData->start_time,
                'duration' => $meetingOrData->duration,
                'timezone' => $meetingOrData->timezone,
                'password' => $meetingOrData->password,
                'join_url' => $meetingOrData->join_url,
                'start_url' => $meetingOrData->start_url,
                'recording_url' => $meetingOrData->recording_url,
                'moderator_email' => $meetingOrData->moderator_email,
                'agenda' => $meetingOrData->agenda,
                'status' => $meetingOrData->status,
                'formatted_date' => $meetingOrData->formatted_date ?? null,
                'formatted_time' => $meetingOrData->formatted_time ?? null,
                'product' => ($meetingOrData->product && isset($meetingOrData->product->id)) ? [
                    'id' => $meetingOrData->product->id,
                    'titre' => $meetingOrData->product->titre ?? 'Course',
                ] : null,
            ];
        } else {
            $this->meetingData = $meetingOrData;
        }

        $this->students = $students;
        $this->action = $action;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Starting Zoom meeting notification job for meeting {$this->meetingData['id']}, action: {$this->action}");

        $successCount = 0;
        $failCount = 0;

        // Create a temporary meeting object for the email
        $meeting = (object) $this->meetingData;
        if (isset($this->meetingData['product'])) {
            $meeting->product = (object) $this->meetingData['product'];
        }

        foreach ($this->students as $student) {
            try {
                $mailable = match($this->action) {
                    'created' => new ZoomMeetingScheduled($meeting, $student),
                    'updated' => new ZoomMeetingUpdated($meeting, $student),
                    'cancelled', 'deleted' => new ZoomMeetingCancelled($meeting, $student),
                    'recording' => new ZoomMeetingFollowUp($meeting, $student),
                    default => new ZoomMeetingScheduled($meeting, $student),
                };

                Mail::to($student->email)->send($mailable);
                $successCount++;

                EmailLog::logSent(
                    $student->email,
                    'zoom_meeting_' . $this->action,
                    'Zoom Meeting: ' . ($this->meetingData['topic'] ?? 'N/A'),
                    $student->id ?? null,
                    ($student->first_name ?? '') . ' ' . ($student->last_name ?? ''),
                    'ZoomMeeting',
                    $this->meetingData['id'] ?? null
                );
            } catch (\Exception $e) {
                $failCount++;
                Log::error("Failed to send Zoom meeting email to {$student->email}: {$e->getMessage()}");

                EmailLog::logFailed(
                    $student->email,
                    'zoom_meeting_' . $this->action,
                    'Zoom Meeting: ' . ($this->meetingData['topic'] ?? 'N/A'),
                    $e->getMessage(),
                    $student->id ?? null,
                    ($student->first_name ?? '') . ' ' . ($student->last_name ?? ''),
                    'ZoomMeeting',
                    $this->meetingData['id'] ?? null
                );
            }
        }

        Log::info("Zoom meeting notification job completed. Success: {$successCount}, Failed: {$failCount}");
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $meetingId = isset($this->meetingData['id']) ? $this->meetingData['id'] : 'unknown';
        Log::error("Zoom meeting notification job failed for meeting {$meetingId}: {$exception->getMessage()}");
    }
}
