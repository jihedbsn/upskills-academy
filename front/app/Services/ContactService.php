<?php

namespace App\Services;

use App\Mail\NewContactMessage;
use App\Models\ContactMessage;
use App\Models\Information;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ContactService
{
    public function index()
    {
        $information = Information::first();

        session(['contact_form_load_time' => time()]);

        return view('contact.index', compact('information'));
    }

    public function submit(Request $request)
    {
        if ($request->filled('website_url')) {
            Log::warning('Bot detected via honeypot on contact form', [
                'ip' => $request->ip(),
                'email' => $request->email,
            ]);

            return $this->successfulBotResponse($request);
        }

        $formLoadTime = session('contact_form_load_time');
        if ($formLoadTime && (time() - $formLoadTime) < 3) {
            Log::warning('Bot detected via fast submission on contact form', [
                'ip' => $request->ip(),
                'time_diff' => time() - $formLoadTime,
            ]);

            return $this->successfulBotResponse($request);
        }

        if ($this->isSpamContent($request->message) || $this->isSpamContent($request->name)) {
            Log::warning('Bot detected via spam content on contact form', [
                'ip' => $request->ip(),
                'email' => $request->email,
                'message_preview' => substr((string) $request->message, 0, 100),
            ]);

            return $this->successfulBotResponse($request);
        }

        session()->forget(['contact_form_load_time']);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
        ], [
            'name.required' => __('messages.name_required'),
            'email.required' => __('messages.email_required'),
            'email.email' => __('messages.email_invalid'),
            'message.required' => __('messages.message_required'),
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', __('messages.validation_error'));
        }

        try {
            $contactMessage = ContactMessage::create([
                'name' => $request->name,
                'email' => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
                'is_read' => false,
            ]);

            $information = Information::first();
            $adminEmail = $information && $information->email
                ? $information->email
                : env('ADMIN_EMAIL', 'admin@up-skills.academy');

            try {
                Mail::to($adminEmail)->send(new NewContactMessage($contactMessage));
            } catch (\Exception $e) {
                Log::error('Failed to send contact message notification email: ' . $e->getMessage());
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => __('messages.message_sent_successfully'),
                ]);
            }

            return redirect()->back()
                ->with('success', __('messages.message_sent_successfully'));
        } catch (\Exception $e) {
            Log::error('Contact form submission error: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.message_send_failed'),
                ], 500);
            }

            return redirect()->back()
                ->with('error', __('messages.message_send_failed'))
                ->withInput();
        }
    }

    private function successfulBotResponse(Request $request)
    {
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.message_sent_successfully'),
            ]);
        }

        return redirect()->back()->with('success', __('messages.message_sent_successfully'));
    }

    private function isSpamContent(?string $content): bool
    {
        if (empty($content)) {
            return false;
        }

        $content = strtolower($content);

        $spamPatterns = [
            '/https?:\/\/[^\s]+.*https?:\/\/[^\s]+/i',
            '/\b(viagra|cialis|casino|poker|lottery|winner|prize|click here|buy now|limited time|act now|urgent|free money|make money fast|earn \$|work from home opportunity|mlm|bitcoin investment|crypto trading|forex signal|seo services|backlinks|web traffic|followers|likes|subscribers)\b/i',
            '/https?:\/\/[^\s]+/i',
            '/(\+?\d{1,3}[-.\s]?)?\(?\d{3}\)?[-.\s]?\d{3}[-.\s]?\d{4}/i',
            '/<[^>]*script|<[^>]*iframe|<[^>]*object|<[^>]*embed/i',
            '/[а-яА-Я]{3,}.*[a-zA-Z]{3,}|[a-zA-Z]{3,}.*[а-яА-Я]{3,}/u',
        ];

        $urlCount = preg_match_all('/https?:\/\/[^\s]+/i', $content);
        if ($urlCount > 2) {
            return true;
        }

        foreach ($spamPatterns as $pattern) {
            if ($pattern === '/https?:\/\/[^\s]+/i') {
                continue;
            }

            if (preg_match($pattern, $content)) {
                return true;
            }
        }

        $words = str_word_count($content, 1);
        if (count($words) > 10) {
            $wordCounts = array_count_values($words);
            $maxCount = max($wordCounts);
            if ($maxCount > count($words) * 0.3) {
                return true;
            }
        }

        return false;
    }
}
