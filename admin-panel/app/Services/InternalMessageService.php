<?php

namespace App\Services;

use App\Mail\InternalMessageNotification;
use App\Models\AdminResponse;
use App\Support\StudentFrontendUrl;
use App\Models\InternalMessage;
use App\Models\MessageRecipient;
use App\Models\MessageResponse;
use App\Models\Product;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InternalMessageService
{
    public function index()
    {
        $messages = InternalMessage::with('recipients')
            ->withCount([
                'responses as unread_responses_count' => fn ($q) => $q->where('is_read_by_admin', false),
                'responses as total_responses_count',
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $totalUnreadResponses = MessageResponse::where('is_read_by_admin', false)->count();

        return view('internal-messages.index', compact('messages', 'totalUnreadResponses'));
    }

    public function create()
    {
        $courses = Product::where('statut', 1)
            ->orderBy('id', 'desc')
            ->get();

        return view('internal-messages.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'recipient_type' => 'required|in:students,courses',
            'student_ids' => 'required_if:recipient_type,students|array',
            'student_ids.*' => 'exists:students,id',
            'course_ids' => 'required_if:recipient_type,courses|array',
            'course_ids.*' => 'exists:products,id',
            'attachments.*' => 'file|max:10240'
        ]);

        try {
            DB::beginTransaction();

            $attachmentPaths = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('internal-messages', 'public');
                    $attachmentPaths[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize()
                    ];
                }
            }

            $message = InternalMessage::create([
                'subject' => $request->subject,
                'body' => $request->body,
                'attachments' => $attachmentPaths,
                'sender_admin_id' => Auth::id()
            ]);

            $studentIds = [];
            if ($request->recipient_type === 'students') {
                $studentIds = $request->student_ids ?? [];
            } else {
                $courseIds = $request->course_ids ?? [];

                if (!empty($courseIds)) {
                    $studentIds = DB::table('product_students')
                        ->whereIn('product_id', $courseIds)
                        ->where('is_active', 1)
                        ->distinct()
                        ->pluck('student_id')
                        ->toArray();

                    $studentIds = array_filter($studentIds, function ($id) {
                        return !is_null($id) && $id > 0;
                    });
                }
            }

            if (empty($studentIds)) {
                DB::rollback();
                return back()->with('error', 'No students found for the selected courses. Please ensure students are enrolled in these courses.')->withInput();
            }

            $studentIds = array_unique($studentIds);

            foreach ($studentIds as $studentId) {
                MessageRecipient::create([
                    'message_id' => $message->id,
                    'student_id' => $studentId
                ]);
            }

            $students = DB::connection('mysql')->table('students')
                ->whereIn('id', $studentIds)
                ->select('id', 'first_name', 'last_name', 'email')
                ->get();

            foreach ($students as $student) {
                try {
                    Mail::to($student->email)->queue(
                        new InternalMessageNotification($message->id, $student, app()->getLocale())
                    );

                    \App\Models\EmailLog::logSent(
                        $student->email,
                        'internal_message',
                        'Internal Message: ' . Str::limit($message->subject ?? 'New Message', 50),
                        $student->id,
                        ($student->first_name ?? '') . ' ' . ($student->last_name ?? ''),
                        'InternalMessage',
                        $message->id
                    );
                } catch (\Exception $e) {
                    Log::error("Failed to send notification to {$student->email}: " . $e->getMessage());

                    \App\Models\EmailLog::logFailed(
                        $student->email,
                        'internal_message',
                        'Internal Message: ' . Str::limit($message->subject ?? 'New Message', 50),
                        $e->getMessage(),
                        $student->id,
                        ($student->first_name ?? '') . ' ' . ($student->last_name ?? ''),
                        'InternalMessage',
                        $message->id
                    );
                }
            }

            DB::commit();

            return redirect()->route('internal-messages.index')
                ->with('success', "Message sent successfully to {$message->total_recipients} student(s)!");
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error sending internal message: ' . $e->getMessage());
            return back()->with('error', 'Failed to send message: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $message = InternalMessage::with(['recipients.student'])->findOrFail($id);
        $recipients = $message->recipients;

        MessageResponse::markAllAsReadByAdminForMessage((int) $message->id);

        return view('internal-messages.show', compact('message', 'recipients'));
    }

    public function searchStudents(Request $request)
    {
        $query = $request->get('q');

        $students = Student::where(function ($q) use ($query) {
            $q->where('first_name', 'LIKE', "%{$query}%")
                ->orWhere('last_name', 'LIKE', "%{$query}%")
                ->orWhere('email', 'LIKE', "%{$query}%");
        })
            ->select('id', 'first_name', 'last_name', 'email')
            ->limit(20)
            ->get();

        return response()->json($students);
    }

    public function storeAdminResponse(Request $request, $responseId)
    {
        $request->validate([
            'response_body' => 'required|string|max:5000',
            'response_attachments.*' => 'file|max:10240'
        ]);

        try {
            $studentResponse = MessageResponse::with(['student', 'message'])->findOrFail($responseId);

            $attachmentPaths = [];
            if ($request->hasFile('response_attachments')) {
                foreach ($request->file('response_attachments') as $file) {
                    $path = $file->store('admin-responses', 'public');
                    $attachmentPaths[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize()
                    ];
                }
            }

            AdminResponse::create([
                'message_response_id' => $responseId,
                'admin_id' => Auth::id(),
                'response_body' => $request->response_body,
                'response_attachments' => $attachmentPaths
            ]);

            $subject = 'Admin Response: ' . $studentResponse->message->subject;

            try {
                $locale = app()->getLocale();
                $locale = in_array($locale, ['en', 'ar', 'fr'], true) ? $locale : 'en';

                Mail::send('emails.admin-response-notification', [
                    'studentName' => $studentResponse->student->first_name . ' ' . $studentResponse->student->last_name,
                    'messageSubject' => $studentResponse->message->subject,
                    'adminResponseBody' => $request->response_body,
                    'messageId' => $studentResponse->message->id,
                    'conversationUrl' => StudentFrontendUrl::localized($locale, 'student-dashboard/messages/' . $studentResponse->message->id),
                ], function ($mail) use ($studentResponse, $subject) {
                    $mail->to($studentResponse->student->email)
                        ->subject($subject);
                });

                OutboundEmailLogger::logSent(
                    $studentResponse->student->email,
                    'internal_message_reply',
                    $subject,
                    $studentResponse->student->id,
                    $studentResponse->student->first_name . ' ' . $studentResponse->student->last_name,
                    'InternalMessage',
                    $studentResponse->message->id,
                    $request->response_body
                );
            } catch (\Exception $e) {
                Log::error('Failed to send notification to student: ' . $e->getMessage());

                OutboundEmailLogger::logFailed(
                    $studentResponse->student->email,
                    'internal_message_reply',
                    $subject,
                    $e->getMessage(),
                    $studentResponse->student->id,
                    $studentResponse->student->first_name . ' ' . $studentResponse->student->last_name,
                    'InternalMessage',
                    $studentResponse->message->id,
                    $request->response_body
                );
            }

            return redirect()->back()->with('success', 'Response sent successfully to student!');
        } catch (\Exception $e) {
            Log::error('Error storing admin response: ' . $e->getMessage());
            return back()->with('error', 'Failed to send response: ' . $e->getMessage())->withInput();
        }
    }

    public function downloadAttachment($filename)
    {
        $path = 'internal-messages/' . $filename;

        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'File not found');
        }

        return response()->download(Storage::disk('public')->path($path));
    }

    public function getUnreadSummary(): array
    {
        $messages = InternalMessage::query()
            ->withCount([
                'responses as unread_responses_count' => fn ($q) => $q->where('is_read_by_admin', false),
            ])
            ->having('unread_responses_count', '>', 0)
            ->orderByDesc('created_at')
            ->get(['id', 'subject', 'created_at']);

        return [
            'total_unread_responses' => MessageResponse::where('is_read_by_admin', false)->count(),
            'messages' => $messages->map(fn ($m) => [
                'id' => $m->id,
                'subject' => $m->subject,
                'unread_responses_count' => $m->unread_responses_count,
                'created_at' => $m->created_at?->toIso8601String(),
            ])->values()->all(),
        ];
    }

    public function markMessageResponsesRead(int $messageId): int
    {
        InternalMessage::findOrFail($messageId);

        return MessageResponse::markAllAsReadByAdminForMessage($messageId);
    }
}
