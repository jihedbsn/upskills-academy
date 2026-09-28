<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Notification;

class NotificationComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $student = Auth::user();

        if ($student) {
            // Get regular notifications
            $notifications = Notification::where('notifiable_type', 'App\\Models\\User')
                ->where('notifiable_id', $student->id)
                ->whereNull('read_at')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            // Get unread message count (admin messages + admin responses)
            $unreadAdminMessages = DB::connection('mysql_admin')->table('message_recipients')
                ->where('student_id', $student->id)
                ->where('is_read', 0)
                ->count();

            $unreadAdminResponses = DB::connection('mysql_admin')->table('message_responses as mr')
                ->join('admin_responses as ar', 'mr.id', '=', 'ar.message_response_id')
                ->where('mr.student_id', $student->id)
                ->where('ar.is_read', 0)
                ->count();

            $unreadMessagesCount = $unreadAdminMessages + $unreadAdminResponses;

            // Get recent unread message details for notification dropdown
            $messageNotifications = collect();

            if ($unreadMessagesCount > 0) {
                // Get recent unread admin messages
                $recentMessages = DB::connection('mysql_admin')->table('message_recipients as mr')
                    ->join('internal_messages as im', 'mr.message_id', '=', 'im.id')
                    ->leftJoin('admins as a', 'im.sender_admin_id', '=', 'a.id')
                    ->where('mr.student_id', $student->id)
                    ->where('mr.is_read', 0)
                    ->select(
                        'im.id',
                        'im.subject',
                        DB::raw("COALESCE(NULLIF(TRIM(CONCAT(COALESCE(a.first_name, ''), ' ', COALESCE(a.last_name, ''))), ''), 'Upskills Academy') as sender_name"),
                        'im.created_at',
                        DB::raw("'admin_message' as type")
                    )
                    ->orderBy('im.created_at', 'desc')
                    ->limit(5)
                    ->get();

                foreach ($recentMessages as $msg) {
                    $messageNotifications->push((object)[
                        'id' => 'msg_' . $msg->id,
                        'title' => 'New Message from Admin',
                        'message' => $msg->subject,
                        'icon' => '✉️',
                        'action_url' => route('student.messages.show', ['locale' => app()->getLocale(), 'id' => $msg->id]),
                        'created_at' => $msg->created_at,
                        'type' => 'message'
                    ]);
                }

                // Get recent admin responses
                if ($unreadAdminResponses > 0) {
                    $recentResponses = DB::connection('mysql_admin')->table('admin_responses as ar')
                        ->join('message_responses as mr', 'ar.message_response_id', '=', 'mr.id')
                        ->join('internal_messages as im', 'mr.message_id', '=', 'im.id')
                        ->leftJoin('admins as u', 'ar.admin_id', '=', 'u.id')
                        ->where('mr.student_id', $student->id)
                        ->where('ar.is_read', 0)
                        ->select(
                            'im.id as message_id',
                            'im.subject',
                            DB::raw("COALESCE(NULLIF(TRIM(CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))), ''), 'Administrator') as admin_name"),
                            'ar.created_at',
                            DB::raw("'admin_response' as type")
                        )
                        ->orderBy('ar.created_at', 'desc')
                        ->limit(3)
                        ->get();

                    foreach ($recentResponses as $resp) {
                        $messageNotifications->push((object)[
                            'id' => 'resp_' . $resp->message_id,
                            'title' => 'Admin replied to your message',
                            'message' => 'Re: ' . $resp->subject,
                            'icon' => '💬',
                            'action_url' => route('student.messages.show', ['locale' => app()->getLocale(), 'id' => $resp->message_id]),
                            'created_at' => $resp->created_at,
                            'type' => 'message'
                        ]);
                    }
                }
            }

            // Merge message notifications with regular notifications
            $allNotifications = $messageNotifications->merge($notifications)->sortByDesc('created_at')->take(10);

            $view->with('unreadNotificationsCount', $notifications->count() + $unreadMessagesCount);
            $view->with('unreadNotifications', $allNotifications);
            $view->with('unreadMessagesCount', $unreadMessagesCount);
        } else {
            $view->with('unreadNotificationsCount', 0);
            $view->with('unreadNotifications', collect());
            $view->with('unreadMessagesCount', 0);
        }
    }
}
