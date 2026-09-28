<?php

namespace App\Services;

use App\Models\ResponseDiscussion;
use App\Models\Discussion;
use App\Models\Admin;
use App\Models\Notification;
use App\Mail\StudentCommentReplyNotification;
use App\Models\EmailLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ResponseDiscussionService
{
    public function index(Request $request): array
    {
        $query = ResponseDiscussion::with(['discussion.student', 'discussion.product', 'admin']);

        if ($request->filled('status')) {
            $query->where('is_approved', $request->status);
        }

        if ($request->filled('discussion')) {
            $query->where('discussion_id', $request->discussion);
        }

        if ($request->filled('admin')) {
            $query->whereHas('admin', function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->admin . '%')
                  ->orWhere('last_name', 'like', '%' . $request->admin . '%')
                  ->orWhere('email', 'like', '%' . $request->admin . '%');
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $responses   = $query->orderBy('created_at', 'desc')->paginate(15);
        $discussions = Discussion::with('product')->get();

        $stats = [
            'total'    => ResponseDiscussion::count(),
            'approved' => ResponseDiscussion::where('is_approved', true)->count(),
            'pending'  => ResponseDiscussion::where('is_approved', false)->count(),
        ];

        return compact('responses', 'discussions', 'stats');
    }

    public function getCreateData(Request $request): array
    {
        $discussions             = Discussion::with(['student', 'product'])->get();
        $admins                  = Admin::where('is_active', true)->get();
        $preselectedDiscussionId = $request->query('discussion_id');
        $preselectedDiscussion   = null;

        if ($preselectedDiscussionId) {
            $preselectedDiscussion = Discussion::with(['student', 'product'])->find($preselectedDiscussionId);
        }

        return compact('discussions', 'admins', 'preselectedDiscussionId', 'preselectedDiscussion');
    }

    public function store(Request $request)
    {
        $request->validate([
            'discussion_id' => 'required|exists:discussions,id',
            'admin_id'      => 'required|exists:admins,id',
            'reponse'       => 'required|string|max:1000',
            'is_approved'   => 'boolean',
        ]);

        $response = ResponseDiscussion::create([
            'discussion_id' => $request->discussion_id,
            'admin_id'      => $request->admin_id,
            'reponse'       => $request->reponse,
            'is_approved'   => $request->is_approved ?? false,
        ]);

        $discussion = Discussion::with(['student', 'product'])->find($request->discussion_id);

        if ($discussion && $discussion->student) {
            try {
                Mail::to($discussion->student->email)->send(
                    new StudentCommentReplyNotification($response)
                );

                EmailLog::logSent(
                    $discussion->student->email,
                    'comment_reply',
                    'New Reply to Your Comment',
                    $discussion->student->id ?? $discussion->student_id,
                    ($discussion->student->first_name ?? '') . ' ' . ($discussion->student->last_name ?? ''),
                    'Discussion',
                    $discussion->id
                );

                Log::info('Student comment reply email sent', [
                    'student_id'    => $discussion->student_id,
                    'discussion_id' => $discussion->id,
                    'response_id'   => $response->id,
                ]);
            } catch (\Exception $e) {
                Log::error('Student comment reply email error: ' . $e->getMessage());

                EmailLog::logFailed(
                    $discussion->student->email ?? 'unknown',
                    'comment_reply',
                    'New Reply to Your Comment',
                    $e->getMessage(),
                    $discussion->student->id ?? $discussion->student_id ?? null,
                    ($discussion->student->first_name ?? '') . ' ' . ($discussion->student->last_name ?? ''),
                    'Discussion',
                    $discussion->id
                );
            }

            try {
                $courseName = $discussion->product->titre ?? $discussion->product->name ?? 'Course';
                $admin      = Admin::find($request->admin_id);
                $adminName  = $admin ? ($admin->first_name . ' ' . $admin->last_name) : 'Admin';

                Notification::notifyStudent(
                    $discussion->student_id,
                    Notification::TYPE_COMMENT,
                    'Reply to Your Comment',
                    $adminName . ' replied to your comment on "' . $courseName . '"',
                    config('app.user_url', env('USER_URL')) . '/discussions/' . $discussion->id,
                    [
                        'discussion_id' => $discussion->id,
                        'response_id'   => $response->id,
                        'admin_id'      => $request->admin_id,
                    ],
                    '💬',
                    'blue',
                    false
                );
            } catch (\Exception $e) {
                Log::error('Student comment reply notification creation error: ' . $e->getMessage());
            }
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Réponse créée avec succès.',
                'response' => $response,
            ]);
        }

        return redirect()->route('admin.response-discussions.index')->with('success', 'Réponse créée avec succès.');
    }

    public function show(ResponseDiscussion $responseDiscussion): ResponseDiscussion
    {
        $responseDiscussion->load(['discussion.student', 'discussion.product', 'admin']);
        return $responseDiscussion;
    }

    public function getEditData(ResponseDiscussion $responseDiscussion): array
    {
        $discussions = Discussion::with(['student', 'product'])->get();
        $admins      = Admin::all();
        return compact('responseDiscussion', 'discussions', 'admins');
    }

    public function update(Request $request, ResponseDiscussion $responseDiscussion)
    {
        $request->validate([
            'discussion_id' => 'required|exists:discussions,id',
            'admin_id'      => 'required|exists:admins,id',
            'reponse'       => 'required|string|max:1000',
            'is_approved'   => 'boolean',
        ]);

        $responseDiscussion->update([
            'discussion_id' => $request->discussion_id,
            'admin_id'      => $request->admin_id,
            'reponse'       => $request->reponse,
            'is_approved'   => $request->is_approved ?? false,
        ]);

        return redirect()->route('admin.response-discussions.index')->with('success', 'Réponse mise à jour avec succès.');
    }

    public function destroy(ResponseDiscussion $responseDiscussion)
    {
        try {
            $responseDiscussion->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur lors de la suppression.']);
        }
    }

    public function approve(ResponseDiscussion $responseDiscussion)
    {
        try {
            $responseDiscussion->update(['is_approved' => true]);

            if (request()->expectsJson()) {
                return response()->json(['success' => true]);
            }
            return back()->with('success', 'Réponse approuvée avec succès.');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Erreur lors de l\'approbation.']);
            }
            return back()->with('error', 'Erreur lors de l\'approbation.');
        }
    }

    public function disapprove(ResponseDiscussion $responseDiscussion)
    {
        try {
            $responseDiscussion->update(['is_approved' => false]);

            if (request()->expectsJson()) {
                return response()->json(['success' => true]);
            }
            return back()->with('success', 'Réponse désapprouvée avec succès.');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Erreur lors de la désapprobation.']);
            }
            return back()->with('error', 'Erreur lors de la désapprobation.');
        }
    }
}
