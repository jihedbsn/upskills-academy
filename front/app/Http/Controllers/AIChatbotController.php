<?php

namespace App\Http\Controllers;

use App\Services\ChatbotConversationService;
use Illuminate\Http\Request;

class AIChatbotController extends Controller
{
    protected ChatbotConversationService $chatbotConversationService;

    public function __construct(ChatbotConversationService $chatbotConversationService)
    {
        $this->chatbotConversationService = $chatbotConversationService;
    }

    /**
     * Send a message to the chatbot
     */
    public function sendMessage(Request $request)
    {
        return $this->chatbotConversationService->sendMessage($request);
    }

    /**
     * Poll for new messages (from admin)
     */
    public function pollMessages(Request $request)
    {
        return $this->chatbotConversationService->pollMessages($request);
    }

    /**
     * Get initial data (welcome message, quick replies)
     */
    public function init(Request $request)
    {
        return $this->chatbotConversationService->init($request);
    }

    /**
     * Clear conversation history
     */
    public function clearHistory(Request $request)
    {
        return $this->chatbotConversationService->clearHistory($request);
    }

    /**
     * Get conversation history
     */
    public function getHistory(Request $request)
    {
        return $this->chatbotConversationService->getHistory($request);
    }
}
