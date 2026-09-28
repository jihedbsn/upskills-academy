<?php

namespace App\Services;

use App\Mail\StudentCommentReplyNotification;
use App\Models\Discussion;
use App\Models\Notification;
use App\Models\ResponseDiscussion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ResponseDiscussionService
{
    public function store(Request $request)
    {
        $request->validate([
            'discussion_id' => 'required|exists:discussions,id',
            'reponse' => 'required|string|max:1000',
        ]);

        if (!Auth::user()->isAdmin()) {
            return back()->withErrors(['error' => 'Accès non autorisé.']);
        }

        try {
            $response = ResponseDiscussion::create([
                'discussion_id' => $request->discussion_id,
                'admin_id' => Auth::id(),
                'reponse' => $request->reponse,
                'is_approved' => true,
            ]);

            $discussion = Discussion::with(['student', 'product'])->find($request->discussion_id);

            if ($discussion && $discussion->student) {
                try {
                    Mail::to($discussion->student->email)->send(
                        new StudentCommentReplyNotification($response)
                    );
                    Log::info('Student comment reply email sent', [
                        'student_id' => $discussion->student_id,
                        'discussion_id' => $discussion->id,
                        'response_id' => $response->id,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Student comment reply email error: ' . $e->getMessage());
                }

                try {
                    $courseName = $discussion->product->titre ?? $discussion->product->name ?? 'Course';
                    $adminName = Auth::user()->first_name . ' ' . Auth::user()->last_name;

                    Notification::notifyStudent(
                        $discussion->student_id,
                        Notification::TYPE_COMMENT,
                        'Reply to Your Comment',
                        $adminName . ' replied to your comment on "' . $courseName . '"',
                        url('/discussions/' . $discussion->id),
                        [
                            'discussion_id' => $discussion->id,
                            'response_id' => $response->id,
                            'admin_id' => Auth::id(),
                        ],
                        '💬',
                        'blue',
                        false
                    );
                } catch (\Exception $e) {
                    Log::error('Student comment reply notification creation error: ' . $e->getMessage());
                }
            }

            return back()->with('success', 'Réponse ajoutée avec succès !');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de l\'ajout de la réponse: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, ResponseDiscussion $responseDiscussion)
    {
        $request->validate([
            'reponse' => 'required|string|max:1000',
        ]);

        if (!Auth::user()->isAdmin()) {
            return back()->withErrors(['error' => 'Accès non autorisé.']);
        }

        try {
            $responseDiscussion->update([
                'reponse' => $request->reponse,
            ]);

            return back()->with('success', 'Réponse mise à jour avec succès !');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()]);
        }
    }

    public function destroy(ResponseDiscussion $responseDiscussion)
    {
        if (!Auth::user()->isAdmin()) {
            return back()->withErrors(['error' => 'Accès non autorisé.']);
        }

        try {
            $responseDiscussion->delete();

            return back()->with('success', 'Réponse supprimée avec succès !');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la suppression: ' . $e->getMessage()]);
        }
    }

    public function approve(ResponseDiscussion $responseDiscussion)
    {
        if (!Auth::user()->isAdmin()) {
            return back()->withErrors(['error' => 'Accès non autorisé.']);
        }

        try {
            $responseDiscussion->approve();

            return back()->with('success', 'Réponse approuvée !');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de l\'approbation: ' . $e->getMessage()]);
        }
    }

    public function disapprove(ResponseDiscussion $responseDiscussion)
    {
        if (!Auth::user()->isAdmin()) {
            return back()->withErrors(['error' => 'Accès non autorisé.']);
        }

        try {
            $responseDiscussion->disapprove();

            return back()->with('success', 'Réponse désapprouvée !');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de la désapprobation: ' . $e->getMessage()]);
        }
    }
}
