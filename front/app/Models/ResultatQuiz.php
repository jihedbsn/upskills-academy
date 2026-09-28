<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultatQuiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'product_id',
        'quiz_id',
        'score',
        'success',
        'attempts',
        'started_at',
        'completed_at'
    ];

    protected $casts = [
        'success' => 'boolean',
        'score' => 'decimal:2',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Relations
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    // Scopes
    public function scopeSuccessful($query)
    {
        return $query->where('success', true);
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    // Méthodes
    public function isBlocked()
    {
        // For exams (type_id = 1), check StudentExamAttempt table
        if ($this->quiz && $this->quiz->type_id == 1) {
            $examAttempt = \App\Models\StudentExamAttempt::where('student_id', $this->student_id)
                ->where('product_id', $this->product_id)
                ->where('quiz_id', $this->quiz_id)
                ->first();

            return $examAttempt ? $examAttempt->is_blocked : false;
        }

        // For regular quizzes (type_id = 2), use the old logic
        $maxAttempts = $this->quiz->max_attempts;

        if (is_null($maxAttempts)) {
            return false;
        }

        return $this->attempts >= $maxAttempts && !$this->success;
    }

    public function getExamAttemptInfo()
    {
        // Only for exams (type_id = 1)
        if ($this->quiz && $this->quiz->type_id == 1) {
            $examAttempt = \App\Models\StudentExamAttempt::where('student_id', $this->student_id)
                ->where('product_id', $this->product_id)
                ->where('quiz_id', $this->quiz_id)
                ->first();

            $maxAttempts = $this->product->max_exam_attempts ?? 3;

            return [
                'attempts' => $examAttempt ? $examAttempt->attempts : 0,
                'max_attempts' => $maxAttempts,
                'is_blocked' => $examAttempt ? $examAttempt->is_blocked : false,
            ];
        }

        return null;
    }

    public function canRetake()
    {
        // Si max_attempts est null, c'est illimité
        $maxAttempts = $this->quiz->max_attempts;

        if (is_null($maxAttempts)) {
            return !$this->success;
        }

        return $this->attempts < $maxAttempts && !$this->success;
    }

    public function incrementAttempts()
    {
        $this->increment('attempts');
        $this->refresh();
    }

    public function markAsSuccessful()
    {
        $this->update([
            'success' => true,
            'completed_at' => now()
        ]);
    }
}
