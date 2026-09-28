<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_type',
        'admin_id',
        'message',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Sender type constants
     */
    const SENDER_USER = 'user';
    const SENDER_AI = 'ai';
    const SENDER_ADMIN = 'admin';

    /**
     * Get the conversation this message belongs to
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(ChatConversation::class, 'conversation_id');
    }

    /**
     * Get the admin who sent this message (if applicable)
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Scope for unread messages
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for messages by sender type
     */
    public function scopeBySender($query, string $senderType)
    {
        return $query->where('sender_type', $senderType);
    }

    /**
     * Mark message as read
     */
    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    /**
     * Check if message is from user
     */
    public function isFromUser(): bool
    {
        return $this->sender_type === self::SENDER_USER;
    }

    /**
     * Check if message is from AI
     */
    public function isFromAI(): bool
    {
        return $this->sender_type === self::SENDER_AI;
    }

    /**
     * Check if message is from admin
     */
    public function isFromAdmin(): bool
    {
        return $this->sender_type === self::SENDER_ADMIN;
    }

    /**
     * Get sender label
     */
    public function getSenderLabelAttribute(): string
    {
        return match($this->sender_type) {
            self::SENDER_USER => 'User',
            self::SENDER_AI => 'AI Bot',
            self::SENDER_ADMIN => $this->admin ? $this->admin->name : 'Admin',
            default => 'Unknown',
        };
    }

    /**
     * Get sender badge color
     */
    public function getSenderColorAttribute(): string
    {
        return match($this->sender_type) {
            self::SENDER_USER => 'info',
            self::SENDER_AI => 'secondary',
            self::SENDER_ADMIN => 'primary',
            default => 'light',
        };
    }
}
