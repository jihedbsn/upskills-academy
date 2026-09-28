<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InternalMessage extends Model
{
    protected $fillable = [
        'subject',
        'body',
        'attachments',
        'sender_admin_id'
    ];

    protected $casts = [
        'attachments' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get all recipients for this message
     */
    public function recipients()
    {
        return $this->hasMany(MessageRecipient::class, 'message_id');
    }

    /**
     * Get all responses for this message
     */
    public function responses()
    {
        return $this->hasMany(MessageResponse::class, 'message_id');
    }

    /**
     * Get all students who received this message
     */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'message_recipients', 'message_id', 'student_id')
            ->withPivot('is_read', 'read_at')
            ->withTimestamps();
    }

    /**
     * Get unread count for this message
     */
    public function getUnreadCountAttribute()
    {
        return $this->recipients()->where('is_read', false)->count();
    }

    /**
     * Get total recipients count
     */
    public function getTotalRecipientsAttribute()
    {
        return $this->recipients()->count();
    }
}
