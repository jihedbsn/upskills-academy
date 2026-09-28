<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriqueQuiz extends Model
{
    use HasFactory;

    protected $table = 'historique_quizzes';

    protected $fillable = [
        'student_id',
        'quiz_id',
        'product_id',
        'question_id',
        'response_id',
        'is_correct',
        'quiz_type',
        'attempt_number',
        'total_questions',
        'correct_answers',
        'score_percentage',
        'score',
        'success',
        'attempts',
        'started_at',
        'completed_at',
        'answers',
        'time_spent',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'answers' => 'array',
        'success' => 'boolean',
        'is_correct' => 'boolean',
        'attempts' => 'integer',
        'score' => 'integer',
        'time_spent' => 'integer',
        'attempt_number' => 'integer',
        'total_questions' => 'integer',
        'correct_answers' => 'integer',
        'score_percentage' => 'decimal:2'
    ];

    // Relations
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function response()
    {
        return $this->belongsTo(ReponseQuestion::class, 'response_id');
    }

    // Scopes
    public function scopeSuccessful($query)
    {
        return $query->where('success', true);
    }

    public function scopeFailed($query)
    {
        return $query->where('success', false);
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByQuiz($query, $quizId)
    {
        return $query->where('quiz_id', $quizId);
    }

    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('completed_at', '>=', now()->subDays($days));
    }

    // Accesseurs
    public function getStatusAttribute()
    {
        return $this->success ? 'success' : 'failed';
    }

    public function getStatusTextAttribute()
    {
        return $this->success ? 'Passed' : 'Failed';
    }

    public function getStatusColorAttribute()
    {
        return $this->success ? 'success' : 'danger';
    }

    public function getDurationAttribute()
    {
        if ($this->started_at && $this->completed_at) {
            return $this->started_at->diffInMinutes($this->completed_at);
        }
        return $this->time_spent ?? 0;
    }

    public function getFormattedDurationAttribute()
    {
        $minutes = $this->duration;
        if ($minutes < 60) {
            return $minutes . ' min';
        }
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
        return $hours . 'h ' . $remainingMinutes . 'min';
    }

    // Méthodes
    public function isSuccessful()
    {
        return $this->success;
    }

    public function isFailed()
    {
        return !$this->success;
    }

    public function getScorePercentage()
    {
        // Assuming max score is 100, adjust if different
        return round(($this->score / 100) * 100, 1);
    }

    public function getAttemptNumber()
    {
        // Get the attempt number for this quiz by this student
        return HistoriqueQuiz::where('student_id', $this->student_id)
            ->where('quiz_id', $this->quiz_id)
            ->where('id', '<=', $this->id)
            ->count();
    }
}
