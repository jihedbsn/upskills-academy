<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class StudentMessageService
{
    public function inbox()
    {
        $studentId = Auth::id();

        $messages = DB::connection('mysql_admin')->table('message_recipients')
            ->join('internal_messages', 'message_recipients.message_id', '=', 'internal_messages.id')
            ->where('message_recipients.student_id', $studentId)
            ->select(
                'internal_messages.id',
                'internal_messages.subject',
                'internal_messages.body',
                'internal_messages.attachments',
                'internal_messages.created_at',
                'message_recipients.is_read',
                'message_recipients.read_at',
                DB::raw('(
                    SELECT COUNT(*)
                    FROM admin_responses ar
                    INNER JOIN message_responses mr ON ar.message_response_id = mr.id
                    WHERE mr.message_id = internal_messages.id
                      AND mr.student_id = ' . (int) $studentId . '
                      AND ar.is_read = 0
                ) as unread_admin_replies')
            )
            ->orderBy('internal_messages.created_at', 'desc')
            ->paginate(15);

        foreach ($messages as $message) {
            $message->is_unread = !$message->is_read || ($message->unread_admin_replies ?? 0) > 0;
        }

        $unreadCount = $this->getUnreadCountValue($studentId);

        return view('student.messages.inbox', compact('messages', 'unreadCount'));
    }

    public function show($locale, $id)
    {
        $studentId = Auth::id();

        $message = DB::connection('mysql_admin')->table('message_recipients')
            ->join('internal_messages', 'message_recipients.message_id', '=', 'internal_messages.id')
            ->where('message_recipients.student_id', $studentId)
            ->where('internal_messages.id', $id)
            ->select(
                'internal_messages.id',
                'internal_messages.subject',
                'internal_messages.body',
                'internal_messages.attachments',
                'internal_messages.created_at',
                'message_recipients.is_read',
                'message_recipients.read_at'
            )
            ->first();

        if (!$message) {
            abort(404);
        }

        // Mark as read if not already read
        if (!$message->is_read) {
            DB::connection('mysql_admin')->table('message_recipients')
                ->where('student_id', $studentId)
                ->where('message_id', $id)
                ->update([
                    'is_read' => 1,
                    'read_at' => now()
                ]);
        }

        // Decode attachments
        $message->attachments = json_decode($message->attachments, true);

        // Get student's previous responses with admin replies
        $responses = DB::connection('mysql_admin')
            ->table('message_responses')
            ->where('message_id', $id)
            ->where('student_id', $studentId)
            ->orderBy('created_at', 'desc')
            ->get();

        // For each response, get admin responses
        foreach ($responses as $response) {
            // admin_id references admins.id (admin panel uses Admin model), not users.id
            $response->admin_responses = DB::connection('mysql_admin')
                ->table('admin_responses')
                ->leftJoin('admins', 'admin_responses.admin_id', '=', 'admins.id')
                ->where('admin_responses.message_response_id', $response->id)
                ->select(
                    'admin_responses.*',
                    DB::raw("COALESCE(NULLIF(TRIM(CONCAT(COALESCE(admins.first_name, ''), ' ', COALESCE(admins.last_name, ''))), ''), 'Administrator') as admin_name")
                )
                ->orderBy('admin_responses.created_at', 'asc')
                ->get();

            // Mark all admin responses as read for this student's message response
            DB::connection('mysql_admin')
                ->table('admin_responses')
                ->where('message_response_id', $response->id)
                ->where('is_read', 0)
                ->update(['is_read' => 1]);
        }

        return view('student.messages.show', compact('message', 'responses'));
    }

    public function markAsRead($locale, $id)
    {
        $studentId = Auth::id();

        DB::connection('mysql_admin')->table('message_recipients')
            ->where('student_id', $studentId)
            ->where('message_id', $id)
            ->update([
                'is_read' => 1,
                'read_at' => now()
            ]);

        DB::connection('mysql_admin')
            ->table('admin_responses')
            ->whereIn('message_response_id', DB::connection('mysql_admin')
                ->table('message_responses')
                ->select('id')
                ->where('student_id', $studentId)
                ->where('message_id', $id))
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => $this->getUnreadCountValue($studentId),
            ]);
        }

        return redirect()->back()->with('success', 'Message marked as read');
    }

    public function markAsUnread($locale, $id)
    {
        $studentId = Auth::id();

        DB::connection('mysql_admin')->table('message_recipients')
            ->where('student_id', $studentId)
            ->where('message_id', $id)
            ->update([
                'is_read' => 0,
                'read_at' => null
            ]);

        return redirect()->back()->with('success', 'Message marked as unread');
    }

    public function getUnreadCount()
    {
        $studentId = Auth::id();

        return response()->json([
            'count' => $this->getUnreadCountValue($studentId),
        ]);
    }

    public function syncStatus()
    {
        $studentId = Auth::id();

        $rows = DB::connection('mysql_admin')->table('message_recipients')
            ->join('internal_messages', 'message_recipients.message_id', '=', 'internal_messages.id')
            ->where('message_recipients.student_id', $studentId)
            ->select(
                'internal_messages.id',
                'message_recipients.is_read',
                DB::raw('(
                    SELECT COUNT(*)
                    FROM admin_responses ar
                    INNER JOIN message_responses mr ON ar.message_response_id = mr.id
                    WHERE mr.message_id = internal_messages.id
                      AND mr.student_id = ' . (int) $studentId . '
                      AND ar.is_read = 0
                ) as unread_admin_replies')
            )
            ->orderByDesc('internal_messages.created_at')
            ->get();

        $messages = $rows->map(fn ($row) => [
            'id' => $row->id,
            'is_unread' => !$row->is_read || ($row->unread_admin_replies ?? 0) > 0,
            'unread_admin_replies' => (int) ($row->unread_admin_replies ?? 0),
        ]);

        return response()->json([
            'success' => true,
            'unread_count' => $this->getUnreadCountValue($studentId),
            'messages' => $messages,
        ]);
    }

    private function getUnreadCountValue(int $studentId): int
    {
        $unreadAdminMessages = DB::connection('mysql_admin')->table('message_recipients')
            ->where('student_id', $studentId)
            ->where('is_read', 0)
            ->count();

        $unreadAdminResponses = DB::connection('mysql_admin')->table('message_responses as mr')
            ->join('admin_responses as ar', 'mr.id', '=', 'ar.message_response_id')
            ->where('mr.student_id', $studentId)
            ->where('ar.is_read', 0)
            ->count();

        return $unreadAdminMessages + $unreadAdminResponses;
    }

    /**
     * Store student response to a message
     */
    public function storeResponse(Request $request, $locale, $id)
    {
        $request->validate([
            'response_body' => 'required|string|max:5000',
            'response_attachments.*' => 'file|max:10240' // 10MB max per file
        ]);

        $studentId = Auth::id();

        // Verify student is a recipient of this message
        $recipient = DB::connection('mysql_admin')
            ->table('message_recipients')
            ->where('message_id', $id)
            ->where('student_id', $studentId)
            ->first();

        if (!$recipient) {
            abort(403, 'You are not authorized to respond to this message');
        }

        try {
            // Handle file uploads
            $attachmentPaths = [];
            if ($request->hasFile('response_attachments')) {
                foreach ($request->file('response_attachments') as $file) {
                    $path = $file->store('message-responses', 'public');
                    $attachmentPaths[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize()
                    ];
                }
            }

            // Store response in admin database
            DB::connection('mysql_admin')->table('message_responses')->insert([
                'message_id' => $id,
                'student_id' => $studentId,
                'response_body' => $request->response_body,
                'response_attachments' => json_encode($attachmentPaths),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Get student info
            $student = DB::table('students')
                ->where('id', $studentId)
                ->select('id', 'first_name', 'last_name', 'email')
                ->first();

            // Get message info
            $message = DB::connection('mysql_admin')
                ->table('internal_messages')
                ->where('id', $id)
                ->select('id', 'subject', 'sender_admin_id')
                ->first();

            // sender_admin_id references admins.id (not users)
            $admin = DB::connection('mysql_admin')
                ->table('admins')
                ->where('id', $message->sender_admin_id)
                ->select('email', 'first_name', 'last_name')
                ->first();

            // Send email notification to admin
            if ($admin && $admin->email) {
                try {
                    $adminDisplayName = trim(($admin->first_name ?? '') . ' ' . ($admin->last_name ?? ''));
                    Mail::send('emails.student-response-notification', [
                        'studentName' => $student->first_name . ' ' . $student->last_name,
                        'studentEmail' => $student->email,
                        'messageSubject' => $message->subject,
                        'responseBody' => $request->response_body,
                        'messageId' => $id,
                        'adminName' => $adminDisplayName !== '' ? $adminDisplayName : 'Administrator'
                    ], function ($mail) use ($admin, $message) {
                        $mail->to($admin->email)
                            ->subject('Student Response: ' . $message->subject);
                    });
                } catch (\Exception $e) {
                    Log::error("Failed to send notification to admin: " . $e->getMessage());
                }
            }

            return redirect()->back()->with('success', __('messages.response_sent_successfully'));

        } catch (\Exception $e) {
            Log::error('Error storing student response: ' . $e->getMessage());
            return back()->with('error', __('messages.failed_to_send_response'))->withInput();
        }
    }
}
