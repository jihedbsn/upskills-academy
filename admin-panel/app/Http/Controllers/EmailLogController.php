<?php

namespace App\Http\Controllers;

use App\Services\EmailLogService;
use Illuminate\Http\Request;

class EmailLogController extends Controller
{
    protected EmailLogService $emailLogService;

    public function __construct(EmailLogService $emailLogService)
    {
        $this->emailLogService = $emailLogService;
    }

    /**
     * Display the email logs index page.
     */
    public function index(Request $request)
    {
        return $this->emailLogService->index($request);
    }
}
