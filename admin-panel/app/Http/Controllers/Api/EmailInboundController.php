<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\EmailInboxService;
use Illuminate\Http\Request;

class EmailInboundController extends Controller
{
    public function __construct(
        protected EmailInboxService $inboxService
    ) {}

    public function webhook(Request $request)
    {
        $token = config('email-inbox.inbound_webhook_token');

        if ($token && $request->header('X-Email-Inbound-Token') !== $token) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $parsed = $this->inboxService->parseWebhookPayload($request->all());

        if (empty($parsed['from_email'])) {
            return response()->json(['success' => false, 'message' => 'Missing sender'], 422);
        }

        $message = $this->inboxService->recordInboundMessage($parsed);

        if (! $message) {
            return response()->json(['success' => true, 'message' => 'Duplicate ignored']);
        }

        return response()->json([
            'success' => true,
            'thread_id' => $message->thread_id,
            'message_id' => $message->id,
        ]);
    }
}
