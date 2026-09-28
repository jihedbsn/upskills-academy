<?php

namespace App\Services;

use App\Models\EmailLog;
use App\Models\Student;

/**
 * Central logger for all outbound emails sent to students.
 * Resolves student_id from email when not provided.
 */
class OutboundEmailLogger
{
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
    ): EmailLog {
        [$studentId, $studentName] = self::resolveStudent($email, $studentId, $studentName);

        return EmailLog::logSent(
            $email,
            $emailType,
            $subject,
            $studentId,
            $studentName,
            $relatedModel,
            $relatedId,
            $body,
            $trackingToken
        );
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
    ): EmailLog {
        [$studentId, $studentName] = self::resolveStudent($email, $studentId, $studentName);

        return EmailLog::logFailed(
            $email,
            $emailType,
            $subject,
            $errorMessage,
            $studentId,
            $studentName,
            $relatedModel,
            $relatedId,
            $body,
            $trackingToken
        );
    }

    /**
     * @return array{0: ?int, 1: ?string}
     */
    private static function resolveStudent(string $email, ?int $studentId, ?string $studentName): array
    {
        if ($studentId) {
            return [$studentId, $studentName];
        }

        $student = Student::where('email', $email)->first(['id', 'first_name', 'last_name']);

        if (!$student) {
            return [null, $studentName];
        }

        return [
            $student->id,
            $studentName ?? trim("{$student->first_name} {$student->last_name}"),
        ];
    }
}
