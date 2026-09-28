<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailThread extends Model
{
    protected $fillable = [
        'subject',
        'subject_normalized',
        'participant_email',
        'participant_name',
        'student_id',
        'last_message_at',
        'messages_count',
        'unread_count',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
            'messages_count' => 'integer',
            'unread_count' => 'integer',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(EmailMessage::class, 'thread_id')->orderBy('created_at');
    }

    public function latestMessage()
    {
        return $this->hasOne(EmailMessage::class, 'thread_id')->latestOfMany();
    }

    public static function normalizeSubject(string $subject): string
    {
        $normalized = trim($subject);

        while (preg_match('/^(re|fw|fwd|aw|sv|ant|reply)\s*:\s*/iu', $normalized)) {
            $normalized = preg_replace('/^(re|fw|fwd|aw|sv|ant|reply)\s*:\s*/iu', '', $normalized);
            $normalized = trim($normalized);
        }

        return $normalized !== '' ? $normalized : '(no subject)';
    }

    public function replySubject(): string
    {
        $subject = $this->subject;

        if (! preg_match('/^re\s*:/iu', $subject)) {
            return 'Re: ' . $subject;
        }

        return $subject;
    }
}
