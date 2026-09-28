<?php

namespace App\Services;

use App\Models\Student;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthService
{
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            $facebookUser = $this->fetchSocialUser('facebook');

            Log::info('Facebook OAuth callback received', [
                'email' => $facebookUser->getEmail(),
                'name' => $facebookUser->getName(),
                'id' => $facebookUser->getId(),
            ]);

            return $this->handleSocialLogin($facebookUser, 'facebook');
        } catch (Exception $e) {
            Log::error('Facebook OAuth Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            $locale = app()->getLocale() ?? 'en';
            return redirect()->route('login', ['locale' => $locale])
                ->with('error', __('messages.social_login_failed', ['provider' => 'Facebook']));
        }
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = $this->fetchSocialUser('google');

            Log::info('Google OAuth callback received', [
                'email' => $googleUser->getEmail(),
                'name' => $googleUser->getName(),
                'id' => $googleUser->getId(),
            ]);

            return $this->handleSocialLogin($googleUser, 'google');
        } catch (Exception $e) {
            Log::error('Google OAuth Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            $locale = app()->getLocale() ?? 'en';
            return redirect()->route('login', ['locale' => $locale])
                ->with('error', __('messages.social_login_failed', ['provider' => 'Google']));
        }
    }

    private function handleSocialLogin($socialUser, $provider)
    {
        try {
            Log::info('Starting social login process', [
                'provider' => $provider,
                'email' => $socialUser->getEmail(),
            ]);

            $user = Student::where('email', $socialUser->getEmail())->first();

            if ($user) {
                Log::info('Existing user found', ['user_id' => $user->id]);

                if (empty($user->{$provider . '_id'})) {
                    $updateData = [
                        $provider . '_id' => $socialUser->getId(),
                    ];

                    if (Schema::hasColumn('students', 'avatar')) {
                        $updateData['avatar'] = $socialUser->getAvatar() ?? $user->avatar;
                    }

                    $user->update($updateData);
                }
            } else {
                Log::info('Creating new user from social login');

                $nameParts = $this->parseFullName($socialUser->getName());

                $userData = [
                    'first_name' => $nameParts['first_name'],
                    'last_name' => $nameParts['last_name'],
                    'email' => $socialUser->getEmail(),
                    'password' => Hash::make(Str::random(24)),
                    'email_verified_at' => now(),
                    'phone' => null,
                ];

                $userData[$provider . '_id'] = $socialUser->getId();

                if (Schema::hasColumn('students', 'avatar')) {
                    $userData['avatar'] = $socialUser->getAvatar();
                }

                if (Schema::hasColumn('students', 'status')) {
                    $userData['status'] = 'active';
                }

                if (Schema::hasColumn('students', 'country_id')) {
                    $userData['country_id'] = null;
                }

                $user = Student::create($userData);

                // Auto-generate referral code for the new social-auth student
                try {
                    \Illuminate\Support\Facades\Http::timeout(5)->post(
                        env('ADMIN_URL', 'http://localhost:8100') . '/api/referrals/generate-code',
                        ['student_id' => $user->id]
                    );
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Referral code generation failed (social): ' . $e->getMessage());
                }

                Log::info('New user created via ' . ucfirst($provider) . ' OAuth', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'provider' => $provider,
                ]);
            }

            if (isset($user->status) && $user->status !== 'active') {
                Log::warning('User account is not active', ['user_id' => $user->id, 'status' => $user->status]);
                $locale = app()->getLocale() ?? 'en';
                return redirect()->route('login', ['locale' => $locale])
                    ->with('error', __('messages.account_suspended'));
            }

            if ($user->is_blocked) {
                Log::warning('User account is blocked', ['user_id' => $user->id]);
                $locale = app()->getLocale() ?? 'en';
                return redirect()->route('login', ['locale' => $locale])
                    ->with('error', __('messages.account_blocked'));
            }

            Auth::login($user, true);

            Log::info('User logged in successfully', ['user_id' => $user->id]);

            if (Schema::hasColumn('students', 'last_login_at')) {
                $user->update(['last_login_at' => now()]);
            }

            $locale = app()->getLocale() ?? 'en';

            return redirect()->intended(route('student-dashboard.index', ['locale' => $locale]))
                ->with('success', __('messages.welcome_back', ['name' => $user->first_name]));
        } catch (Exception $e) {
            Log::error('Error in handleSocialLogin', [
                'provider' => $provider,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $locale = app()->getLocale() ?? 'en';
            return redirect()->route('login', ['locale' => $locale])
                ->with('error', __('messages.social_login_failed', ['provider' => ucfirst($provider)]));
        }
    }

    private function parseFullName($fullName)
    {
        $parts = explode(' ', trim($fullName), 2);

        return [
            'first_name' => $parts[0] ?? 'User',
            'last_name' => $parts[1] ?? '',
        ];
    }

    private function fetchSocialUser(string $provider)
    {
        $driver = Socialite::driver($provider);

        if (method_exists($driver, 'stateless')) {
            $driver = call_user_func([$driver, 'stateless']);
        }

        return $driver->user();
    }
}
