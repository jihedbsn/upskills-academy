<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

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

    public static function getEnrolledCourseIds(int $studentId): Collection
    {
        $fromOrders = Order::query()
            ->where('student_id', $studentId)
            ->where('payment_success', 1)
            ->whereNotNull('product_id')
            ->where('product_id', '>', 0)
            ->pluck('product_id');

        $fromProductStudents = ProductStudent::query()
            ->where('student_id', $studentId)
            ->whereNotNull('product_id')
            ->where('product_id', '>', 0)
            ->pluck('product_id');

        return $fromOrders->merge($fromProductStudents)->unique()->values();
    }

    public static function getCourseTitle(Product $course): string
    {
        $course->loadMissing('variations');

        $variation = $course->variations->firstWhere('langue', app()->getLocale())
            ?? $course->variations->firstWhere('langue', 'en')
            ?? $course->variations->firstWhere('langue', 'ar')
            ?? $course->variations->first();

        return $variation?->name ?? ('Course #' . $course->id);
    }

    public static function mapCoursesForBlock(Collection $courseIds): Collection
    {
        if ($courseIds->isEmpty()) {
            return collect();
        }

        return Product::with('variations')
            ->whereIn('id', $courseIds->values())
            ->orderBy('id', 'desc')
            ->get()
            ->map(function (Product $course) {
                return [
                    'id' => $course->id,
                    'title' => static::getCourseTitle($course),
                    'price' => (float) ($course->prix ?? 0),
                ];
            })
            ->values();
    }
}
