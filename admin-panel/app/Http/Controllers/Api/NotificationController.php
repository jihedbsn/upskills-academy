<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Installment;
use App\Models\Notification;
use App\Models\ReferralReward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * Get all notifications for the current user
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $userType = 'App\Models\Admin';

        $query = Notification::where('notifiable_type', $userType)
            ->where('notifiable_id', $user->id)
            ->orderBy('created_at', 'desc');

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by read status
        if ($request->filled('unread_only') && $request->unread_only == '1') {
            $query->whereNull('read_at');
        }

        $notifications = $query->paginate($request->per_page ?? 20);

        return response()->json([
            'success' => true,
            'notifications' => $notifications
        ]);
    }

    /**
     * Get unread notification count
     */
    public function unreadCount()
    {
        $user = Auth::user();
        $userType = 'App\Models\Admin';

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
            'important_count' => $importantCount
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        $userType = 'App\Models\Admin';

        $notification = Notification::where('notifiable_type', $userType)
            ->where('notifiable_id', $user->id)
            ->findOrFail($id);

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $userType = 'App\Models\Admin';

        Notification::where('notifiable_type', $userType)
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    }

    /**
     * Delete a notification
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $userType = 'App\Models\Admin';

        $notification = Notification::where('notifiable_type', $userType)
            ->where('notifiable_id', $user->id)
            ->findOrFail($id);

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted'
        ]);
    }

    /**
     * Get notification counts grouped by type
     */
    public function getCountsByType()
    {
        $user = Auth::user();
        $userType = 'App\\Models\\Admin';

        $counts = Notification::where('notifiable_type', $userType)
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        // Add count for unread student message responses (not yet opened by admin)
        $studentResponseCount = DB::table('message_responses')
            ->where('is_read_by_admin', false)
            ->count();

        $counts['student_message_response'] = $studentResponseCount;

        // Installments awaiting admin verification (bank transfer / cash requests).
        // Keeps the sidebar badge accurate even if notification inserts failed historically.
        $awaitingInstallmentCount = Installment::where('status', 'awaiting_payment')->count();
        $installmentNotifCount = (int) ($counts['installment'] ?? 0);
        $counts['installment'] = max($installmentNotifCount, $awaitingInstallmentCount);

        // Referral cash rewards earned by referrers that admin still needs to pay out.
        $unpaidReferralRewards = ReferralReward::where('role', 'referrer')
            ->where('type', 'cash')
            ->whereNull('claimed_at')
            ->count();
        $referralNotifCount = (int) ($counts['referral'] ?? 0);
        $counts['referral'] = max($referralNotifCount, $unpaidReferralRewards);

        return response()->json([
            'success' => true,
            'counts' => $counts,
            'total' => array_sum($counts)
        ]);
    }

    /**
     * Mark all notifications of a specific type as read
     */
    public function markAsReadByType($type)
    {
        $user = Auth::user();
        $userType = 'App\\Models\\Admin';

        $updated = Notification::where('notifiable_type', $userType)
            ->where('notifiable_id', $user->id)
            ->where('type', $type)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Notifications marked as read',
            'updated_count' => $updated
        ]);
    }
}
