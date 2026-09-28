<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlockStudentCourse extends Model
{
    protected $fillable = [
        'course_id',
        'student_id',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'course_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public static function isBlocked(int $courseId, int $studentId): bool
    {
        return static::where('course_id', $courseId)
            ->where('student_id', $studentId)
            ->exists();
    }
}
