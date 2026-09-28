<?php

namespace App\Jobs;

use App\Models\CourseSession;
use App\Models\Student;
use App\Mail\CourseSessionScheduled;
use App\Mail\CourseSessionUpdated;
use App\Mail\CourseSessionCancelled;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\EmailLog;

class SendCourseSessionNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $sessionData;
    protected $students;
    protected $action; // 'scheduled', 'updated', 'cancelled'

    /**
     * Create a new job instance.
     */
    public function __construct($sessionOrData, $students, string $action = 'scheduled')
    {
        // If it's a model, convert to array to avoid serialization issues when deleted
        if ($sessionOrData instanceof CourseSession) {
            $this->sessionData = [
                'id' => $sessionOrData->id,
                'product_id' => $sessionOrData->product_id,
                'title' => $sessionOrData->title,
                'description' => $sessionOrData->description,
                'session_date' => $sessionOrData->session_date,
                'start_time' => $sessionOrData->start_time,
                'end_time' => $sessionOrData->end_time,
                'session_type' => $sessionOrData->session_type,
                'instructor_name' => $sessionOrData->instructor_name,
                'location' => $sessionOrData->location,
                'zoom_meeting_id' => $sessionOrData->zoom_meeting_id,
                'zoom_join_url' => $sessionOrData->zoom_join_url,
                'status' => $sessionOrData->status,
                'notes' => $sessionOrData->notes,
                'formatted_date' => $sessionOrData->formatted_date,
                'formatted_time' => $sessionOrData->formatted_time,
                'product' => ($sessionOrData->product && isset($sessionOrData->product->id)) ? [
                    'id' => $sessionOrData->product->id,
                    'titre' => $sessionOrData->product->titre ?? 'Course',
                ] : null,
            ];
        } else {
            $this->sessionData = $sessionOrData;
        }

        $this->students = $students;
        $this->action = $action;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Starting course session notification job for session {$this->sessionData['id']}, action: {$this->action}");

        $successCount = 0;
        $failCount = 0;

        // Create a temporary session object for the email with helper methods
        $session = new class($this->sessionData) {
            private $data;

            public function __construct($data) {
                $this->data = $data;
            }

            public function __get($key) {
                return $this->data[$key] ?? null;
            }

            public function __isset($key) {
                return isset($this->data[$key]);
            }

            public function getTypeLabel() {
                return match($this->data['session_type'] ?? '') {
                    'theory' => 'Theory',
                    'practical' => 'Practical',
                    'online' => 'Online',
                    'classroom' => 'Classroom',
                    default => 'Unknown',
                };
            }

            public function getStatusLabel() {
                return match($this->data['status'] ?? '') {
                    'scheduled' => 'Scheduled',
                    'ongoing' => 'Ongoing',
                    'completed' => 'Completed',
                    'cancelled' => 'Cancelled',
                    default => 'Unknown',
                };
            }
        };

        if (isset($this->sessionData['product'])) {
            $session->product = (object) $this->sessionData['product'];
        }

        foreach ($this->students as $student) {
            try {
                $mailable = match($this->action) {
                    'scheduled' => new CourseSessionScheduled($session, $student),
                    'updated' => new CourseSessionUpdated($session, $student),
                    'cancelled' => new CourseSessionCancelled($session, $student),
                    default => new CourseSessionScheduled($session, $student),
                };

                Mail::to($student->email)->send($mailable);
                $successCount++;

                EmailLog::logSent(
                    $student->email,
                    'course_session_' . $this->action,
                    'Course Session: ' . ($this->sessionData['title'] ?? 'N/A'),
                    $student->id ?? null,
                    ($student->first_name ?? '') . ' ' . ($student->last_name ?? ''),
                    'CourseSession',
                    $this->sessionData['id'] ?? null
                );
            } catch (\Exception $e) {
                $failCount++;
                Log::error("Failed to send course session email to {$student->email}: {$e->getMessage()}");

                EmailLog::logFailed(
                    $student->email,
                    'course_session_' . $this->action,
                    'Course Session: ' . ($this->sessionData['title'] ?? 'N/A'),
                    $e->getMessage(),
                    $student->id ?? null,
                    ($student->first_name ?? '') . ' ' . ($student->last_name ?? ''),
                    'CourseSession',
                    $this->sessionData['id'] ?? null
                );
            }
        }

        Log::info("Course session notification job completed. Success: {$successCount}, Failed: {$failCount}");
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $sessionId = isset($this->sessionData['id']) ? $this->sessionData['id'] : 'unknown';
        Log::error("Course session notification job failed for session {$sessionId}: {$exception->getMessage()}");
    }
}
