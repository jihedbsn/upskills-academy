<?php

namespace App\Services;

use App\Models\ContactMessage;
use App\Services\OutboundEmailLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactMessageService
{
    public function index(Request $request)
    {
        $query = ContactMessage::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($queryBuilder) use ($search) {
                $queryBuilder->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->where('is_read', false);
            } elseif ($request->status === 'read') {
                $query->where('is_read', true);
            } elseif ($request->status === 'responded') {
                $query->whereNotNull('responded_at');
            }
        }

        $messages = $query->orderBy('created_at', 'desc')->paginate(15);
        $unreadCount = ContactMessage::unread()->count();

        return view('contact-messages.index', compact('messages', 'unreadCount'));
    }

    public function show(ContactMessage $contactMessage)
    {
        if (!$contactMessage->is_read) {
            $contactMessage->markAsRead(Auth::id());
        }

        return view('contact-messages.show', compact('contactMessage'));
    }

    public function respond(Request $request, ContactMessage $contactMessage)
    {
        $request->validate([
            'response' => 'required|string|max:10000',
        ], [
            'response.required' => 'La réponse est obligatoire.',
        ]);

        try {
            $contactMessage->update([
                'admin_response' => $request->response,
                'responded_at' => now(),
                'responded_by' => Auth::id(),
            ]);

            $subject = 'Re: ' . ($contactMessage->subject ?: 'Your Message to Upskills Academy');

            try {
                Mail::send('emails.contact-response', [
                    'contactMessage' => $contactMessage,
                    'response' => $request->response,
                ], function ($message) use ($contactMessage, $subject) {
                    $message->to($contactMessage->email, $contactMessage->name)
                        ->subject($subject);
                });

                OutboundEmailLogger::logSent(
                    $contactMessage->email,
                    'contact_response',
                    $subject,
                    relatedModel: 'ContactMessage',
                    relatedId: $contactMessage->id,
                    body: $request->response
                );
            } catch (\Exception $e) {
                Log::error('Failed to send contact response email: ' . $e->getMessage());

                OutboundEmailLogger::logFailed(
                    $contactMessage->email,
                    'contact_response',
                    $subject,
                    $e->getMessage(),
                    relatedModel: 'ContactMessage',
                    relatedId: $contactMessage->id,
                    body: $request->response
                );

                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Réponse enregistrée mais l\'email n\'a pas pu être envoyé.'
                    ], 500);
                }

                return redirect()->back()
                    ->with('warning', 'Réponse enregistrée mais l\'email n\'a pas pu être envoyé.');
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Réponse envoyée avec succès!'
                ]);
            }

            return redirect()->route('contact-messages.show', $contactMessage)
                ->with('success', 'Réponse envoyée avec succès!');
        } catch (\Exception $e) {
            Log::error('Error responding to contact message: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de l\'envoi de la réponse.'
                ], 500);
            }

            return redirect()->back()->with('error', 'Erreur lors de l\'envoi de la réponse.');
        }
    }

    public function markAsRead(Request $request, ContactMessage $contactMessage)
    {
        $contactMessage->markAsRead(Auth::id());

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Message marqué comme lu.'
            ]);
        }

        return redirect()->back()->with('success', 'Message marqué comme lu.');
    }

    public function markAsUnread(Request $request, ContactMessage $contactMessage)
    {
        $contactMessage->update([
            'is_read' => false,
            'read_at' => null,
            'read_by' => null,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Message marqué comme non lu.'
            ]);
        }

        return redirect()->back()->with('success', 'Message marqué comme non lu.');
    }

    public function destroy(Request $request, ContactMessage $contactMessage)
    {
        try {
            $contactMessage->delete();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Message supprimé avec succès!'
                ]);
            }

            return redirect()->route('contact-messages.index')
                ->with('success', 'Message supprimé avec succès!');
        } catch (\Exception $e) {
            Log::error('Error deleting contact message: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la suppression.'
                ], 500);
            }

            return redirect()->back()->with('error', 'Erreur lors de la suppression.');
        }
    }

    public function getUnreadCount()
    {
        return response()->json([
            'count' => ContactMessage::unread()->count()
        ]);
    }

    public function bulkMarkAsRead(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun message sélectionné.'
            ], 400);
        }

        ContactMessage::whereIn('id', $ids)->update([
            'is_read' => true,
            'read_at' => now(),
            'read_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => count($ids) . ' message(s) marqué(s) comme lu(s).'
        ]);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun message sélectionné.'
            ], 400);
        }

        ContactMessage::whereIn('id', $ids)->delete();

        return response()->json([
            'success' => true,
            'message' => count($ids) . ' message(s) supprimé(s).'
        ]);
    }
}
