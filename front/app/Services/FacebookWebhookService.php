<?php

namespace App\Services;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FacebookWebhookService
{
    protected OpenAIChatbotService $chatbotService;

    public function __construct(OpenAIChatbotService $chatbotService)
    {
        $this->chatbotService = $chatbotService;
    }

    public function verify(Request $request)
    {
        $facebookVerifyToken = config('services.facebook.webhook_verify_token');
        $whatsappVerifyToken = config('services.whatsapp.webhook_verify_token');

        $mode = $request->input('hub_mode');
        $token = $request->input('hub_verify_token');
        $challenge = $request->input('hub_challenge');

        if ($mode === 'subscribe' && ($token === $facebookVerifyToken || $token === $whatsappVerifyToken)) {
            Log::info('Webhook verified successfully', ['token_type' => $token === $whatsappVerifyToken ? 'whatsapp' : 'facebook']);
            return response($challenge, 200);
        }

        Log::warning('Webhook verification failed', [
            'mode' => $mode,
            'token' => $token,
        ]);

        return response('Forbidden', 403);
    }

    public function handle(Request $request)
    {
        $body = $request->all();

        Log::info('Webhook received', ['body' => $body]);

        $object = $body['object'] ?? '';

        if ($object === 'whatsapp_business_account') {
            foreach ($body['entry'] ?? [] as $entry) {
                foreach ($entry['changes'] ?? [] as $change) {
                    if (($change['field'] ?? null) === 'messages') {
                        $this->handleWhatsAppMessages($change['value'] ?? []);
                    }
                }
            }

            return response('EVENT_RECEIVED', 200);
        }

        if ($object !== 'page' && $object !== 'instagram') {
            return response('Not Found', 404);
        }

        foreach ($body['entry'] ?? [] as $entry) {
            if (isset($entry['messaging'])) {
                foreach ($entry['messaging'] as $event) {
                    if ($object === 'instagram') {
                        $this->handleInstagramEvent($event);
                    } else {
                        $this->handleMessengerEvent($event);
                    }
                }
            }

            if (isset($entry['changes'])) {
                foreach ($entry['changes'] as $change) {
                    if (($change['field'] ?? null) === 'comments') {
                        $this->handleInstagramComment($change['value'] ?? []);
                    }
                }
            }
        }

        return response('EVENT_RECEIVED', 200);
    }

    public function sendAdminMessage(ChatConversation $conversation, string $message): bool
    {
        $sessionId = $conversation->session_id;

        if (str_starts_with($sessionId, 'messenger_')) {
            $recipientId = str_replace('messenger_', '', $sessionId);
            return $this->sendMessengerMessage($recipientId, $message);
        }

        if (str_starts_with($sessionId, 'instagram_')) {
            $recipientId = str_replace('instagram_', '', $sessionId);
            return $this->sendInstagramMessage($recipientId, $message);
        }

        if (str_starts_with($sessionId, 'whatsapp_')) {
            $phoneNumber = str_replace('whatsapp_', '', $sessionId);
            return $this->sendWhatsAppMessage($phoneNumber, $message);
        }

        return false;
    }

    protected function handleMessengerEvent(array $event): void
    {
        $senderId = $event['sender']['id'] ?? null;
        $message = $event['message'] ?? null;

        if (!$senderId || !$message) {
            return;
        }

        if ($message['is_echo'] ?? false) {
            return;
        }

        $messageText = $message['text'] ?? '';
        if (empty($messageText)) {
            $this->sendMessengerMessage($senderId, 'عذراً، أستطيع الرد على الرسائل النصية فقط حالياً. كيف يمكنني مساعدتك؟');
            return;
        }

        $conversation = $this->getOrCreateConversation($senderId, 'messenger');

        if ($conversation->isHandledByAdmin()) {
            $conversation->addMessage($messageText, ChatMessage::SENDER_USER);
            return;
        }

        $conversation->addMessage($messageText, ChatMessage::SENDER_USER);

        $history = $this->buildConversationHistory($conversation);
        $response = $this->chatbotService->chat($messageText, $history);

        if ($response['success']) {
            $conversation->addMessage($response['message'], ChatMessage::SENDER_AI);
            $this->sendMessengerMessage($senderId, $response['message']);
        } else {
            $this->sendMessengerMessage($senderId, 'عذراً، حدث خطأ. يرجى المحاولة مرة أخرى.');
        }
    }

    protected function handleInstagramEvent(array $event): void
    {
        $senderId = $event['sender']['id'] ?? null;
        $message = $event['message'] ?? null;

        if (!$senderId || !$message) {
            return;
        }

        if ($message['is_echo'] ?? false) {
            return;
        }

        $messageText = $message['text'] ?? '';
        if (empty($messageText)) {
            $this->sendInstagramMessage($senderId, 'عذراً، أستطيع الرد على الرسائل النصية فقط حالياً.');
            return;
        }

        $conversation = $this->getOrCreateConversation($senderId, 'instagram');

        if ($conversation->isHandledByAdmin()) {
            $conversation->addMessage($messageText, ChatMessage::SENDER_USER);
            return;
        }

        $conversation->addMessage($messageText, ChatMessage::SENDER_USER);

        $history = $this->buildConversationHistory($conversation);
        $response = $this->chatbotService->chat($messageText, $history);

        if ($response['success']) {
            $conversation->addMessage($response['message'], ChatMessage::SENDER_AI);
            $this->sendInstagramMessage($senderId, $response['message']);
        } else {
            $this->sendInstagramMessage($senderId, 'عذراً، حدث خطأ. يرجى المحاولة مرة أخرى.');
        }
    }

