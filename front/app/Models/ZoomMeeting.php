<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ZoomMeeting extends Model
{
    // Connect to admin database
    protected $connection = 'mysql';

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
     * Check if meeting is upcoming
     */
    public function isUpcoming(): bool
    {
        return $this->start_time->isFuture() && $this->status === self::STATUS_SCHEDULED;
    }

    /**
     * Check if meeting has passed
     */
    public function isPast(): bool
    {
        return $this->start_time->isPast();
    }

    /**
     * Get formatted date
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->start_time->format('F j, Y');
    }

    /**
     * Get formatted time
     */
    public function getFormattedTimeAttribute(): string
    {
        return $this->start_time->format('g:i A');
    }

    /**
     * Get end time
     */
    public function getEndTimeAttribute(): Carbon
    {
        return $this->start_time->copy()->addMinutes($this->duration);
    }

    /**
     * Scope for upcoming meetings
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_time', '>', now())
                    ->where('status', self::STATUS_SCHEDULED)
                    ->orderBy('start_time', 'asc');
    }

    /**
     * Scope for past meetings
     */
    public function scopePast($query)
    {
        return $query->where('start_time', '<=', now())
                    ->orderBy('start_time', 'desc');
    }

    /**
     * Scope for student's enrolled courses
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->whereIn('product_id', function($subquery) use ($studentId) {
            $subquery->select('product_id')
                     ->from('product_students')
                     ->where('student_id', $studentId);
        });
    }
}
