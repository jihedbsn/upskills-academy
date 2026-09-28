<?php

namespace App\Services;

use App\Mail\NewPartnershipRequest;
use App\Models\Partnership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class PartnershipService
{
    public function index()
    {
        session(['partnership_form_load_time' => time()]);

        return view('partnership.index');
    }

    public function store(Request $request, $locale)
    {
        if ($request->filled('website_url')) {
            Log::warning('Bot detected via honeypot on partnership form', [
                'ip' => $request->ip(),
                'email' => $request->email,
            ]);

            return redirect()->route('partnership.index', ['success' => '1']);
        }

        $formLoadTime = session('partnership_form_load_time');
        if ($formLoadTime && (time() - $formLoadTime) < 5) {
            Log::warning('Bot detected via fast submission on partnership form', [
                'ip' => $request->ip(),
                'time_diff' => time() - $formLoadTime,
            ]);

            return redirect()->route('partnership.index', ['success' => '1']);
        }

        if ($this->isSpamContent($request->institution_name)
            || $this->isSpamContent($request->institution_address)
            || $this->isSpamContent($request->additional_courses)) {
            Log::warning('Bot detected via spam content on partnership form', [
                'ip' => $request->ip(),
                'email' => $request->email,
                'institution_name' => $request->institution_name,
            ]);

            return redirect()->route('partnership.index', ['success' => '1']);
        }

        session()->forget(['partnership_form_load_time']);

        $validator = Validator::make($request->all(), [
            'institution_name' => 'required|string|max:255',
            'institution_address' => 'required|string|max:500',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'website' => 'nullable|url|max:255',
            'profile_file' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'requested_courses' => 'required|array|min:1',
            'requested_courses.*' => 'in:sports_injuries,sports_nutrition,fitness_assistant,fitness_trainer,personal_trainer,sports_rehabilitation,sports_psychology,sports_management',
            'additional_courses' => 'nullable|string|max:1000',
        ], [
            'institution_name.required' => __('messages.institution_name_required'),
            'institution_address.required' => __('messages.institution_address_required'),
            'email.required' => __('messages.email_required'),
            'email.email' => __('messages.email_invalid'),
            'phone.required' => __('messages.phone_required'),
            'website.url' => __('messages.website_invalid'),
            'profile_file.required' => __('messages.profile_file_required'),
            'profile_file.file' => __('messages.profile_file_invalid'),
            'profile_file.mimes' => __('messages.profile_file_mimes'),
            'profile_file.max' => __('messages.profile_file_max'),
            'requested_courses.required' => __('messages.requested_courses_required'),
            'requested_courses.min' => __('messages.requested_courses_min'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $profileFile = null;
            if ($request->hasFile('profile_file')) {
                $file = $request->file('profile_file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $profileFile = $file->storeAs('partnerships', $fileName, 'public');
            }

            $partnership = Partnership::create([
                'institution_name' => $request->institution_name,
                'institution_address' => $request->institution_address,
                'email' => $request->email,
                'phone' => $request->phone,
                'website' => $request->website,
                'profile_file' => $profileFile,
                'requested_courses' => $request->requested_courses,
                'additional_courses' => $request->additional_courses,
                'is_read' => false,
                'submitted_at' => now(),
            ]);

            Log::info('Partnership created successfully', ['partnership_id' => $partnership->id]);

            try {
                $adminEmail = env('ADMIN_EMAIL', 'info@up-skills.academy');
                Mail::to($adminEmail)->send(new NewPartnershipRequest($partnership));
                Log::info('Partnership notification email sent to admin');
            } catch (\Exception $e) {
                Log::error('Failed to send partnership notification email: ' . $e->getMessage());
            }

            return redirect()->route('partnership.index', ['success' => '1']);
        } catch (\Exception $e) {
            Log::error('Partnership creation error: ' . $e->getMessage());

            return redirect()->route('partnership.index', ['error' => urlencode($e->getMessage())]);
        }
    }

    public function admin()
    {
        $partnerships = Partnership::orderBy('created_at', 'desc')->paginate(20);

        return view('partnership.admin', compact('partnerships'));
    }

    public function updateStatus(Request $request, Partnership $partnership)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'notes' => 'nullable|string|max:1000',
        ]);

        $partnership->update([
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        return redirect()->back()
            ->with('success', __('messages.partnership_status_updated'));
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
            '/<[^>]*script|<[^>]*iframe|<[^>]*object|<[^>]*embed/i',
            '/[а-яА-Я]{3,}.*[a-zA-Z]{3,}|[a-zA-Z]{3,}.*[а-яА-Я]{3,}/u',
        ];

        $urlCount = preg_match_all('/https?:\/\/[^\s]+/i', $content);
        if ($urlCount > 2) {
            return true;
        }

        foreach ($spamPatterns as $pattern) {
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
