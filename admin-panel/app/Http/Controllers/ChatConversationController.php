<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Services\ChatConversationService;
use Illuminate\Http\Request;

class ChatConversationController extends Controller
{
    protected ChatConversationService $chatConversationService;

    public function __construct(ChatConversationService $chatConversationService)
    {
        $this->chatConversationService = $chatConversationService;
    }

    /**
     * Display list of all conversations
     */
    public function index(Request $request)
    {
        return $this->chatConversationService->index($request);
    }

    /**
     * Show a specific conversation
     */
    public function show(ChatConversation $conversation)
    {
        return $this->chatConversationService->show($conversation);
    }

    /**
     * Take over a conversation
     */
    public function takeOver(ChatConversation $conversation)
    {
        return $this->chatConversationService->takeOver($conversation);
    }

    /**
     * Release conversation back to AI
     */
    public function release(ChatConversation $conversation)
    {
        return $this->chatConversationService->release($conversation);
    }

    /**
     * Close a conversation
     */
    public function close(ChatConversation $conversation)
    {
        return $this->chatConversationService->close($conversation);
    }

    /**
     * Send a message as admin
     */
    public function sendMessage(Request $request, ChatConversation $conversation)
    {
        return $this->chatConversationService->sendMessage($request, $conversation);
    }

    /**
     * Get new messages for a conversation (for polling)
     */
    public function getMessages(Request $request, ChatConversation $conversation)
    {
        return $this->chatConversationService->getMessages($request, $conversation);
    }

    /**
     * Get conversation status and unread count (for polling)
     */
    public function getStatus(ChatConversation $conversation)
    {
        return $this->chatConversationService->getStatus($conversation);
    }

    /**
     * Get all conversations with unread messages (for dashboard notification)
     */
    public function getUnreadConversations()
    {
        return $this->chatConversationService->getUnreadConversations();
    }

    /**
     * Delete a conversation
     */
    public function destroy(ChatConversation $conversation)
    {
        return $this->chatConversationService->destroy($conversation);
    }

    /**
     * Bulk delete conversations
     */
    public function bulkDelete(Request $request)
    {
        return $this->chatConversationService->bulkDelete($request);
    }
}
