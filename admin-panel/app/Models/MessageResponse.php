<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageResponse extends Model
{
    protected $fillable = [
        'message_id',
        'student_id',
        'response_body',
        'response_attachments',
        'is_read_by_admin',
        'read_by_admin_at',
    ];

    protected $casts = [
        'response_attachments' => 'array',
        'is_read_by_admin' => 'boolean',
        'read_by_admin_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the message this response belongs to
     */
    public function message()
    {
        return $this->belongsTo(InternalMessage::class, 'message_id');
    }

    /**
     * Get the student who sent this response
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Get all admin responses to this student response
     */
    public function adminResponses()
    {
        return $this->hasMany(AdminResponse::class, 'message_response_id');
    }

    public function markAsReadByAdmin(): void
    {
        if ($this->is_read_by_admin) {
            return;
        }

        $this->update([
            'is_read_by_admin' => true,
            'read_by_admin_at' => now(),
        ]);
    }

    public static function markAllAsReadByAdminForMessage(int $messageId): int
    {
        return self::where('message_id', $messageId)
            ->where('is_read_by_admin', false)
            ->update([
                'is_read_by_admin' => true,
                'read_by_admin_at' => now(),
            ]);
    }
}
