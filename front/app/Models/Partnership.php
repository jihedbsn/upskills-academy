<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partnership extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_name',
        'institution_address',
        'email',
        'phone',
        'website',
        'profile_file',
        'requested_courses',
        'additional_courses',
        'status',
        'notes',
        'is_read',
        'submitted_at'
    ];

    protected $casts = [
        'requested_courses' => 'array',
        'submitted_at' => 'datetime',
        'is_read' => 'boolean',
    ];

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Accesseurs
    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'pending' => __('messages.pending'),
            'approved' => __('messages.approved'),
            'rejected' => __('messages.rejected'),
            default => __('messages.unknown')
        };
    }

    public function getRequestedCoursesTextAttribute()
    {
        if (!$this->requested_courses) {
            return '';
        }

        $courseNames = [
            'sports_injuries' => __('messages.sports_injuries'),
            'sports_nutrition' => __('messages.sports_nutrition'),
            'fitness_assistant' => __('messages.fitness_assistant'),
            'fitness_trainer' => __('messages.fitness_trainer'),
            'personal_trainer' => __('messages.personal_trainer'),
            'sports_rehabilitation' => __('messages.sports_rehabilitation'),
            'sports_psychology' => __('messages.sports_psychology'),
            'sports_management' => __('messages.sports_management'),
        ];

        $selectedCourses = [];
        foreach ($this->requested_courses as $course) {
            if (isset($courseNames[$course])) {
                $selectedCourses[] = $courseNames[$course];
            }
        }

        return implode(', ', $selectedCourses);
    }
}
