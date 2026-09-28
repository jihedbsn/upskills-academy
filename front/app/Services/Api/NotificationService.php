<?php

namespace App\Services\Api;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $userType = 'App\Models\User';

        $query = Notification::where('notifiable_type', $userType)
            ->where('notifiable_id', $user->id)
            ->orderBy('created_at', 'desc');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('unread_only') && $request->unread_only == '1') {
            $query->whereNull('read_at');
        }

        $notifications = $query->paginate($request->per_page ?? 20);

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
        ]);
    }

    public function unreadCount()
    {
        $user = Auth::user();
        $userType = 'App\Models\User';

        $count = Notification::where('notifiable_type', $userType)
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->count();

        $importantCount = Notification::where('notifiable_type', $userType)
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->where('is_important', true)
            ->count();

        return response()->json([
            'success' => true,
            'unread_count' => $count,
            'important_count' => $importantCount,
        ]);
    }

    public function markAsRead($id)
    {
        $user = Auth::user();
        $userType = 'App\Models\User';

        $notification = Notification::where('notifiable_type', $userType)
            ->where('notifiable_id', $user->id)
            ->findOrFail($id);

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
        ]);
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        $userType = 'App\Models\User';

        Notification::where('notifiable_type', $userType)
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
        ]);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $userType = 'App\Models\User';

        $notification = Notification::where('notifiable_type', $userType)
            ->where('notifiable_id', $user->id)
            ->findOrFail($id);

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted',
        ]);
    }

    public function getCountsByType()
    {
        $studentId = Auth::id();

        $unreadAdminMessages = DB::connection('mysql_admin')->table('message_recipients')
            ->where('student_id', $studentId)
            ->where('is_read', 0)
            ->count();

        $unreadAdminResponses = DB::connection('mysql_admin')->table('message_responses as mr')
            ->join('admin_responses as ar', 'mr.id', '=', 'ar.message_response_id')
            ->where('mr.student_id', $studentId)
            ->where('ar.is_read', 0)
            ->count();

        $totalUnreadMessages = $unreadAdminMessages + $unreadAdminResponses;

        $counts = [
            'admin_message' => $totalUnreadMessages,
        ];

        return response()->json($counts);
    }
}
