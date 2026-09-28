<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Shared email_logs table (managed by admin app).
 * Used by the user app to record outbound emails such as password reset.
 */
class EmailLog extends Model
{
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
}
