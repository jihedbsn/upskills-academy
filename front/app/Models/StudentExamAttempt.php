<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentExamAttempt extends Model
{
    protected $fillable = [
        'student_id',
        'product_id',
        'quiz_id',
        'attempts',
        'is_blocked',
        'blocked_at',
        'renewal_email_sent_at',
    ];

    protected $casts = [
        'is_blocked' => 'boolean',
        'blocked_at' => 'datetime',
        'renewal_email_sent_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Increment exam attempts and check if should be blocked
     */
    public function incrementAttempt(): bool
    {
        $this->increment('attempts');

        $maxAttempts = $this->product->max_exam_attempts ?? 3;

        if ($this->attempts >= $maxAttempts && !$this->is_blocked) {
            $this->update([
                'is_blocked' => true,
                'blocked_at' => now(),
            ]);
            return true; // Indicates that blocking occurred
        }

        return false;
    }

    /**
     * Check if student can take the exam
     */
    public function canTakeExam(): bool
    {
        return !$this->is_blocked;
    }

    /**
     * Get remaining attempts
     */
    public function getRemainingAttempts(): int
    {
        $maxAttempts = $this->product->max_exam_attempts ?? 3;
        return max(0, $maxAttempts - $this->attempts);
    }
}
