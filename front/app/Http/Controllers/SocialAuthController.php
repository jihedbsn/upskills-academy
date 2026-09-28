<?php

namespace App\Http\Controllers;

use App\Services\SocialAuthService;

class SocialAuthController extends Controller
{
    protected SocialAuthService $socialAuthService;

    public function __construct(SocialAuthService $socialAuthService)
    {
        $this->socialAuthService = $socialAuthService;
    }

    /**
     * Redirect to Facebook for authentication
     */
    public function redirectToFacebook()
    {
        return $this->socialAuthService->redirectToFacebook();
    }

    /**
     * Handle Facebook callback
     */
    public function handleFacebookCallback()
    {
        return $this->socialAuthService->handleFacebookCallback();
    }

    /**
     * Redirect to Google for authentication
     */
    public function redirectToGoogle()
    {
        return $this->socialAuthService->redirectToGoogle();
    }

    /**
     * Handle Google callback
     */
    public function handleGoogleCallback()
    {
        return $this->socialAuthService->handleGoogleCallback();
    }
}
