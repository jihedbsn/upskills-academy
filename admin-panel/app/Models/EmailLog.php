<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'student_email',
        'student_name',
        'email_type',
        'subject',
        'body',
        'tracking_token',
        'read_at',
        'status',
        'error_message',
        'related_model',
        'related_id',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    /** Whether open tracking applies (Send Email → custom_email with pixel). */
    public function tracksOpens(): bool
    {
        return $this->email_type === 'custom_email'
            && $this->status === 'sent'
            && filled($this->tracking_token);
    }

    /**
     * Get the student associated with this email log.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * All logs for a student (by id or email address).
     */
    public static function forStudent(int $studentId, string $email, int $limit = 25)
    {
        return self::query()
            ->where(function ($q) use ($studentId, $email) {
                $q->where('student_id', $studentId)
                    ->orWhere('student_email', $email);
            })
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Log a sent email.
     */
    public static function logSent(
        string $email,
        string $emailType,
        string $subject,
        ?int $studentId = null,
        ?string $studentName = null,
        ?string $relatedModel = null,
        ?int $relatedId = null,
        ?string $body = null,
        ?string $trackingToken = null
    ): self {
        return self::create([
            'student_id' => $studentId,
            'student_email' => $email,
            'student_name' => $studentName,
            'email_type' => $emailType,
            'subject' => $subject,
            'body' => $body,
            'tracking_token' => $trackingToken,
            'read_at' => null,
            'status' => 'sent',
            'related_model' => $relatedModel,
            'related_id' => $relatedId,
        ]);
    }

    /**
     * Log a failed email.
     */
    public static function logFailed(
        string $email,
        string $emailType,
        string $subject,
        string $errorMessage,
        ?int $studentId = null,
        ?string $studentName = null,
        ?string $relatedModel = null,
        ?int $relatedId = null,
        ?string $body = null,
        ?string $trackingToken = null
    ): self {
        return self::create([
            'student_id' => $studentId,
            'student_email' => $email,
            'student_name' => $studentName,
            'email_type' => $emailType,
            'subject' => $subject,
            'body' => $body,
            'tracking_token' => $trackingToken,
            'read_at' => null,
            'status' => 'failed',
            'error_message' => $errorMessage,
            'related_model' => $relatedModel,
            'related_id' => $relatedId,
        ]);
    }

    /**
     * Get a human-readable label for the email type.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->email_type) {
            'zoom_meeting_scheduled' => 'Zoom Meeting – Scheduled',
            'zoom_meeting_updated' => 'Zoom Meeting – Updated',
            'zoom_meeting_cancelled' => 'Zoom Meeting – Cancelled',
            'zoom_meeting_recording' => 'Zoom Meeting – Recording',
            'course_session_scheduled' => 'Course Session – Scheduled',
            'course_session_updated' => 'Course Session – Updated',
            'course_session_cancelled' => 'Course Session – Cancelled',
            'abandoned_cart' => 'Abandoned Cart Reminder',
            'student_enrollment' => 'Course Enrollment',
            'scheduled_task' => 'Scheduled Task',
            'certificate_generated' => 'Certificate Generated',
            'certificate_ready' => 'Certificate Ready',
            'practical_exam_graded' => 'Practical Exam Graded',
            'video_exam_validated' => 'Video Exam Validated',
            'video_exam_rejected' => 'Video Exam Rejected',
            'stage_validated' => 'Stage Validated',
            'stage_rejected' => 'Stage Rejected',
            'ticket_response' => 'Ticket Response',
            'ticket_status_changed' => 'Ticket Status Changed',
            'comment_reply' => 'Comment Reply',
            'account_blocked' => 'Account Blocked',
            'custom_email' => 'Custom Email',
            'password_reset' => 'Password Reset',
            'password_changed' => 'Password Changed Confirmation',
            'welcome' => 'Welcome Email',
            'course_expiration' => 'Course Expiration Reminder',
            'course_expired' => 'Course Expired',
            'quiz_retake' => 'Quiz Retake Opportunity',
            'quiz_passed' => 'Quiz Passed',
            'birthday_greeting' => 'Birthday Greeting',
            'payment_approved' => 'Payment Approved',
            'payment_rejected' => 'Payment Rejected',
            'success_approved' => 'Success Approved',
            'success_rejected' => 'Success Rejected',
            'student_success_approved' => 'Student Success Approved',
            'student_success_rejected' => 'Student Success Rejected',
            'partnership_response' => 'Partnership Response',
            'internal_message' => 'Internal Message',
            'internal_message_reply' => 'Internal Message Reply',
            'contact_response' => 'Contact Form Response',
            'referral_signup' => 'Referral — Friend Signed Up',
            'referral_reward_earned' => 'Referral — Reward Earned',
            'referral_discount_applied' => 'Referral — Discount Applied',
            'zoom_meeting_followup' => 'Zoom Meeting — Follow-up',
            'scheduled_task_test' => 'Scheduled Task (Test)',
            'course_extension_approved' => 'Course Extension Approved',
            'course_extension_rejected' => 'Course Extension Disapproved',
            default => ucwords(str_replace('_', ' ', $this->email_type)),
        };
    }

    /**
     * Get a badge color for the email type.
     */
    public function getTypeBadgeAttribute(): string
    {
        return match (true) {
            str_contains($this->email_type, 'zoom') => 'primary',
            str_contains($this->email_type, 'course_session') => 'info',
            str_contains($this->email_type, 'certificate') || str_contains($this->email_type, 'success') || str_contains($this->email_type, 'quiz_passed') => 'success',
            str_contains($this->email_type, 'exam') || str_contains($this->email_type, 'stage') => 'warning',
            str_contains($this->email_type, 'cart') || str_contains($this->email_type, 'payment') => 'secondary',
            str_contains($this->email_type, 'blocked') || str_contains($this->email_type, 'rejected') => 'danger',
            str_contains($this->email_type, 'ticket') => 'info',
            str_contains($this->email_type, 'internal') || str_contains($this->email_type, 'comment') => 'primary',
            str_contains($this->email_type, 'partnership') => 'warning',
            str_contains($this->email_type, 'custom') => 'dark',
            default => 'dark',
        };
    }
}
