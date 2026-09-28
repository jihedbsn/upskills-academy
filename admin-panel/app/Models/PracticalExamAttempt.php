<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PracticalExamAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'training_case_id',
        'video_url',
        'status',
        'admin_comment',
        'reviewed_by',
        'attempt_number',
        'submitted_at',
        'reviewed_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get the student who made this attempt
     */
    public function user()
    {
        return $this->belongsTo(Student::class, 'user_id');
    }

    /**
     * Get the product (course) this attempt is for
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the training case assigned for this attempt
     */
    public function trainingCase()
    {
        return $this->belongsTo(TrainingCase::class);
    }

    /**
     * Get the specific training case file assigned for this attempt
     */
    public function trainingCaseFile()
    {
        return $this->belongsTo(TrainingCaseFile::class);
    }

    /**
     * Get the admin who reviewed this attempt
     */
    public function reviewer()
    {
        return $this->belongsTo(Admin::class, 'reviewed_by');
    }

    /**
     * Scope to get pending attempts
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get passed attempts
     */
    public function scopePassed($query)
    {
        return $query->where('status', 'passed');
    }

    /**
     * Scope to get failed attempts
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}
