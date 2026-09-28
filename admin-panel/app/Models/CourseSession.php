<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class CourseSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'title',
        'description',
        'session_date',
        'start_time',
        'end_time',
        'session_type',
        'instructor_name',
        'location',
        'zoom_meeting_id',
        'zoom_join_url',
        'status',
        'notes',
    ];

    protected $casts = [
        'session_date' => 'datetime',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    // Session type constants
    const TYPE_THEORY = 'theory';
    const TYPE_PRACTICAL = 'practical';
    const TYPE_ONLINE = 'online';
    const TYPE_CLASSROOM = 'classroom';

    // Status constants
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_ONGOING = 'ongoing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Get the product (course) this session belongs to
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get enrolled students for this session's course
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
     * Get formatted date and time
     */
    public function getFormattedDateTimeAttribute()
    {
        return $this->session_date->format('F j, Y') . ' at ' .
               Carbon::parse($this->start_time)->format('g:i A') . ' - ' .
               Carbon::parse($this->end_time)->format('g:i A');
    }

    /**
     * Get formatted date only
     */
    public function getFormattedDateAttribute()
    {
        return $this->session_date->format('F j, Y');
    }

    /**
     * Get formatted time range
     */
    public function getFormattedTimeAttribute()
    {
        return Carbon::parse($this->start_time)->format('g:i A') . ' - ' .
               Carbon::parse($this->end_time)->format('g:i A');
    }

    /**
     * Check if session is upcoming
     */
    public function isUpcoming()
    {
        $sessionDateTime = Carbon::parse($this->session_date->format('Y-m-d') . ' ' . Carbon::parse($this->start_time)->format('H:i:s'));
        return $sessionDateTime->isFuture() && $this->status === self::STATUS_SCHEDULED;
    }

    /**
     * Check if session is past
     */
    public function isPast()
    {
        $sessionDateTime = Carbon::parse($this->session_date->format('Y-m-d') . ' ' . Carbon::parse($this->end_time)->format('H:i:s'));
        return $sessionDateTime->isPast();
    }

    /**
     * Check if session is today
     */
    public function isToday()
    {
        return $this->session_date->isToday();
    }

    /**
     * Scope for upcoming sessions
     */
    public function scopeUpcoming($query)
    {
        return $query->where('status', self::STATUS_SCHEDULED)
                     ->where('session_date', '>=', now()->toDateString())
                     ->orderBy('session_date')
                     ->orderBy('start_time');
    }

    /**
     * Scope for past sessions
     */
    public function scopePast($query)
    {
        return $query->where('session_date', '<', now()->toDateString())
                     ->orderBy('session_date', 'desc')
                     ->orderBy('start_time', 'desc');
    }

    /**
     * Scope by course
     */
    public function scopeByCourse($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Scope by session type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('session_type', $type);
    }

    /**
     * Scope by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Get session type label
     */
    public function getTypeLabel()
    {
        return match($this->session_type) {
            self::TYPE_THEORY => 'Theory',
            self::TYPE_PRACTICAL => 'Practical',
            self::TYPE_ONLINE => 'Online (Zoom)',
            self::TYPE_CLASSROOM => 'Classroom',
            default => 'Unknown',
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabel()
    {
        return match($this->status) {
            self::STATUS_SCHEDULED => 'Scheduled',
            self::STATUS_ONGOING => 'Ongoing',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
            default => 'Unknown',
        };
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColor()
    {
        return match($this->status) {
            self::STATUS_SCHEDULED => 'primary',
            self::STATUS_ONGOING => 'success',
            self::STATUS_COMPLETED => 'secondary',
            self::STATUS_CANCELLED => 'danger',
            default => 'light',
        };
    }
}
