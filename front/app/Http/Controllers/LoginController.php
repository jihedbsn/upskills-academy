<?php

namespace App\Http\Controllers;

use App\Services\LoginFrontendService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    protected LoginFrontendService $loginFrontendService;

    public function __construct(LoginFrontendService $loginFrontendService)
    {
        $this->loginFrontendService = $loginFrontendService;
        Log::info('LoginController instantiated');
    }

    public function index()
    {
        return $this->loginFrontendService->index();
    }

    public function login(Request $request)
    {
        return $this->loginFrontendService->login($request);
    }

    public function logout(Request $request)
    {
        return $this->loginFrontendService->logout($request);
    }

    /**
     * Redirect to Facebook for authentication
     */
    public function redirectToFacebook()
    {
        return $this->loginFrontendService->redirectToFacebook();
    }

    /**
     * Handle Facebook callback
     */
    public function handleFacebookCallback()
    {
        return $this->loginFrontendService->handleFacebookCallback();
    }

    /**
     * Redirect to Google for authentication
     */
    public function redirectToGoogle()
    {
        return $this->loginFrontendService->redirectToGoogle();
    }

    /**
     * Handle Google callback
     */
    public function handleGoogleCallback()
    {
        return $this->loginFrontendService->handleGoogleCallback();
    }
}
