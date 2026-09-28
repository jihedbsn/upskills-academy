<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Services\FacebookWebhookService;
use Illuminate\Http\Request;

class FacebookWebhookController extends Controller
{
    protected FacebookWebhookService $facebookWebhookService;

    public function __construct(FacebookWebhookService $facebookWebhookService)
    {
        $this->facebookWebhookService = $facebookWebhookService;
    }

    /**
     * Verify webhook subscription (GET request from Facebook/WhatsApp)
     */
    public function verify(Request $request)
    {
        return $this->facebookWebhookService->verify($request);
    }

    /**
     * Handle incoming webhook events (POST request from Facebook/Instagram/WhatsApp)
     */
    public function handle(Request $request)
    {
        return $this->facebookWebhookService->handle($request);
    }

    /**
     * Send message from admin to social media user
     */
    public function sendAdminMessage(ChatConversation $conversation, string $message): bool
    {
        return $this->facebookWebhookService->sendAdminMessage($conversation, $message);
    }
}
