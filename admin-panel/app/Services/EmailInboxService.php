<?php

namespace App\Services;

use App\Mail\CustomEmail;
use App\Models\EmailMessage;
use App\Models\EmailMessageAttachment;
use App\Models\EmailThread;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EmailInboxService
{
    public function listThreads(?string $search = null, ?string $filter = null)
    {
        $query = EmailThread::with('latestMessage', 'student')
            ->orderByDesc('last_message_at');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', '%' . $search . '%')
                    ->orWhere('participant_email', 'like', '%' . $search . '%')
                    ->orWhere('participant_name', 'like', '%' . $search . '%');
            });
        }

        if ($filter === 'unread') {
            $query->where('unread_count', '>', 0);
        } elseif ($filter === 'closed') {
            $query->where('status', 'closed');
        } else {
            $query->where('status', 'open');
        }

        return $query->paginate(20);
    }

    public function unreadCount(): int
    {
        return (int) EmailThread::where('unread_count', '>', 0)->sum('unread_count');
    }

    public function getThread(EmailThread $thread): EmailThread
    {
        return $thread->load([
            'student',
            'messages.attachments',
            'messages.admin',
        ]);
    }

    public function markThreadAsRead(EmailThread $thread): void
    {
        DB::transaction(function () use ($thread) {
            EmailMessage::where('thread_id', $thread->id)
                ->where('direction', 'inbound')
                ->where('is_read', false)
                ->update(['is_read' => true]);

            $thread->update(['unread_count' => 0]);
        });
    }

    public function generateMessageId(): string
    {
        $domain = parse_url(config('app.url', 'http://localhost'), PHP_URL_HOST) ?: 'localhost';

        return bin2hex(random_bytes(16)) . '@' . $domain;
    }

    public function formatMessageId(?string $messageId): ?string
    {
        if (! $messageId) {
            return null;
        }

        return trim(trim($messageId), '<>');
    }

    public function resolveThread(
        string $participantEmail,
        string $subject,
        ?string $messageId = null,
        ?string $inReplyTo = null,
        ?string $references = null,
        ?string $participantName = null
    ): EmailThread {
        $participantEmail = strtolower(trim($participantEmail));
        $inReplyTo = $this->formatMessageId($inReplyTo);

        if ($inReplyTo) {
            $parent = EmailMessage::whereRaw('TRIM(BOTH "<>" FROM message_id) = ?', [$inReplyTo])->first();
            if ($parent) {
                return $parent->thread;
            }
        }

        if ($references) {
            foreach (preg_split('/\s+/', $references) as $ref) {
                $ref = $this->formatMessageId($ref);
                if (! $ref) {
                    continue;
                }

                $parent = EmailMessage::whereRaw('TRIM(BOTH "<>" FROM message_id) = ?', [$ref])->first();
                if ($parent) {
                    return $parent->thread;
                }
            }
        }

        $normalized = EmailThread::normalizeSubject($subject);
        $thread = EmailThread::where('participant_email', $participantEmail)
            ->where('subject_normalized', $normalized)
            ->first();

        if ($thread) {
            return $thread;
        }

        $student = Student::whereRaw('LOWER(email) = ?', [$participantEmail])->first();

        return EmailThread::create([
            'subject' => $subject ?: $normalized,
            'subject_normalized' => $normalized,
            'participant_email' => $participantEmail,
            'participant_name' => $participantName ?? ($student?->full_name),
            'student_id' => $student?->id,
            'last_message_at' => now(),
            'messages_count' => 0,
            'unread_count' => 0,
            'status' => 'open',
        ]);
    }

    private function resolveFromAddress(): string
    {
        return (string) (
            config('email-inbox.from_address')
            ?: config('mail.from.address')
            ?: env('MAIL_FROM_ADDRESS', 'noreply@up-skills.academy')
        );
    }

    private function resolveFromName(): string
    {
        return (string) (
            config('email-inbox.from_name')
            ?: config('mail.from.name')
            ?: env('MAIL_FROM_NAME', 'Upskills Academy')
        );
    }

    public function recordOutboundMessage(
        EmailThread $thread,
        string $subject,
        string $body,
        string $toEmail,
        ?string $messageId,
        ?string $inReplyTo,
        ?string $references,
        ?int $emailLogId = null,
        array $attachmentMeta = []
    ): EmailMessage {
        return DB::transaction(function () use (
            $thread, $subject, $body, $toEmail, $messageId, $inReplyTo, $references, $emailLogId, $attachmentMeta
        ) {
            $fromEmail = $this->resolveFromAddress();
            $fromName = $this->resolveFromName();

            $message = EmailMessage::create([
                'thread_id' => $thread->id,
                'direction' => 'outbound',
                'from_email' => $fromEmail,
                'from_name' => $fromName,
                'to_email' => strtolower($toEmail),
                'subject' => $subject,
                'body' => $body,
                'message_id' => $this->formatMessageId($messageId),
                'in_reply_to' => $this->formatMessageId($inReplyTo),
                'references' => $references,
                'email_log_id' => $emailLogId,
                'admin_id' => Auth::id(),
                'is_read' => true,
            ]);

            foreach ($attachmentMeta as $attachment) {
                EmailMessageAttachment::create([
                    'email_message_id' => $message->id,
                    'name' => $attachment['name'],
                    'path' => $attachment['path'],
                    'mime' => $attachment['mime'] ?? null,
                    'size' => $attachment['size'] ?? 0,
                ]);
            }

            $thread->update([
                'last_message_at' => now(),
                'messages_count' => $thread->messages()->count(),
            ]);

            return $message;
        });
    }

    public function recordInboundMessage(array $data): ?EmailMessage
    {
        $messageId = $this->formatMessageId($data['message_id'] ?? null);

        if ($messageId && EmailMessage::whereRaw('TRIM(BOTH "<>" FROM message_id) = ?', [$messageId])->exists()) {
            return null;
        }

        $fromEmail = strtolower(trim($data['from_email'] ?? ''));
        if ($fromEmail === '') {
            return null;
        }

        return DB::transaction(function () use ($data, $messageId, $fromEmail) {
            $thread = $this->resolveThread(
                $fromEmail,
                $data['subject'] ?? '(no subject)',
                $messageId,
                $data['in_reply_to'] ?? null,
                $data['references'] ?? null,
                $data['from_name'] ?? null
            );

            $message = EmailMessage::create([
                'thread_id' => $thread->id,
                'direction' => 'inbound',
                'from_email' => $fromEmail,
                'from_name' => $data['from_name'] ?? null,
                'to_email' => strtolower(trim($data['to_email'] ?? $this->resolveFromAddress())),
                'subject' => $data['subject'] ?? $thread->subject,
                'body' => $data['body'] ?? null,
                'body_html' => $data['body_html'] ?? null,
                'message_id' => $messageId,
                'in_reply_to' => $this->formatMessageId($data['in_reply_to'] ?? null),
                'references' => $data['references'] ?? null,
                'is_read' => false,
            ]);

            foreach ($data['attachments'] ?? [] as $attachment) {
                EmailMessageAttachment::create([
                    'email_message_id' => $message->id,
                    'name' => $attachment['name'],
                    'path' => $attachment['path'],
                    'mime' => $attachment['mime'] ?? null,
                    'size' => $attachment['size'] ?? 0,
                ]);
            }

            $thread->update([
                'last_message_at' => now(),
                'messages_count' => $thread->messages()->count(),
                'unread_count' => $thread->messages()->where('direction', 'inbound')->where('is_read', false)->count(),
                'status' => 'open',
                'participant_name' => $data['from_name'] ?? $thread->participant_name,
            ]);

            return $message;
        });
    }

    /**
     * @return array<int, array{name: string, data: string, mime: string}>
     */
    public function prepareOutboundAttachments(Request $request): array
    {
        $fileAttachments = [];

        if (! $request->hasFile('attachments')) {
            return $fileAttachments;
        }

        foreach ($request->file('attachments') as $file) {
            if (! $file instanceof UploadedFile || ! $file->isValid()) {
                continue;
            }

            $contents = @file_get_contents($file->getRealPath());
            if ($contents === false) {
                continue;
            }

            $fileAttachments[] = [
                'name' => $file->getClientOriginalName(),
                'data' => $contents,
                'mime' => $file->getMimeType() ?: 'application/octet-stream',
            ];
        }

        return $fileAttachments;
    }

    /**
     * @return array<int, array{name: string, path: string, mime: string, size: int}>
     */
    public function storeOutboundAttachmentMeta(array $fileAttachments): array
    {
        $stored = [];

        foreach ($fileAttachments as $attachment) {
            $path = 'email-inbox/outbound/' . now()->format('Y/m') . '/' . Str::uuid() . '_' . $attachment['name'];
            Storage::disk('public')->put($path, $attachment['data']);

            $stored[] = [
                'name' => $attachment['name'],
                'path' => $path,
                'mime' => $attachment['mime'],
                'size' => strlen($attachment['data']),
            ];
        }

        return $stored;
    }

    public function buildThreadReferences(EmailThread $thread): array
    {
        return $thread->messages()
            ->whereNotNull('message_id')
            ->orderBy('created_at')
            ->pluck('message_id')
            ->filter()
            ->values()
            ->all();
    }

    public function lastInboundMessageId(EmailThread $thread): ?string
    {
        return $thread->messages()
            ->whereNotNull('message_id')
            ->orderByDesc('created_at')
            ->value('message_id');
    }

    public function syncFromImap(): int
    {
        if (! config('email-inbox.imap.enabled')) {
            throw new \RuntimeException('IMAP is disabled. Set IMAP_ENABLED=true in .env');
        }

        if (! function_exists('imap_open')) {
            throw new \RuntimeException('PHP IMAP extension is not installed.');
        }

        $host = config('email-inbox.imap.host');
        $port = config('email-inbox.imap.port');
        $encryption = config('email-inbox.imap.encryption');
        $username = config('email-inbox.imap.username');
        $password = config('email-inbox.imap.password');
        $folder = config('email-inbox.imap.folder', 'INBOX');

        if (! $host || ! $username || ! $password) {
            throw new \RuntimeException('IMAP credentials are not configured.');
        }

        $flags = '/imap';
        if ($encryption === 'ssl') {
            $flags .= '/ssl';
        } elseif ($encryption === 'tls') {
            $flags .= '/tls';
        }

        $mailbox = sprintf('{%s:%s%s}%s', $host, $port, $flags, $folder);
        $connection = @imap_open($mailbox, $username, $password);

        if (! $connection) {
            throw new \RuntimeException('IMAP connection failed: ' . imap_last_error());
        }

        $imported = 0;
        $emails = imap_search($connection, 'UNSEEN') ?: [];

        foreach ($emails as $emailNumber) {
            try {
                $overview = imap_fetch_overview($connection, (string) $emailNumber, 0)[0] ?? null;
                if (! $overview) {
                    continue;
                }

                $structure = imap_fetchstructure($connection, $emailNumber);
                $body = $this->extractImapBody($connection, $emailNumber, $structure);
                $headers = imap_fetchheader($connection, $emailNumber);

                $messageId = $overview->message_id ?? null;
                $inReplyTo = $this->parseHeaderValue($headers, 'In-Reply-To');
                $references = $this->parseHeaderValue($headers, 'References');
                $from = $overview->from ?? '';
                [$fromEmail, $fromName] = $this->parseFromAddress($from);

                $message = $this->recordInboundMessage([
                    'from_email' => $fromEmail,
                    'from_name' => $fromName,
                    'to_email' => $overview->to ?? $this->resolveFromAddress(),
                    'subject' => imap_utf8($overview->subject ?? '(no subject)'),
                    'body' => $body['text'],
                    'body_html' => $body['html'],
                    'message_id' => $messageId,
                    'in_reply_to' => $inReplyTo,
                    'references' => $references,
                    'attachments' => $this->extractImapAttachments($connection, $emailNumber, $structure),
                ]);

                if ($message) {
                    $imported++;
                    imap_setflag_full($connection, (string) $emailNumber, '\\Seen');
                }
            } catch (\Throwable $e) {
                Log::warning('IMAP message import failed: ' . $e->getMessage());
            }
        }

        imap_close($connection);

        return $imported;
    }

    private function parseFromAddress(string $from): array
    {
        if (preg_match('/^(?:"?([^"]*)"?\s)?<?([^>]+@[^>]+)>?$/', $from, $matches)) {
            return [strtolower(trim($matches[2])), trim($matches[1] ?? '') ?: null];
        }

        return [strtolower(trim($from)), null];
    }

    private function parseHeaderValue(string $headers, string $name): ?string
    {
        if (preg_match('/^' . preg_quote($name, '/') . ':\s*(.+)$/im', $headers, $matches)) {
            return trim(preg_replace('/\s+/', ' ', $matches[1]));
        }

        return null;
    }

    private function extractImapBody($connection, int $emailNumber, $structure): array
    {
        $text = null;
        $html = null;

        if (! isset($structure->parts)) {
            $body = imap_body($connection, $emailNumber);
            $text = $this->decodeImapPart($body, $structure->encoding ?? 0);

            return ['text' => $text, 'html' => null];
        }

        foreach ($structure->parts as $index => $part) {
            $partNumber = (string) ($index + 1);
            $body = imap_fetchbody($connection, $emailNumber, $partNumber);
            $decoded = $this->decodeImapPart($body, $part->encoding ?? 0);
            $type = strtolower($part->subtype ?? 'plain');

            if ($type === 'html') {
                $html = $decoded;
            } elseif ($type === 'plain' && $text === null) {
                $text = $decoded;
            }
        }

        return ['text' => $text, 'html' => $html];
    }

    private function decodeImapPart(?string $body, int $encoding): ?string
    {
        if ($body === null) {
            return null;
        }

        return match ($encoding) {
            ENCBASE64 => base64_decode($body),
            ENCQUOTEDPRINTABLE => quoted_printable_decode($body),
            default => $body,
        };
    }

    private function extractImapAttachments($connection, int $emailNumber, $structure): array
    {
        $attachments = [];

        if (! isset($structure->parts)) {
            return $attachments;
        }

        foreach ($structure->parts as $index => $part) {
            if (empty($part->ifdparameters)) {
                continue;
            }

            $isAttachment = false;
            $filename = 'attachment';

            foreach ($part->dparameters as $parameter) {
                if (strtolower($parameter->attribute) === 'filename') {
                    $filename = imap_utf8($parameter->value);
                    $isAttachment = true;
                }
            }

            if (! $isAttachment) {
                continue;
            }

            $partNumber = (string) ($index + 1);
            $body = imap_fetchbody($connection, $emailNumber, $partNumber);
            $decoded = $this->decodeImapPart($body, $part->encoding ?? 0);

            if ($decoded === null) {
                continue;
            }

            $path = 'email-inbox/inbound/' . now()->format('Y/m') . '/' . Str::uuid() . '_' . $filename;
            Storage::disk('public')->put($path, $decoded);

            $attachments[] = [
                'name' => $filename,
                'path' => $path,
                'mime' => strtolower($part->subtype ?? 'octet-stream'),
                'size' => strlen($decoded),
            ];
        }

        return $attachments;
    }

    public function parseWebhookPayload(array $payload): array
    {
        if (isset($payload['items'][0])) {
            $item = $payload['items'][0];
            $payload = array_merge($payload, $item);
        }

        return [
            'from_email' => $payload['from'] ?? $payload['from_email'] ?? $payload['sender'] ?? null,
            'from_name' => $payload['from_name'] ?? $payload['sender_name'] ?? null,
            'to_email' => $payload['to'] ?? $payload['to_email'] ?? $payload['recipient'] ?? $this->resolveFromAddress(),
            'subject' => $payload['subject'] ?? '(no subject)',
            'body' => $payload['text'] ?? $payload['body'] ?? strip_tags($payload['html'] ?? ''),
            'body_html' => $payload['html'] ?? $payload['body_html'] ?? null,
            'message_id' => $payload['message_id'] ?? $payload['MessageId'] ?? null,
            'in_reply_to' => $payload['in_reply_to'] ?? $payload['InReplyTo'] ?? null,
            'references' => $payload['references'] ?? $payload['References'] ?? null,
            'attachments' => $this->parseWebhookAttachments($payload['attachments'] ?? []),
        ];
    }

    private function parseWebhookAttachments(array $attachments): array
    {
        $stored = [];

        foreach ($attachments as $attachment) {
            $name = $attachment['name'] ?? $attachment['filename'] ?? 'attachment';
            $content = $attachment['content'] ?? null;

            if (! $content) {
                continue;
            }

            $binary = base64_decode($content, true) ?: $content;
            $path = 'email-inbox/inbound/' . now()->format('Y/m') . '/' . Str::uuid() . '_' . $name;
            Storage::disk('public')->put($path, $binary);

            $stored[] = [
                'name' => $name,
                'path' => $path,
                'mime' => $attachment['mime'] ?? $attachment['content_type'] ?? null,
                'size' => strlen($binary),
            ];
        }

        return $stored;
    }
}
