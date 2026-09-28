<?php

namespace App\Services;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;

class ChatbotConversationService
{
    private const SESSION_CONVERSATION_ID_KEY = 'chatbot_conversation_id';
    private const INIT_MESSAGES_LIMIT = 50;

    protected OpenAIChatbotService $chatbotService;

    public function __construct(OpenAIChatbotService $chatbotService)
    {
        $this->chatbotService = $chatbotService;
    }

    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $key = 'chatbot:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 20)) {
            $seconds = RateLimiter::availableIn($key);

            return response()->json([
                'success' => false,
                'message' => "Too many messages. Please wait {$seconds} seconds.",
            ], 429);
        }
        RateLimiter::hit($key, 60);

        $message = trim($request->message);
        $conversation = $this->getConversation($request);

        if ($conversation->status === ChatConversation::STATUS_CLOSED) {
            return response()->json([
                'success' => false,
                'message' => 'This conversation has been closed.',
            ]);
        }

        $conversation->addMessage($message, ChatMessage::SENDER_USER);

        if ($conversation->isHandledByAdmin()) {
            return response()->json([
                'success' => true,
                'message' => null,
                'admin_handling' => true,
                'timestamp' => now()->format('H:i'),
            ]);
        }

        $conversationHistory = $this->buildConversationHistory($conversation);
        $response = $this->chatbotService->chat($message, $conversationHistory);

        if ($response['success']) {
            $aiMessage = $conversation->addMessage($response['message'], ChatMessage::SENDER_AI);

            return response()->json([
                'success' => true,
                'message' => $response['message'],
                'message_id' => $aiMessage->id,
                'admin_handling' => false,
                'timestamp' => now()->format('H:i'),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $response['message'] ?? 'An error occurred.',
        ]);
    }

    public function pollMessages(Request $request): JsonResponse
    {
        $conversation = $this->getConversation($request);
        $lastId = (int) $request->input('last_id', 0);

        $messages = ChatMessage::query()
            ->where('conversation_id', $conversation->id)
            ->where('id', '>', $lastId)
            ->where('sender_type', ChatMessage::SENDER_ADMIN)
            ->select(['id', 'message', 'sender_type', 'created_at'])
            ->orderBy('id')
            ->get();

        if ($messages->isNotEmpty()) {
            $conversation->markAsReadByUser();
        }

        return response()->json([
            'success' => true,
            'messages' => $messages->map(function (ChatMessage $msg) {
                return [
                    'id' => $msg->id,
                    'message' => $msg->message,
                    'sender_type' => $msg->sender_type,
                    'timestamp' => $msg->created_at->format('H:i'),
                ];
            }),
            'admin_handling' => $conversation->isHandledByAdmin(),
            'status' => $conversation->status,
            'unread_count' => $conversation->unread_user_count,
        ]);
    }

    public function init(Request $request): JsonResponse
    {
        $locale = app()->getLocale();
        $language = match ($locale) {
            'ar' => 'ar',
            'fr' => 'fr',
            default => 'en',
        };

        $conversation = $this->getConversation($request);

        $existingMessages = ChatMessage::query()
            ->where('conversation_id', $conversation->id)
            ->select(['id', 'message', 'sender_type', 'created_at'])
            ->orderByDesc('id')
            ->limit(self::INIT_MESSAGES_LIMIT)
            ->get()
            ->reverse()
            ->values()
            ->map(function (ChatMessage $msg) {
                return [
                    'id' => $msg->id,
                    'message' => $msg->message,
                    'sender_type' => $msg->sender_type,
                    'timestamp' => $msg->created_at->format('H:i'),
                ];
            });

        return response()->json([
            'welcome_message' => $this->chatbotService->getWelcomeMessage($language),
            'quick_replies' => $this->chatbotService->getQuickReplies($language),
            'language' => $language,
            'conversation_id' => $conversation->id,
            'existing_messages' => $existingMessages,
            'admin_handling' => $conversation->isHandledByAdmin(),
            'status' => $conversation->status,
        ]);
    }

    public function clearHistory(Request $request): JsonResponse
    {
        $conversation = $this->getConversation($request);

        $conversation->messages()->delete();

        $conversation->update([
            'last_message' => null,
            'last_message_at' => null,
            'unread_admin_count' => 0,
            'unread_user_count' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Conversation cleared',
        ]);
    }

    public function getHistory(Request $request): JsonResponse
    {
        $conversation = $this->getConversation($request);

        $messages = ChatMessage::query()
            ->where('conversation_id', $conversation->id)
            ->select(['id', 'sender_type', 'message', 'created_at'])
            ->orderBy('id')
            ->get()
            ->map(function (ChatMessage $msg) {
                return [
                    'id' => $msg->id,
                    'role' => $msg->sender_type === ChatMessage::SENDER_USER ? 'user' : 'assistant',
                    'content' => $msg->message,
                    'sender_type' => $msg->sender_type,
                    'timestamp' => $msg->created_at->format('H:i'),
                ];
            });

        return response()->json([
            'history' => $messages,
            'count' => $messages->count(),
            'admin_handling' => $conversation->isHandledByAdmin(),
        ]);
    }

    private function getConversation(Request $request): ChatConversation
    {
        $sessionId = Session::getId();
        $studentId = Auth::id();

        $cachedConversationId = Session::get(self::SESSION_CONVERSATION_ID_KEY);
        if ($cachedConversationId) {
            $conversation = ChatConversation::query()
                ->where('id', $cachedConversationId)
                ->where('session_id', $sessionId)
                ->first();

            if ($conversation) {
                if ($studentId && !$conversation->student_id) {
                    $conversation->update(['student_id' => $studentId]);
                }

                return $conversation;
            }
        }

        $conversation = ChatConversation::getOrCreateForSession($sessionId, $studentId, [
            'visitor_ip' => $this->getClientIp($request),
            'visitor_country' => Session::get('detected_country', 'Unknown'),
            'visitor_language' => app()->getLocale(),
        ]);

        if ($studentId && !$conversation->student_id) {
            $conversation->update(['student_id' => $studentId]);
        }

        Session::put(self::SESSION_CONVERSATION_ID_KEY, $conversation->id);

        return $conversation;
    }

    private function getClientIp(Request $request): string
    {
        $headers = [
            'CF-Connecting-IP',
            'X-Forwarded-For',
            'X-Real-IP',
            'True-Client-IP',
        ];

        foreach ($headers as $header) {
            if ($request->hasHeader($header)) {
                $ip = $request->header($header);

                if (str_contains($ip, ',')) {
                    $ip = trim(explode(',', $ip)[0]);
                }

                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return $request->ip();
    }

    private function buildConversationHistory(ChatConversation $conversation): array
    {
        $messages = ChatMessage::query()
            ->where('conversation_id', $conversation->id)
            ->orderByDesc('id')
            ->limit(20)
            ->select(['sender_type', 'message'])
            ->get()
            ->reverse()
            ->values();

        $history = [];
        foreach ($messages as $msg) {
            $history[] = [
                'role' => $msg->sender_type === ChatMessage::SENDER_USER ? 'user' : 'assistant',
                'content' => $msg->message,
            ];
        }

        return $history;
    }
}
