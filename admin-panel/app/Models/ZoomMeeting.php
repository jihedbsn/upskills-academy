<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ZoomMeeting extends Model
{
    protected $fillable = [
        'product_id',
        'zoom_meeting_id',
        'topic',
        'start_time',
        'duration',
        'timezone',
        'password',
        'join_url',
        'start_url',
        'recording_url',
        'moderator_email',
        'created_by',
        'status',
        'agenda',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'duration' => 'integer',
    ];

    /**
     * Status constants
     */
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Get the product (course) this meeting is for
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the admin user who created this meeting
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get enrolled students for this meeting's course
     */
    public function getEnrolledStudents()
    {
        return $this->product->productStudents()
            ->where('is_active', true)
            ->with('student')
            ->get()
            ->pluck('student');
    }

    /**
     * Get formatted start time
     */
    public function getFormattedStartTimeAttribute()
    {
        return $this->start_time->format('F j, Y \a\t g:i A');
    }

    /**
     * Get formatted date only
     */
    public function getFormattedDateAttribute()
    {
        return $this->start_time->format('F j, Y');
    }

    /**
     * Get formatted time only
     */
    public function getFormattedTimeAttribute()
    {
        return $this->start_time->format('g:i A');
    }

    /**
     * Check if meeting is upcoming
     */
    public function isUpcoming()
    {
        return $this->start_time->isFuture() && $this->status === self::STATUS_SCHEDULED;
    }

    /**
     * Check if meeting is past
     */
    public function isPast()
    {
        return $this->start_time->isPast();
    }

    /**
     * Check if meeting is cancelled
     */
    public function isCancelled()
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Mark meeting as completed
     */
    public function markAsCompleted()
    {
        $this->update(['status' => self::STATUS_COMPLETED]);
    }

    /**
     * Cancel meeting
     */
    public function cancel()
    {
        $this->update(['status' => self::STATUS_CANCELLED]);
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            self::STATUS_SCHEDULED => 'badge-success',
            self::STATUS_COMPLETED => 'badge-secondary',
            self::STATUS_CANCELLED => 'badge-danger',
            default => 'badge-primary',
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return ucfirst($this->status);
    }
}
