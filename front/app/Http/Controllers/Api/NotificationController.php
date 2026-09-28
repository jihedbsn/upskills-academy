<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Api\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get all notifications for the current user
     */
    public function index(Request $request)
    {
        return $this->notificationService->index($request);
    }

    /**
     * Get unread notification count
     */
    public function unreadCount()
    {
        return $this->notificationService->unreadCount();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        return $this->notificationService->markAsRead($id);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        return $this->notificationService->markAllAsRead();
    }

    /**
     * Delete a notification
     */
    public function destroy($id)
    {
        return $this->notificationService->destroy($id);
    }

    /**
     * Get notification counts grouped by type for sidebar badges
     */
    public function getCountsByType()
    {
        return $this->notificationService->getCountsByType();
    }
}
