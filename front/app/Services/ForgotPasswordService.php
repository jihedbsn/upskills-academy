<?php

namespace App\Services;

use App\Mail\PasswordChangedConfirmationMail;
use App\Mail\PasswordResetCodeMail;
use App\Models\EmailLog;
use App\Models\PasswordResetCode;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordService
{
    public function index()
    {
        return view('forgot-password');
    }

    public function sendCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:students,email',
        ], [
            'email.required' => 'Email is required',
            'email.email' => 'Please enter a valid email',
            'email.exists' => 'No account found with this email',
        ]);

        $student = Student::where('email', $request->email)->first();

        if (!$student) {
            return back()->withErrors(['email' => 'No account found with this email']);
        }

        $code = PasswordResetCode::generateCode();
        $expiresAt = Carbon::now()->addMinutes(15);

        PasswordResetCode::where('email', $request->email)
            ->update(['used' => true]);

        PasswordResetCode::create([
            'email' => $request->email,
            'code' => $code,
            'used' => false,
            'expires_at' => $expiresAt,
        ]);

        Log::info('Password reset code generated', [
            'email' => $request->email,
            'code' => $code,
            'mail_driver' => config('mail.default'),
        ]);

        $mailSent = false;
        try {
            Log::info('Attempting to send password reset code', [
                'email' => $student->email,
                'code' => $code,
                'mail_driver' => config('mail.default'),
                'mail_host' => config('mail.mailers.smtp.host'),
                'mail_from' => config('mail.from.address'),
            ]);

            Mail::to($student->email)->send(new PasswordResetCodeMail($code, $student, app()->getLocale()));
            $mailSent = true;

            EmailLog::logSent(
                $student->email,
                'password_reset',
                'Upskills Academy - Password Reset Code',
                $student->id,
                trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''))
            );

            Log::info('Password reset code sent successfully', ['email' => $student->email]);
        } catch (\Exception $e) {
            EmailLog::logFailed(
                $student->email,
                'password_reset',
                'Upskills Academy - Password Reset Code',
                $e->getMessage(),
                $student->id,
                trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''))
            );

            Log::error('Failed to send password reset code: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString(),
                'email' => $student->email,
                'code' => $code,
                'mail_driver' => config('mail.default'),
            ]);
        }

        $message = $mailSent
            ? 'Reset code has been sent to your email!'
            : 'Reset code: ' . $code . ' (Email not sent. Check configuration.)';

        $encodedEmail = base64_encode($request->email);

        return redirect()->route('verify-code', ['locale' => app()->getLocale(), 'email' => $encodedEmail])
            ->with('success', $message)
            ->with('code', $code);
    }

    public function verifyCodePage($locale, $email)
    {
        $decodedEmail = base64_decode($email, true);

        if ($decodedEmail === false || !filter_var($decodedEmail, FILTER_VALIDATE_EMAIL)) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return redirect()->route('forgot-password', ['locale' => $locale])
                    ->withErrors(['email' => 'Invalid email format']);
            }

            $decodedEmail = $email;
        }

        return view('verify-code', ['email' => $decodedEmail]);
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|digits:6',
        ]);

        $resetCode = PasswordResetCode::where('email', $request->email)
            ->where('code', $request->code)
            ->latest()
            ->first();

        if (!$resetCode) {
            return back()->withErrors(['code' => 'Invalid code']);
        }

        if ($resetCode->used) {
            return back()->withErrors(['code' => 'This code has already been used']);
        }

        if ($resetCode->isExpired()) {
            return back()->withErrors(['code' => 'This code has expired. Please request a new one.']);
        }

        $encodedEmail = base64_encode($request->email);

        return redirect()->route('reset-password', [
            'locale' => app()->getLocale(),
            'email' => $encodedEmail,
            'code' => $request->code,
        ]);
    }

    public function resetPasswordPage($locale, $email, $code)
    {
        $decodedEmail = base64_decode($email, true);

        if ($decodedEmail === false || !filter_var($decodedEmail, FILTER_VALIDATE_EMAIL)) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return redirect()->route('forgot-password', ['locale' => $locale])
                    ->withErrors(['email' => 'Invalid email format']);
            }

            $decodedEmail = $email;
        }

        $resetCode = PasswordResetCode::where('email', $decodedEmail)
            ->where('code', $code)
            ->latest()
            ->first();

        if (!$resetCode || !$resetCode->isValid()) {
            return redirect()->route('forgot-password', ['locale' => $locale])
                ->withErrors(['code' => 'Invalid or expired code']);
        }

        return view('reset-password', ['email' => $decodedEmail, 'code' => $code]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|digits:6',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters',
            'password.confirmed' => 'Password confirmation does not match',
        ]);

        $resetCode = PasswordResetCode::where('email', $request->email)
            ->where('code', $request->code)
            ->latest()
            ->first();

        if (!$resetCode || !$resetCode->isValid()) {
            return back()->withErrors(['code' => 'Invalid or expired code']);
        }

        $student = Student::where('email', $request->email)->first();

        if (!$student) {
            return back()->withErrors(['email' => 'Student not found']);
        }

        $student->update([
            'password' => Hash::make($request->password),
        ]);

        $resetCode->update(['used' => true]);

        try {
            Mail::to($student->email)->send(new PasswordChangedConfirmationMail($student));

            EmailLog::logSent(
                $student->email,
                'password_changed',
                'Upskills Academy - Password Changed',
                $student->id,
                trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''))
            );

            Log::info('Password changed confirmation email sent successfully', ['email' => $student->email]);
        } catch (\Exception $e) {
            EmailLog::logFailed(
                $student->email,
                'password_changed',
                'Upskills Academy - Password Changed',
                $e->getMessage(),
                $student->id,
                trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''))
            );

            Log::error('Failed to send password changed confirmation email: ' . $e->getMessage(), [
                'email' => $student->email,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('login', ['locale' => app()->getLocale()])
            ->with('success', 'Password has been reset successfully! You can now login.');
    }
}
