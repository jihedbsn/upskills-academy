<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriqueQuiz extends Model
{
    use HasFactory;

    protected $table = 'historique_quizzes';

    protected $fillable = [
        'product_id',
        'student_id',
        'quiz_id',
        'question_id',
        'response_id',
        'is_correct',
        'quiz_type', // 1 = examen, 2 = quiz
        'attempt_number',
        'total_questions',
        'correct_answers',
        'score_percentage',
        'success',
        'time_spent', // en secondes
        'started_at',
        'completed_at'
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'success' => 'boolean',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'time_spent' => 'integer',
        'attempt_number' => 'integer',
        'total_questions' => 'integer',
        'correct_answers' => 'integer',
        'score_percentage' => 'decimal:2'
    ];

    // Relations
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function response()
    {
        return $this->belongsTo(ReponseQuestion::class, 'response_id');
    }

    // Scopes
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeForQuiz($query, $quizId)
    {
        return $query->where('quiz_id', $quizId);
    }

    public function scopeByQuizType($query, $type)
    {
        return $query->where('quiz_type', $type);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('success', true);
    }

    public function scopeFailed($query)
    {
        return $query->where('success', false);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('completed_at', '>=', now()->subDays($days));
    }

    // Accesseurs
    public function getQuizTypeNameAttribute()
    {
        return $this->quiz_type == 1 ? 'Examen' : 'Quiz';
    }

    public function getTimeSpentFormattedAttribute()
    {
        if (!$this->time_spent) return null;
        
        $minutes = floor($this->time_spent / 60);
        $seconds = $this->time_spent % 60;
        
        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    public function getScoreFormattedAttribute()
    {
        return number_format($this->score_percentage, 1) . '%';
    }

    // Méthodes statiques
    public static function getStudentStats($studentId, $productId = null)
    {
        $query = static::forStudent($studentId);
        
        if ($productId) {
            $query->forProduct($productId);
        }

        $stats = $query->selectRaw('
            COUNT(*) as total_attempts,
            COUNT(DISTINCT quiz_id) as unique_quizzes_taken,
            COUNT(DISTINCT product_id) as unique_courses,
            AVG(score_percentage) as average_score,
            SUM(CASE WHEN success = 1 THEN 1 ELSE 0 END) as successful_attempts,
            AVG(time_spent) as average_time_spent
        ')->first();

        return [
            'total_attempts' => $stats->total_attempts ?? 0,
            'unique_quizzes_taken' => $stats->unique_quizzes_taken ?? 0,
            'unique_courses' => $stats->unique_courses ?? 0,
            'average_score' => round($stats->average_score ?? 0, 1),
            'success_rate' => $stats->total_attempts > 0 ? round(($stats->successful_attempts / $stats->total_attempts) * 100, 1) : 0,
            'average_time_spent' => round($stats->average_time_spent ?? 0),
        ];
    }

    public static function getProductStats($productId)
    {
        $stats = static::forProduct($productId)->selectRaw('
            COUNT(*) as total_attempts,
            COUNT(DISTINCT student_id) as unique_students,
            AVG(score_percentage) as average_score,
            SUM(CASE WHEN success = 1 THEN 1 ELSE 0 END) as successful_attempts
        ')->first();

        return [
            'total_attempts' => $stats->total_attempts ?? 0,
            'unique_students' => $stats->unique_students ?? 0,
            'average_score' => round($stats->average_score ?? 0, 1),
            'success_rate' => $stats->total_attempts > 0 ? round(($stats->successful_attempts / $stats->total_attempts) * 100, 1) : 0,
        ];
    }

    // Méthodes d'instance
    public function isExam()
    {
        return $this->quiz_type == 1;
    }

    public function isQuiz()
    {
        return $this->quiz_type == 2;
    }

    public function getDuration()
    {
        if (!$this->started_at || !$this->completed_at) {
            return null;
        }

        return $this->started_at->diffInSeconds($this->completed_at);
    }
}