    protected function handleInstagramComment(array $commentData): void
    {
        Log::info('Instagram comment received', $commentData);
    }

    protected function handleWhatsAppMessages(array $value): void
    {
        $messages = $value['messages'] ?? [];
        $contacts = $value['contacts'] ?? [];

        foreach ($messages as $message) {
            $from = $message['from'] ?? null;
            $type = $message['type'] ?? 'text';

            if (!$from) {
                continue;
            }

            $senderName = 'WhatsApp User';
            foreach ($contacts as $contact) {
                if (($contact['wa_id'] ?? '') === $from) {
                    $senderName = $contact['profile']['name'] ?? 'WhatsApp User';
                    break;
                }
            }

            if ($type !== 'text') {
                $this->sendWhatsAppMessage($from, 'عذراً، أستطيع الرد على الرسائل النصية فقط حالياً. كيف يمكنني مساعدتك؟');
                continue;
            }

            $messageText = $message['text']['body'] ?? '';
            if (empty($messageText)) {
                continue;
            }

            $conversation = $this->getOrCreateConversation($from, 'whatsapp');

            if ($conversation->wasRecentlyCreated || !$conversation->visitor_name) {
                $conversation->update(['visitor_name' => $senderName]);
            }

            if ($conversation->isHandledByAdmin()) {
                $conversation->addMessage($messageText, ChatMessage::SENDER_USER);
                continue;
            }

            $conversation->addMessage($messageText, ChatMessage::SENDER_USER);

            $history = $this->buildConversationHistory($conversation);
            $response = $this->chatbotService->chat($messageText, $history);

            if ($response['success']) {
                $conversation->addMessage($response['message'], ChatMessage::SENDER_AI);
                $this->sendWhatsAppMessage($from, $response['message']);
            } else {
                $this->sendWhatsAppMessage($from, 'عذراً، حدث خطأ. يرجى المحاولة مرة أخرى.');
            }
        }
    }

    protected function sendWhatsAppMessage(string $to, string $message): bool
    {
        $phoneNumberId = config('services.whatsapp.phone_number_id');
        $accessToken = config('services.whatsapp.access_token');

        if (empty($phoneNumberId) || empty($accessToken)) {
            Log::error('WhatsApp credentials not configured');
            return false;
        }

        try {
            $response = Http::withToken($accessToken)
                ->post("https://graph.facebook.com/v18.0/{$phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => $to,
                    'type' => 'text',
                    'text' => ['body' => $message],
                ]);

            if (!$response->successful()) {
                Log::error('Failed to send WhatsApp message', [
                    'to' => $to,
                    'error' => $response->json(),
                ]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('WhatsApp send exception', ['error' => $e->getMessage()]);
            return false;
        }
    }

    protected function sendMessengerMessage(string $recipientId, string $message): bool
    {
        $accessToken = config('services.facebook.page_access_token');

        try {
            $response = Http::post('https://graph.facebook.com/v18.0/me/messages', [
                'access_token' => $accessToken,
                'recipient' => ['id' => $recipientId],
                'message' => ['text' => $message],
                'messaging_type' => 'RESPONSE',
            ]);

            if (!$response->successful()) {
                Log::error('Failed to send Messenger message', [
                    'recipient' => $recipientId,
                    'error' => $response->json(),
                ]);
            }

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Messenger send exception', ['error' => $e->getMessage()]);
            return false;
        }
    }

    protected function sendInstagramMessage(string $recipientId, string $message): bool
    {
        $accessToken = config('services.facebook.page_access_token');

        try {
            $response = Http::post('https://graph.facebook.com/v18.0/me/messages', [
                'access_token' => $accessToken,
                'recipient' => ['id' => $recipientId],
                'message' => ['text' => $message],
            ]);

            if (!$response->successful()) {
                Log::error('Failed to send Instagram message', [
                    'recipient' => $recipientId,
                    'error' => $response->json(),
                ]);
            }

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Instagram send exception', ['error' => $e->getMessage()]);
            return false;
        }
    }

    protected function getOrCreateConversation(string $platformUserId, string $platform): ChatConversation
    {
        $sessionId = "{$platform}_{$platformUserId}";

        $conversation = ChatConversation::where('session_id', $sessionId)->first();

        if (!$conversation) {
            $conversation = ChatConversation::create([
                'session_id' => $sessionId,
                'student_id' => null,
                'status' => ChatConversation::STATUS_ACTIVE,
                'admin_takeover' => false,
                'visitor_ip' => $platform,
                'visitor_country' => 'Social Media',
                'visitor_language' => 'ar',
                'last_message_at' => now(),
            ]);
        }

        return $conversation;
    }

    protected function buildConversationHistory(ChatConversation $conversation): array
    {
        $messages = $conversation->messages()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->reverse();

        $history = [];
        foreach ($messages as $msg) {
            $role = $msg->sender_type === 'user' ? 'user' : 'assistant';
            $history[] = ['role' => $role, 'content' => $msg->message];
        }

        return $history;
    }
}
