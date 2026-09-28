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
        return $this->attempts >= 3 && !$this->success;
    }

    public function canRetake()
    {
        return $this->attempts < 3 && !$this->success;
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
