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

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    // Accesseurs
    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'pending' => 'En attente',
            'approved' => 'Approuvé',
            'rejected' => 'Rejeté',
            default => 'Inconnu',
        };
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'secondary',
        };
    }

    public function getCoursesListAttribute()
    {
        $courseNames = [
            'sports_injuries' => 'Exam Preparation (IELTS / TOEFL)',
            'sports_nutrition' => 'Business English',
            'fitness_assistant' => 'Foundation English (FA)',
            'fitness_trainer' => 'Fluency & Interaction (FI)',
            'personal_trainer' => 'Proficiency Track (PT)',
            'sports_rehabilitation' => 'Academic Writing',
            'sports_psychology' => 'Young Learners',
            'sports_management' => 'Teacher Training (TESOL)',
        ];

        if (!$this->requested_courses) {
            return [];
        }

        return collect($this->requested_courses)->map(function($course) use ($courseNames) {
            return $courseNames[$course] ?? $course;
        })->toArray();
    }

    public function getProfileFileUrlAttribute()
    {
        if ($this->profile_file) {
            return config('app.file_url') . 'storage/' . $this->profile_file;
        }
        return null;
    }
}
