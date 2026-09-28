<?php

namespace App\Http\Controllers;

use App\Services\SignupService;
use Illuminate\Http\Request;

class SignupController extends Controller
{
    protected SignupService $signupService;

    public function __construct(SignupService $signupService)
    {
        $this->signupService = $signupService;
    }

    /**
     * Show the signup page
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return $this->signupService->index();
    }

    /**
     * Handle signup form submission
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function signup(Request $request)
    {
        return $this->signupService->signup($request);
    }
}
