<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class NewsletterService
{
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
        ]);

        if ($validator->fails()) {
            return $this->backToFooter($request)
                ->with('newsletter_error', __('messages.invalid_email'));
        }

        try {
            $email = $request->email;

            $exists = DB::table('newsletter_subscriptions')
                ->where('email', $email)
                ->exists();

            if ($exists) {
                return $this->backToFooter($request)
                    ->with('newsletter_info', __('messages.already_subscribed'));
            }

            DB::table('newsletter_subscriptions')->insert([
                'email' => $email,
                'subscribed_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return $this->backToFooter($request)
                ->with('newsletter_success', __('messages.newsletter_subscribed'));
        } catch (\Exception $e) {
            Log::error('Newsletter subscription error: ' . $e->getMessage());

            return $this->backToFooter($request)
                ->with('newsletter_error', __('messages.newsletter_subscription_failed'));
        }
    }

    private function backToFooter(Request $request)
    {
        return redirect()->back()->with('scroll_to_newsletter', true);
    }

    private function sendWelcomeEmail($email)
    {
    }
}
