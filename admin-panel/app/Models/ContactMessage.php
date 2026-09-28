<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory;

    protected $table = 'contact_messages';

    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'is_read',
        'read_at',
        'read_by',
        'admin_response',
        'responded_at',
        'responded_by',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    /**
     * Scope for unread messages
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for read messages
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Mark as read
     */
    public function markAsRead($adminId = null)
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
            'read_by' => $adminId,
        ]);
    }

    /**
     * Get the admin who read the message
     */
    public function readByAdmin()
    {
        return $this->belongsTo(Admin::class, 'read_by');
    }

    /**
     * Get the admin who responded
     */
    public function respondedByAdmin()
    {
        return $this->belongsTo(Admin::class, 'responded_by');
    }

    /**
     * Get unread count
     */
    public static function getUnreadCount()
    {
        return self::unread()->count();
    }
}
