<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'student_id',
        'admin_id',
        'status',
        'admin_takeover',
        'admin_takeover_at',
        'visitor_ip',
        'visitor_country',
        'visitor_language',
        'last_message',
        'last_message_at',
        'unread_admin_count',
        'unread_user_count',
    ];

    protected $casts = [
        'admin_takeover' => 'boolean',
        'admin_takeover_at' => 'datetime',
        'last_message_at' => 'datetime',
        'unread_admin_count' => 'integer',
        'unread_user_count' => 'integer',
    ];

    /**
     * Status constants
     */
    const STATUS_ACTIVE = 'active';
    const STATUS_ADMIN_TAKEN = 'admin_taken';
    const STATUS_CLOSED = 'closed';

    /**
     * Get all messages for this conversation
     */
    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id')->orderBy('created_at', 'asc');
    }

    /**
     * Get the student for this conversation
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the admin handling this conversation
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Scope for active conversations
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope for conversations taken over by admin
     */
    public function scopeAdminTaken($query)
    {
        return $query->where('admin_takeover', true);
    }

    /**
     * Scope for conversations with unread messages
     */
    public function scopeWithUnread($query)
    {
        return $query->where('unread_admin_count', '>', 0);
    }

    /**
     * Scope for recent conversations
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('last_message_at', 'desc');
    }

    /**
     * Take over conversation by admin
     */
    public function takeOver(Admin $admin): void
    {
        $this->update([
            'admin_id' => $admin->id,
            'admin_takeover' => true,
            'admin_takeover_at' => now(),
            'status' => self::STATUS_ADMIN_TAKEN,
        ]);
    }

    /**
     * Release conversation back to AI
     */
    public function releaseToAI(): void
    {
        $this->update([
            'admin_takeover' => false,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Close the conversation
     */
    public function closeConversation(): void
    {
        $this->update([
            'status' => self::STATUS_CLOSED,
        ]);
    }

    /**
     * Add a message to the conversation
     */
    public function addMessage(string $message, string $senderType, ?int $adminId = null): ChatMessage
    {
        $chatMessage = $this->messages()->create([
            'message' => $message,
            'sender_type' => $senderType,
            'admin_id' => $adminId,
        ]);

        // Update conversation metadata
        $updateData = [
            'last_message' => \Str::limit($message, 100),
            'last_message_at' => now(),
        ];

        // Increment unread count based on sender
        if ($senderType === 'user') {
            $updateData['unread_admin_count'] = $this->unread_admin_count + 1;
        } elseif (in_array($senderType, ['admin', 'ai'])) {
            $updateData['unread_user_count'] = $this->unread_user_count + 1;
        }

        $this->update($updateData);

        return $chatMessage;
    }

    /**
     * Mark messages as read
     */
    public function markAsReadByAdmin(): void
    {
        $this->messages()
            ->where('sender_type', 'user')
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        $this->update(['unread_admin_count' => 0]);
    }

    /**
     * Mark messages as read by user
     */
    public function markAsReadByUser(): void
    {
        $this->messages()
            ->whereIn('sender_type', ['admin', 'ai'])
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        $this->update(['unread_user_count' => 0]);
    }

    /**
     * Get display name for conversation
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->student) {
            return $this->student->first_name . ' ' . $this->student->last_name;
        }
        return 'Visitor #' . $this->id;
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_ACTIVE => 'success',
            self::STATUS_ADMIN_TAKEN => 'primary',
            self::STATUS_CLOSED => 'secondary',
            default => 'light',
        };
    }

    /**
     * Check if conversation is handled by admin
     */
    public function isHandledByAdmin(): bool
    {
        return $this->admin_takeover && $this->status === self::STATUS_ADMIN_TAKEN;
    }
}
