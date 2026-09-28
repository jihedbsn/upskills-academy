<?php

namespace App\Services;

use App\Mail\CustomEmail;
use App\Models\EmailLog;
use App\Models\EmailThread;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class EmailService
{
    private const ALLOWED_ATTACHMENT_EXTENSIONS = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];

    private const MAX_ATTACHMENTS = 10;

    private const MAX_ATTACHMENT_SIZE_KB = 10240;

    public function __construct(
        protected EmailInboxService $inboxService
    ) {}

    /**
     * Mail drivers that never deliver to a real inbox (no SMTP/API send).
     */
    private function mailDeliversExternally(): bool
    {
        $driver = config('mail.default', 'log');

        return ! in_array($driver, ['log', 'array'], true);
    }

    public function index()
    {
        return view('emails.send', [
            'mailDriver' => config('mail.default', 'log'),
            'mailDeliversExternally' => $this->mailDeliversExternally(),
            'prefillEmail' => request('email'),
        ]);
    }

    public function send(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'thread_id' => 'nullable|exists:email_threads,id',
            'attachments' => 'nullable|array|max:' . self::MAX_ATTACHMENTS,
            'attachments.*' => [
                'file',
                'max:' . self::MAX_ATTACHMENT_SIZE_KB,
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! $value instanceof UploadedFile) {
                        return;
                    }

                    $extension = strtolower($value->getClientOriginalExtension());
                    if (! in_array($extension, self::ALLOWED_ATTACHMENT_EXTENSIONS, true)) {
                        $fail('Format de fichier non supporté. Utilisez PDF, JPG, PNG, DOC ou DOCX.');
                    }
                },
            ],
        ]);

        try {
            $email = strtolower(trim($request->input('email')));
            $subject = $request->input('subject');
            $content = $request->input('content');
            $trackingToken = Str::random(40);
            $trackingPixelUrl = URL::route('email-tracking.pixel', ['token' => $trackingToken], true);
            $trackingConfirmUrl = URL::route('email-tracking.confirm', ['token' => $trackingToken], true);

            $fileAttachments = $this->prepareAttachments($request);
            $attachmentMeta = $this->inboxService->storeOutboundAttachmentMeta($fileAttachments);

            $thread = null;
            if ($request->filled('thread_id')) {
                $thread = EmailThread::findOrFail($request->input('thread_id'));
            } else {
                $student = Student::whereRaw('LOWER(email) = ?', [$email])->first();
                $thread = $this->inboxService->resolveThread(
                    $email,
                    $subject,
                    null,
                    null,
                    null,
                    $student ? ($student->first_name . ' ' . $student->last_name) : null
                );
            }

            $generatedMessageId = $this->inboxService->generateMessageId();
            $inReplyTo = $thread ? $this->inboxService->lastInboundMessageId($thread) : null;
            $references = $thread ? $this->inboxService->buildThreadReferences($thread) : [];

            Mail::to($email)->send(new CustomEmail(
                $subject,
                $content,
                $trackingPixelUrl,
                $trackingConfirmUrl,
                $fileAttachments,
                $generatedMessageId,
                $inReplyTo,
                $references
            ));

            $student = Student::whereRaw('LOWER(email) = ?', [$email])->first();
            $emailLog = EmailLog::logSent(
                $email,
                'custom_email',
                $subject,
                $student->id ?? null,
                $student ? ($student->first_name . ' ' . $student->last_name) : null,
                null,
                null,
                $content,
                $trackingToken
            );

            $this->inboxService->recordOutboundMessage(
                $thread,
                $subject,
                $content,
                $email,
                $this->inboxService->formatMessageId($generatedMessageId),
                $inReplyTo,
                implode(' ', $references),
                $emailLog->id,
                $attachmentMeta
            );

            $attachmentCount = count($fileAttachments);
            $successMessage = 'Demande d’envoi traitée avec succès pour ' . $email . ' !';
            if ($attachmentCount > 0) {
                $successMessage .= ' (' . $attachmentCount . ' pièce(s) jointe(s) incluse(s).)';
            } elseif ($request->hasFile('attachments')) {
                $successMessage .= ' Attention : aucune pièce jointe n’a pu être lue. Vérifiez la taille des fichiers (max 10 Mo).';
            }

            if ($request->filled('thread_id')) {
                return redirect()->route('emails.inbox.show', $thread)
                    ->with('success', $successMessage);
            }

            $redirect = redirect()->back()->with('success', $successMessage);

            if (! $this->mailDeliversExternally()) {
                $driver = config('mail.default', 'log');
                $redirect->with(
                    'warning',
                    'Le courrier est configuré avec MAIL_MAILER=' . $driver . ' : aucun email n’est expédié vers Internet. '
                        . 'Le message est enregistré dans les journaux Laravel (et dans les logs d’emails). '
                        . 'Pour une livraison réelle, configurez MAIL_MAILER=smtp (ou un autre transport) et les variables SMTP dans le fichier .env.'
                );
            }

            return $redirect;
        } catch (\Exception $e) {
            $email = $request->input('email');
            $subject = $request->input('subject');
            $student = Student::where('email', $email)->first();
            EmailLog::logFailed(
                $email,
                'custom_email',
                $subject,
                $e->getMessage(),
                $student->id ?? null,
                $student ? ($student->first_name . ' ' . $student->last_name) : null,
                null,
                null,
                $request->input('content'),
                null
            );

            if ($request->filled('thread_id')) {
                return redirect()->route('emails.inbox.show', $request->input('thread_id'))
                    ->with('error', 'Erreur lors de l\'envoi de l\'email : ' . $e->getMessage());
            }

            return redirect()->back()->with('error', 'Erreur lors de l\'envoi de l\'email : ' . $e->getMessage());
        }
    }

    /**
     * @return array<int, array{name: string, data: string, mime: string}>
     */
    private function prepareAttachments(Request $request): array
    {
        return $this->inboxService->prepareOutboundAttachments($request);
    }
}
