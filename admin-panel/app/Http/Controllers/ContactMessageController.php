<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Services\ContactMessageService;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    protected ContactMessageService $contactMessageService;

    public function __construct(ContactMessageService $contactMessageService)
    {
        $this->contactMessageService = $contactMessageService;
    }

    /**
     * Display a listing of contact messages.
     */
    public function index(Request $request)
    {
        return $this->contactMessageService->index($request);
    }

    /**
     * Display the specified contact message.
     */
    public function show(ContactMessage $contactMessage)
    {
        return $this->contactMessageService->show($contactMessage);
    }

    /**
     * Send a response to the contact message.
     */
    public function respond(Request $request, ContactMessage $contactMessage)
    {
        return $this->contactMessageService->respond($request, $contactMessage);
    }

    /**
     * Mark message as read.
     */
    public function markAsRead(Request $request, ContactMessage $contactMessage)
    {
        return $this->contactMessageService->markAsRead($request, $contactMessage);
    }

    /**
     * Mark message as unread.
     */
    public function markAsUnread(Request $request, ContactMessage $contactMessage)
    {
        return $this->contactMessageService->markAsUnread($request, $contactMessage);
    }

    /**
     * Remove the specified contact message.
     */
    public function destroy(Request $request, ContactMessage $contactMessage)
    {
        return $this->contactMessageService->destroy($request, $contactMessage);
    }

    /**
     * Get unread count (for AJAX).
     */
    public function getUnreadCount()
    {
        return $this->contactMessageService->getUnreadCount();
    }

    /**
     * Bulk mark as read.
     */
    public function bulkMarkAsRead(Request $request)
    {
        return $this->contactMessageService->bulkMarkAsRead($request);
    }

    /**
     * Bulk delete.
     */
    public function bulkDelete(Request $request)
    {
        return $this->contactMessageService->bulkDelete($request);
    }
}
