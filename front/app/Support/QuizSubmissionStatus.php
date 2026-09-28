<?php

namespace App\Support;

use Carbon\Carbon;

class QuizSubmissionStatus
{
    public const COMPLETED = 'completed';

    public const TIME_EXPIRED = 'time_expired';

    public const ABANDONED = 'abandoned';

    public const IN_PROGRESS = 'in_progress';

    public static function timeLimitSeconds(int $totalQuestions): int
    {
        return max(60, $totalQuestions * 60);
    }

    public static function resolve(
        ?Carbon $startedAt,
        ?Carbon $completedAt,
        int $totalQuestions,
        ?int $timeSpentSeconds,
        ?int $answeredCount
    ): string {
        $totalQuestions = max(1, $totalQuestions);
        $limit = self::timeLimitSeconds($totalQuestions);

        if (!$completedAt) {
            if ($startedAt && $startedAt->diffInSeconds(now()) >= $limit) {
                return self::TIME_EXPIRED;
            }

            return self::IN_PROGRESS;
        }

        if ($timeSpentSeconds !== null && $timeSpentSeconds >= ($limit - 2)) {
            return self::TIME_EXPIRED;
        }

        if ($startedAt) {
            $expectedEnd = $startedAt->copy()->addSeconds($limit);
            if (abs($completedAt->diffInSeconds($expectedEnd)) <= 3) {
                return self::TIME_EXPIRED;
            }
        }

        if ($answeredCount !== null && $answeredCount < $totalQuestions) {
            return self::ABANDONED;
        }

        return self::COMPLETED;
    }

    public static function label(string $status): string
    {
        return match ($status) {
            self::TIME_EXPIRED => __('messages.quiz_status_time_expired'),
            self::ABANDONED => __('messages.quiz_status_abandoned'),
            self::IN_PROGRESS => __('messages.quiz_status_in_progress'),
            default => __('messages.quiz_status_completed'),
        };
    }

    public static function badgeClass(string $status): string
    {
        return match ($status) {
            self::TIME_EXPIRED => 'warning',
            self::ABANDONED => 'secondary',
            self::IN_PROGRESS => 'info',
            default => 'success',
        };
    }
}
