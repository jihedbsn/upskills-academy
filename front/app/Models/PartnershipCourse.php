<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnershipCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_name',
        'institution_address',
        'phone',
        'email',
        'course_title_en',
        'course_type',
        'delivery_method',
        'evaluation_method',
        'course_description',
        'website_url',
        'status',
        'notes',
        'submitted_at'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
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

    public function getCourseTypeTextAttribute()
    {
        return match($this->course_type) {
            'training_course' => __('messages.training_course'),
            'workshop' => __('messages.workshop'),
            default => $this->course_type
        };
    }

    public function getDeliveryMethodTextAttribute()
    {
        return match($this->delivery_method) {
            'classroom' => __('messages.classroom'),
            'online' => __('messages.online'),
            default => $this->delivery_method
        };
    }

    public function getEvaluationMethodTextAttribute()
    {
        return match($this->evaluation_method) {
            'self_assessment' => __('messages.self_assessment'),
            'theoretical_test' => __('messages.theoretical_test'),
            'practical_test' => __('messages.practical_test'),
            'theoretical_practical_test' => __('messages.theoretical_practical_test'),
            default => $this->evaluation_method
        };
    }
}
