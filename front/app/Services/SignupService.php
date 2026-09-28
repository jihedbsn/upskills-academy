<?php

namespace App\Services;

use App\Mail\WelcomeEmail;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SignupService
{
    public function index()
    {
        return view('signup.signup');
    }

    public function signup(Request $request)
    {
        $request->validate([
            'email' => 'required|email:rfc,dns|unique:students,email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);

        $referralCode = $request->input('ref');

        $student = Student::create([
            'first_name'       => '',
            'last_name'        => '',
            'email'            => $request->email,
            'password'         => Hash::make($request->password),
            'phone'            => null,
            'country'          => null,
            'gender'           => null,
            'birthdate'        => null,
            'image'            => null,
            'referred_by_code' => $referralCode ?: null,
        ]);

        $adminUrl = env('ADMIN_URL', 'http://localhost:8100');

        // Auto-generate referral code for the new student
        try {
            Http::timeout(5)->post($adminUrl . '/api/referrals/generate-code', [
                'student_id' => $student->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Referral code generation failed: ' . $e->getMessage());
        }

        // Register the referral if they signed up with a code
        if ($referralCode) {
            try {
                Http::timeout(5)->post($adminUrl . '/api/referrals/register', [
                    'student_id'    => $student->id,
                    'referral_code' => $referralCode,
                ]);
            } catch (\Exception $e) {
                Log::error('Referral registration failed: ' . $e->getMessage());
            }
        }

        try {
            Mail::to($student->email)->send(new WelcomeEmail($student));
        } catch (\Exception $e) {
            Log::error('Welcome email error: ' . $e->getMessage());
        }

        Auth::login($student);

        return redirect()->intended(route('student-dashboard.index'))->with('success', 'Account created successfully! Welcome email sent to your inbox.');
    }
}
