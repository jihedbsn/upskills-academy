<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'title',
        'message',
        'action_url',
        'icon',
        'color',
        'read_at',
        'is_important'
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'is_important' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Notification types
    const TYPE_COMMENT = 'comment';
    const TYPE_RATING = 'rating';
    const TYPE_PURCHASE = 'purchase';
    const TYPE_INSTALLMENT = 'installment';
    const TYPE_TICKET = 'ticket';
    const TYPE_EVALUATION = 'evaluation';
    const TYPE_FORUM_COMMENT = 'forum_comment';
    const TYPE_STUDENT_SUCCESS = 'student_success';
    const TYPE_MANUAL_CERTIFICATE = 'manual_certificate';
    const TYPE_REFERRAL = 'referral';

    /**
     * Get the notifiable entity (Admin, Student, etc.)
     */
    public function notifiable()
    {
        return $this->morphTo();
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope for read notifications
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Scope for important notifications
     */
    public function scopeImportant($query)
    {
        return $query->where('is_important', true);
    }

    /**
     * Scope for a specific type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead()
    {
        if (is_null($this->read_at)) {
            $this->forceFill(['read_at' => now()])->save();
        }
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread()
    {
        if (!is_null($this->read_at)) {
            $this->forceFill(['read_at' => null])->save();
        }
    }

    /**
     * Check if notification is read
     */
    public function isRead()
    {
        return !is_null($this->read_at);
    }

    /**
     * Check if notification is unread
     */
    public function isUnread()
    {
        return is_null($this->read_at);
    }

    /**
     * Get time ago format
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Create a notification for a user
     */
    public static function createForUser($notifiableType, $notifiableId, $type, $title, $message, $actionUrl = null, $data = [], $icon = null, $color = 'blue', $isImportant = false)
    {
        return self::create([
            'notifiable_type' => $notifiableType,
            'notifiable_id' => $notifiableId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'action_url' => $actionUrl,
            'data' => $data,
            'icon' => $icon,
            'color' => $color,
            'is_important' => $isImportant,
        ]);
    }

    /**
     * Create notification for admin
     */
    public static function notifyAdmin($adminId, $type, $title, $message, $actionUrl = null, $data = [], $icon = null, $color = 'blue')
    {
        return self::createForUser('App\Models\Admin', $adminId, $type, $title, $message, $actionUrl, $data, $icon, $color);
    }

    /**
     * Create notification for student
     */
    public static function notifyStudent($studentId, $type, $title, $message, $actionUrl = null, $data = [], $icon = null, $color = 'blue', $isImportant = false)
    {
        // Use App\Models\User so notifications appear in the student UI bell
        return self::createForUser('App\Models\User', $studentId, $type, $title, $message, $actionUrl, $data, $icon, $color, $isImportant);
    }

    /**
     * Create notification for all admins
     */
    public static function notifyAllAdmins($type, $title, $message, $actionUrl = null, $data = [], $icon = null, $color = 'blue')
    {
        $admins = \App\Models\Admin::all();
        $notifications = [];

        foreach ($admins as $admin) {
            $notifications[] = self::notifyAdmin($admin->id, $type, $title, $message, $actionUrl, $data, $icon, $color);
        }

        return $notifications;
    }
}
