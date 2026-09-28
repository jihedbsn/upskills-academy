<?php

namespace App\Services;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatConversationService
{
    protected SocialMessagingService $socialMessaging;

    public function __construct(SocialMessagingService $socialMessaging)
    {
        $this->socialMessaging = $socialMessaging;
    }

    public function index(Request $request)
    {
        $query = ChatConversation::with(['student', 'admin', 'messages' => function ($queryBuilder) {
            $queryBuilder->latest()->limit(1);
        }]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('unread') && $request->unread == '1') {
            $query->where('unread_admin_count', '>', 0);
        }

        if ($request->filled('admin_only') && $request->admin_only == '1') {
            $query->where('admin_takeover', true);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($queryBuilder) use ($search) {
                $queryBuilder->where('session_id', 'like', "%{$search}%")
                    ->orWhere('visitor_ip', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($studentQueryBuilder) use ($search) {
                        $studentQueryBuilder->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $conversations = $query->orderBy('last_message_at', 'desc')
            ->paginate(20);

        $stats = [
            'total' => ChatConversation::count(),
            'active' => ChatConversation::where('status', 'active')->count(),
            'admin_taken' => ChatConversation::where('admin_takeover', true)->count(),
            'unread' => ChatConversation::where('unread_admin_count', '>', 0)->count(),
        ];

        return view('chat.index', compact('conversations', 'stats'));
    }

    public function show(ChatConversation $conversation)
    {
        $conversation->load(['student', 'admin', 'messages.admin']);
        $conversation->markAsReadByAdmin();

        return view('chat.show', compact('conversation'));
    }

    public function takeOver(ChatConversation $conversation)
    {
        $admin = Auth::user();
        $conversation->takeOver($admin);

        $conversation->addMessage(
            'An administrator has joined the conversation.',
            ChatMessage::SENDER_ADMIN,
            $admin->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Conversation taken over successfully.',
        ]);
    }

    public function release(ChatConversation $conversation)
    {
        $admin = Auth::user();

        $conversation->addMessage(
            'The administrator has left the conversation. You are now chatting with AI.',
            ChatMessage::SENDER_ADMIN,
            $admin->id
        );

        $conversation->releaseToAI();

        return response()->json([
            'success' => true,
            'message' => 'Conversation released to AI.',
        ]);
    }

    public function close(ChatConversation $conversation)
    {
        $conversation->closeConversation();

        return response()->json([
            'success' => true,
            'message' => 'Conversation closed.',
        ]);
    }

    public function sendMessage(Request $request, ChatConversation $conversation)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $admin = Auth::user();

        if (!$conversation->isHandledByAdmin()) {
            $conversation->takeOver($admin);
        }

        $message = $conversation->addMessage(
            $request->message,
            ChatMessage::SENDER_ADMIN,
            $admin->id
        );

        if ($this->socialMessaging->isSocialPlatform($conversation->session_id)) {
            $sent = $this->socialMessaging->sendMessage($conversation->session_id, $request->message);
            if (!$sent) {
                Log::warning('Failed to send message to social platform', [
                    'conversation_id' => $conversation->id,
                    'session_id' => $conversation->session_id,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'timestamp' => $message->created_at->format('H:i'),
            'platform' => $this->socialMessaging->getPlatformName($conversation->session_id),
        ]);
    }

    public function getMessages(Request $request, ChatConversation $conversation)
    {
        $afterId = $request->input('after_id', 0);

        $messages = $conversation->messages()
            ->where('id', '>', $afterId)
            ->with('admin')
            ->get();

        return response()->json([
            'success' => true,
            'messages' => $messages->map(function ($message) {
                return [
                    'id' => $message->id,
                    'message' => $message->message,
                    'sender_type' => $message->sender_type,
                    'sender_label' => $message->sender_label,
                    'sender_color' => $message->sender_color,
                    'is_read' => $message->is_read,
                    'timestamp' => $message->created_at->format('H:i'),
                    'created_at' => $message->created_at->toISOString(),
                ];
            }),
            'unread_count' => $conversation->unread_admin_count,
        ]);
    }

    public function getStatus(ChatConversation $conversation)
    {
        return response()->json([
            'success' => true,
            'status' => $conversation->status,
            'admin_takeover' => $conversation->admin_takeover,
            'unread_admin_count' => $conversation->unread_admin_count,
            'last_message_at' => $conversation->last_message_at?->toISOString(),
        ]);
    }

    public function getUnreadConversations()
    {
        $conversations = ChatConversation::where('unread_admin_count', '>', 0)
            ->with('student')
            ->orderBy('last_message_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'total_unread' => ChatConversation::where('unread_admin_count', '>', 0)->count(),
            'conversations' => $conversations->map(function ($conversation) {
                return [
                    'id' => $conversation->id,
                    'display_name' => $conversation->display_name,
                    'last_message' => $conversation->last_message,
                    'unread_count' => $conversation->unread_admin_count,
                    'status' => $conversation->status,
                    'last_message_at' => $conversation->last_message_at?->diffForHumans(),
                ];
            }),
        ]);
    }

    public function destroy(ChatConversation $conversation)
    {
        $conversation->delete();

        return redirect()->route('admin.chat.index')
            ->with('success', 'Conversation deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:chat_conversations,id',
        ]);

        ChatConversation::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => count($request->ids) . ' conversations deleted.',
        ]);
    }
}
