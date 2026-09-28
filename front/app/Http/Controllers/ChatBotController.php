<?php

namespace App\Http\Controllers;

use App\Services\ChatBotService;
use Illuminate\Http\Request;

class ChatBotController extends Controller
{
    protected ChatBotService $chatBotService;

    public function __construct(ChatBotService $chatBotService)
    {
        $this->chatBotService = $chatBotService;
    }

    /**
    * Display the chatbot interface
     */
    public function index()
    {
        return $this->chatBotService->index();
    }

    /**
        * Start a new conversation
     */
    public function startConversation(Request $request)
    {
        return $this->chatBotService->startConversation($request);
    }

    /**
        * Send a message and receive a response
     */
    public function sendMessage(Request $request)
    {
        return $this->chatBotService->sendMessage($request);
    }

    /**
        * Get message history
     */
    public function getMessages(Request $request)
    {
        return $this->chatBotService->getMessages($request);
    }

    /**
        * Close a conversation
     */
    public function closeConversation(Request $request)
    {
        return $this->chatBotService->closeConversation($request);
    }

    /**
        * Get frequently asked questions
     */
    public function getFaq(Request $request)
    {
        return $this->chatBotService->getFaq($request);
    }

    /**
        * Send a conversation to admin
     */
    public function sendToAdmin(Request $request)
    {
        return $this->chatBotService->sendToAdmin($request);
    }

    /**
        * Get chatbot statistics (for admin)
     */
    public function getStats()
    {
        return $this->chatBotService->getStats();
    }
}
