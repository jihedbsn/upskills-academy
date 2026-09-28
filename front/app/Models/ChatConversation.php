<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

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
     * Check if conversation is handled by admin
     */
    public function isHandledByAdmin(): bool
    {
        return $this->admin_takeover && $this->status === self::STATUS_ADMIN_TAKEN;
    }

    /**
     * Check if conversation is active
     */
    public function isActive(): bool
    {
        return $this->status !== self::STATUS_CLOSED;
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
            'last_message' => Str::limit($message, 100),
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
     * Get unread messages count for user
     */
    public function getUnreadCountForUser(): int
    {
        return $this->messages()
            ->whereIn('sender_type', ['admin', 'ai'])
            ->where('is_read', false)
            ->count();
    }

    /**
     * Get or create conversation for session
     */
    public static function getOrCreateForSession(string $sessionId, ?int $studentId = null, array $visitorData = []): self
    {
        return self::firstOrCreate(
            ['session_id' => $sessionId],
            array_merge([
                'student_id' => $studentId,
                'status' => self::STATUS_ACTIVE,
                'admin_takeover' => false,
                'unread_admin_count' => 0,
                'unread_user_count' => 0,
            ], $visitorData)
        );
    }
}
