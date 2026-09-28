<?php

namespace App\Services;

use App\Mail\AdminNewTicketNotification;
use App\Models\ChatBotResponse;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\Notification;
use App\Models\ResponseTicket;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ChatBotService
{
    public function index()
    {
        return view('chatbot.index');
    }

    public function startConversation(Request $request)
    {
        $sessionId = $request->session_id ?? Str::uuid();
        $language = $request->language ?? 'en';

        $conversation = ChatConversation::where('session_id', $sessionId)->first();

        if (!$conversation) {
            $conversation = ChatConversation::create([
                'session_id' => $sessionId,
                'user_id' => Auth::id(),
                'user_name' => Auth::user()?->name,
                'user_email' => Auth::user()?->email,
                'language' => $language,
                'status' => 'active',
                'started_at' => now(),
            ]);
        }

        $welcomeMessage = $this->getWelcomeMessage($language);

        ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'bot',
            'message' => $welcomeMessage,
            'sent_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'conversation_id' => $conversation->id,
            'session_id' => $conversation->session_id,
            'welcome_message' => $welcomeMessage,
            'language' => $language,
        ]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'conversation_id' => 'required|exists:chat_conversations,id',
            'language' => 'required|in:en,ar',
        ]);

        $conversation = ChatConversation::findOrFail($request->conversation_id);

        $userMessage = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'user',
            'message' => $request->message,
            'sent_at' => now(),
        ]);

        $botResponse = $this->generateBotResponse($request->message, $request->language);

        $botMessage = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'bot',
            'message' => $botResponse,
            'sent_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'response' => $botResponse,
            'user_message' => $userMessage,
            'bot_response' => $botMessage,
            'conversation_id' => $conversation->id,
        ]);
    }

    public function getMessages(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:chat_conversations,id',
        ]);

        $messages = ChatMessage::where('conversation_id', $request->conversation_id)
            ->orderBy('sent_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }

    public function closeConversation(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:chat_conversations,id',
        ]);

        $conversation = ChatConversation::findOrFail($request->conversation_id);
        $conversation->close();

        return response()->json([
            'success' => true,
            'message' => 'Conversation closed successfully',
        ]);
    }

    public function getFaq(Request $request)
    {
        $language = $request->language ?? 'en';
        $faqs = ChatBotResponse::getFrequentlyAskedQuestions($language, 5);

        return response()->json([
            'success' => true,
            'faqs' => $faqs,
        ]);
    }

    public function sendToAdmin(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:chat_conversations,id',
            'message' => 'required|string|max:1000',
        ]);

        $conversation = ChatConversation::with('messages')->findOrFail($request->conversation_id);

        $ticket = Ticket::create([
            'student_id' => Auth::id(),
            'subject' => 'Chatbot Discussion - ' . $conversation->session_id,
            'message' => $request->message,
            'status' => 'open',
            'priority' => 'medium',
            'category' => 'chatbot',
        ]);

        $conversationDetails = "Conversation ID: " . $conversation->session_id . "\n";
        $conversationDetails .= "User: " . ($conversation->user_name ?: 'Anonymous') . "\n";
        $conversationDetails .= "Language: " . $conversation->language . "\n";
        $conversationDetails .= "Started: " . $conversation->started_at . "\n\n";
        $conversationDetails .= "Conversation History:\n";

        foreach ($conversation->messages as $message) {
            $sender = $message->sender_type === 'user' ? 'User' : 'Bot';
            $conversationDetails .= "[{$sender}] {$message->message}\n";
        }

        ResponseTicket::create([
            'ticket_id' => $ticket->id,
            'admin_id' => null,
            'message' => $conversationDetails,
            'is_internal' => true,
        ]);

        try {
            $ticketWithRelations = Ticket::with(['student', 'responses'])->find($ticket->id);

            $adminEmail = env('ADMIN_EMAIL', 'admin@example.com');
            $student = Auth::user();

            Mail::to($adminEmail)->send(
                new AdminNewTicketNotification($ticketWithRelations)
            );

            if ($student && $student->email) {
                Mail::to($student->email)->send(
                    new AdminNewTicketNotification($ticketWithRelations)
                );
            }
        } catch (\Exception $e) {
            Log::error('Admin ticket notification email error: ' . $e->getMessage());
        }

        try {
            $student = Auth::user();
            Notification::notifyAllAdmins(
                Notification::TYPE_TICKET,
                'New Support Ticket from Chat',
                ($student ? $student->first_name . ' ' . $student->last_name : 'Guest') . ' created a ticket from chat: "' . $ticket->sujet . '"',
                config('app.admin_url') . '/support-tickets/' . $ticket->id,
                ['ticket_id' => $ticket->id, 'student_id' => $student ? $student->id : null, 'subject' => $ticket->sujet, 'from_chat' => true],
                '💬',
                'red'
            );
        } catch (\Exception $e) {
            Log::error('Admin ticket notification creation error: ' . $e->getMessage());
        }

        $conversation->update([
            'status' => 'transferred_to_admin',
            'ended_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Your conversation has been sent to our support team. We will get back to you soon.',
            'ticket_id' => $ticket->id,
        ]);
    }

    public function getStats()
    {
        $stats = [
            'total_conversations' => ChatConversation::count(),
            'active_conversations' => ChatConversation::where('status', 'active')->count(),
            'total_messages' => ChatMessage::count(),
            'user_messages' => ChatMessage::where('sender_type', 'user')->count(),
            'bot_messages' => ChatMessage::where('sender_type', 'bot')->count(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats,
        ]);
    }

    private function generateBotResponse($message, $language)
    {
        $message = strtolower(trim($message));

        Log::info("ChatBot: Processing message: '{$message}' in language: '{$language}'");

        /** @var \Illuminate\Database\Eloquent\Collection<int, ChatBotResponse> $responses */
        $responses = ChatBotResponse::active()->orderBy('priority', 'desc')->get();

        Log::info('ChatBot: Found ' . $responses->count() . ' active responses');

        foreach ($responses as $response) {
            /** @var ChatBotResponse $response */
            if ($response->keyword) {
                $keywords = explode(',', strtolower($response->keyword));
                foreach ($keywords as $keyword) {
                    $keyword = trim($keyword);
                    Log::info("ChatBot: Checking keyword: '{$keyword}' against message: '{$message}'");
                    if (strpos($message, $keyword) !== false) {
                        Log::info('ChatBot: Match found! Using response ID: ' . $response->id);

                        return $response->getResponse($language);
                    }
                }
            }
        }

        Log::info('ChatBot: No match found, using default response');

        return $this->getDefaultResponse($language);
    }

    private function getWelcomeMessage($language)
    {
        if ($language === 'ar') {
            return 'مرحباً! أهلاً وسهلاً بك في الأكاديمية السويدية. أنا مساعدك الذكي هنا لمساعدتك في أي أسئلة حول دوراتنا وخدماتنا. كيف يمكنني مساعدتك اليوم؟';
        }

        return 'Hello! Welcome to Upskills Academy. I\'m your AI assistant here to help you with any questions about our courses and services. How can I assist you today?';
    }

    private function getDefaultResponse($language)
    {
        if ($language === 'ar') {
            return 'شكراً لك على سؤالك. للأسف، لم أفهم سؤالك بشكل واضح. يمكنك إعادة صياغة سؤالك أو اختيار من الأسئلة الشائعة أدناه. إذا كنت تريد، يمكنني إرسال سؤالك إلى فريق الدعم للحصول على مساعدة شخصية.';
        }

        return 'Thank you for your question. I\'m sorry, but I didn\'t understand your question clearly. You can rephrase your question or choose from the frequently asked questions below. If you\'d like, I can send your question to our support team for personalized assistance.';
    }
}
