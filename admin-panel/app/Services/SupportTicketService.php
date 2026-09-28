<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\ResponseTicket;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SupportTicketService
{
    public function index(Request $request): array
    {
        $query = Ticket::with(['student', 'responses']);

        if ($request->filled('status')) {
            if ($request->status == '1') {
                $query->completed();
            } elseif ($request->status == '0') {
                $query->open();
            }
        }

        if ($request->filled('student')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->student . '%')
                  ->orWhere('last_name', 'like', '%' . $request->student . '%')
                  ->orWhere('email', 'like', '%' . $request->student . '%');
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(15);

        $stats = [
            'total'      => Ticket::count(),
            'open'       => Ticket::open()->count(),
            'resolved'   => Ticket::completed()->count(),
            'this_month' => Ticket::whereMonth('created_at', now()->month)
                                   ->whereYear('created_at', now()->year)
                                   ->count(),
        ];

        return compact('tickets', 'stats');
    }

    public function show($id): Ticket
    {
        return Ticket::with(['student', 'responses' => function ($query) {
            $query->orderBy('created_at', 'asc');
        }])->findOrFail($id);
    }

    public function respond(Request $request, $id)
    {
        $request->validate(['message' => 'required|string|max:5000']);

        $ticket = Ticket::with('student')->findOrFail($id);

        $response = ResponseTicket::create([
            'ticket_id'  => $ticket->id,
            'student_id' => null,
            'message'    => $request->message,
            'isAdmin'    => true,
        ]);

        $ticketUrl         = rtrim((string) config('app.user_url'), '/');
        $ticketLocale      = config('app.locale', 'ar');
        if (!in_array($ticketLocale, ['ar', 'en', 'fr'], true)) {
            $ticketLocale = 'ar';
        }
        $studentSupportUrl = $ticketUrl . '/' . $ticketLocale . '/student-dashboard/support';

        try {
            Notification::notifyStudent(
                $ticket->student->id,
                Notification::TYPE_TICKET,
                'New reply to your support ticket',
                'Support replied to ticket #TKT-' . str_pad($ticket->id, 3, '0', STR_PAD_LEFT),
                $studentSupportUrl,
                [
                    'ticket_id'   => $ticket->id,
                    'response_id' => $response->id,
                    'subject'     => $ticket->sujet,
                ],
                '🎫',
                'blue',
                true
            );
        } catch (\Exception $e) {
            Log::error('Failed to create in-app ticket notification: ' . $e->getMessage(), [
                'ticket_id'  => $ticket->id,
                'student_id' => $ticket->student->id ?? null,
            ]);
        }

        $emailSent = false;
        try {
            Mail::to($ticket->student->email)->send(new \App\Mail\AdminTicketResponse($ticket, $request->message));
            $emailSent = true;

            \App\Models\EmailLog::logSent(
                $ticket->student->email,
                'ticket_response',
                'Support Ticket Response #' . $ticket->id,
                $ticket->student->id,
                ($ticket->student->first_name ?? '') . ' ' . ($ticket->student->last_name ?? ''),
                'Ticket',
                $ticket->id
            );
        } catch (\Exception $e) {
            Log::error('Failed to send ticket response email: ' . $e->getMessage());

            \App\Models\EmailLog::logFailed(
                $ticket->student->email ?? 'unknown',
                'ticket_response',
                'Support Ticket Response #' . $ticket->id,
                $e->getMessage(),
                $ticket->student->id ?? null,
                ($ticket->student->first_name ?? '') . ' ' . ($ticket->student->last_name ?? ''),
                'Ticket',
                $ticket->id
            );
        }

        $flashMessage = $emailSent
            ? 'Réponse ajoutée avec succès. Email + notification in-app envoyés à l\'étudiant.'
            : 'Réponse ajoutée avec succès. Notification in-app envoyée, mais l\'email a échoué (voir logs).';

        return redirect()->route('admin.support-tickets.show', $ticket->id)->with('success', $flashMessage);
    }

    public function toggleStatus($id)
    {
        try {
            $ticket = Ticket::with('student')->findOrFail($id);
            $ticket->update(['ticket_iscomplet' => !$ticket->ticket_iscomplet]);

            $status = $ticket->ticket_iscomplet ? 'résolu' : 'réouvert';

            try {
                Mail::to($ticket->student->email)->send(new \App\Mail\TicketStatusChanged($ticket));

                \App\Models\EmailLog::logSent(
                    $ticket->student->email,
                    'ticket_status_changed',
                    'Ticket Status Changed #' . $ticket->id,
                    $ticket->student->id,
                    ($ticket->student->first_name ?? '') . ' ' . ($ticket->student->last_name ?? ''),
                    'Ticket',
                    $ticket->id
                );
            } catch (\Exception $e) {
                Log::error('Failed to send ticket status change email: ' . $e->getMessage());

                \App\Models\EmailLog::logFailed(
                    $ticket->student->email ?? 'unknown',
                    'ticket_status_changed',
                    'Ticket Status Changed #' . $ticket->id,
                    $e->getMessage(),
                    $ticket->student->id ?? null,
                    ($ticket->student->first_name ?? '') . ' ' . ($ticket->student->last_name ?? ''),
                    'Ticket',
                    $ticket->id
                );
            }

            return response()->json([
                'success'    => true,
                'message'    => "Ticket {$status} avec succès.",
                'new_status' => $ticket->ticket_iscomplet,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification du statut.',
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            $ticket = Ticket::findOrFail($id);
            $ticket->responses()->delete();
            $ticket->delete();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression.',
            ]);
        }
    }
}
