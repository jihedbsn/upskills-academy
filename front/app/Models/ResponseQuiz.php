<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResponseQuiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'student_id',
        'quiz_id',
        'question_id',
        'response_id',
        'is_correct'
    ];

    protected $casts = [
        'is_correct' => 'boolean',
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

    public function reponse()
    {
        return $this->belongsTo(ReponseQuestion::class, 'response_id');
    }
}
