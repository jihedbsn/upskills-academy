<?php

namespace App\Http\Controllers;

use App\Models\EmailThread;
use App\Services\EmailInboxService;
use App\Services\EmailService;
use Illuminate\Http\Request;

class EmailInboxController extends Controller
{
    public function __construct(
        protected EmailInboxService $inboxService,
        protected EmailService $emailService
    ) {}

    public function index(Request $request)
    {
        $threads = $this->inboxService->listThreads(
            $request->get('search'),
            $request->get('filter')
        );

        return view('emails.inbox.index', [
            'threads' => $threads,
            'unreadCount' => $this->inboxService->unreadCount(),
            'search' => $request->get('search'),
            'filter' => $request->get('filter', 'open'),
        ]);
    }

    public function show(EmailThread $thread)
    {
        $this->inboxService->markThreadAsRead($thread);

        return view('emails.inbox.show', [
            'thread' => $this->inboxService->getThread($thread),
        ]);
    }

    public function reply(Request $request, EmailThread $thread)
    {
        $request->merge([
            'email' => $thread->participant_email,
            'subject' => $thread->replySubject(),
            'thread_id' => $thread->id,
        ]);

        return $this->emailService->send($request);
    }

    public function sync()
    {
        try {
            $imported = $this->inboxService->syncFromImap();

            return redirect()->route('emails.inbox.index')
                ->with('success', $imported . ' nouveau(x) message(s) importé(s) depuis la boîte mail.');
        } catch (\Exception $e) {
            return redirect()->route('emails.inbox.index')
                ->with('error', 'Synchronisation IMAP impossible : ' . $e->getMessage());
        }
    }

    public function close(EmailThread $thread)
    {
        $thread->update(['status' => 'closed']);

        return redirect()->route('emails.inbox.index')
            ->with('success', 'Conversation fermée.');
    }

    public function reopen(EmailThread $thread)
    {
        $thread->update(['status' => 'open']);

        return redirect()->route('emails.inbox.show', $thread)
            ->with('success', 'Conversation rouverte.');
    }
}
