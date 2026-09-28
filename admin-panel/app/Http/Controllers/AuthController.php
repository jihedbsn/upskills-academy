<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return $this->authService->showLoginForm();
    }

    /**
     * Handle login attempt
     */
    public function login(Request $request)
    {
        return $this->authService->login($request);
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        return $this->authService->logout($request);
    }

    /**
     * Show admin profile
     */
    public function profile()
    {
        return $this->authService->profile();
    }

    /**
     * Update admin profile
     */
    public function updateProfile(Request $request)
    {
        return $this->authService->updateProfile($request);
    }
}
