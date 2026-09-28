<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SocialMessagingService
{
    protected string $facebookAccessToken;
    protected string $whatsappAccessToken;
    protected string $whatsappPhoneNumberId;

    public function __construct()
    {
        $this->facebookAccessToken = config('services.facebook.page_access_token') ?? '';
        $this->whatsappAccessToken = config('services.whatsapp.access_token') ?? '';
        $this->whatsappPhoneNumberId = config('services.whatsapp.phone_number_id') ?? '';
    }

    /**
     * Send message to social platform based on session ID
     */
    public function sendMessage(string $sessionId, string $message): bool
    {
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

        // Not a social platform conversation
        return false;
    }

    /**
     * Check if conversation is from social platform
     */
    public function isSocialPlatform(string $sessionId): bool
    {
        return str_starts_with($sessionId, 'messenger_')
            || str_starts_with($sessionId, 'instagram_')
            || str_starts_with($sessionId, 'whatsapp_');
    }

    /**
     * Get platform name from session ID
     */
    public function getPlatformName(string $sessionId): ?string
    {
        if (str_starts_with($sessionId, 'messenger_')) {
            return 'Facebook Messenger';
        }
        if (str_starts_with($sessionId, 'instagram_')) {
            return 'Instagram';
        }
        if (str_starts_with($sessionId, 'whatsapp_')) {
            return 'WhatsApp';
        }
        return null;
    }

    /**
     * Send message via Facebook Messenger
     */
    protected function sendMessengerMessage(string $recipientId, string $message): bool
    {
        if (empty($this->facebookAccessToken)) {
            Log::error('Facebook Page Access Token not configured');
            return false;
        }

        try {
            $response = Http::post("https://graph.facebook.com/v18.0/me/messages", [
                'access_token' => $this->facebookAccessToken,
                'recipient' => ['id' => $recipientId],
                'message' => ['text' => $message],
                'messaging_type' => 'RESPONSE',
            ]);

            if (!$response->successful()) {
                Log::error('Failed to send Messenger message', [
                    'recipient' => $recipientId,
                    'error' => $response->json(),
                ]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Messenger send exception', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Send message via Instagram Direct
     */
    protected function sendInstagramMessage(string $recipientId, string $message): bool
    {
        if (empty($this->facebookAccessToken)) {
            Log::error('Facebook Page Access Token not configured');
            return false;
        }

        try {
            $response = Http::post("https://graph.facebook.com/v18.0/me/messages", [
                'access_token' => $this->facebookAccessToken,
                'recipient' => ['id' => $recipientId],
                'message' => ['text' => $message],
            ]);

            if (!$response->successful()) {
                Log::error('Failed to send Instagram message', [
                    'recipient' => $recipientId,
                    'error' => $response->json(),
                ]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Instagram send exception', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Send message via WhatsApp Business API
     */
    protected function sendWhatsAppMessage(string $to, string $message): bool
    {
        if (empty($this->whatsappAccessToken) || empty($this->whatsappPhoneNumberId)) {
            Log::error('WhatsApp credentials not configured');
            return false;
        }

        try {
            $response = Http::withToken($this->whatsappAccessToken)
                ->post("https://graph.facebook.com/v18.0/{$this->whatsappPhoneNumberId}/messages", [
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
}
