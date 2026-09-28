<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ForgotPasswordService;

class ForgotPasswordController extends Controller
{
    protected ForgotPasswordService $forgotPasswordService;

    public function __construct(ForgotPasswordService $forgotPasswordService)
    {
        $this->forgotPasswordService = $forgotPasswordService;
    }

    /**
     * Show forgot password page
     */
    public function index()
    {
        return $this->forgotPasswordService->index();
    }

    /**
     * Send reset code to student email
     */
    public function sendCode(Request $request)
    {
        return $this->forgotPasswordService->sendCode($request);
    }

    /**
     * Show verify code page
     */
    public function verifyCodePage($locale, $email)
    {
        return $this->forgotPasswordService->verifyCodePage($locale, $email);
    }

    /**
     * Verify the reset code
     */
    public function verifyCode(Request $request)
    {
        return $this->forgotPasswordService->verifyCode($request);
    }

    /**
     * Show reset password page
     */
    public function resetPasswordPage($locale, $email, $code)
    {
        return $this->forgotPasswordService->resetPasswordPage($locale, $email, $code);
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        return $this->forgotPasswordService->resetPassword($request);
    }
}
