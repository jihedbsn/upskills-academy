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
        'training_case_file_id',
        'attempt_number',
        'status',
        'video_url',
        'admin_comment',
        'reviewed_by',
        'reviewed_at',
        'started_at',
        'submitted_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function trainingCase()
    {
        return $this->belongsTo(TrainingCase::class);
    }

    public function trainingCaseFile()
    {
        return $this->belongsTo(TrainingCaseFile::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
