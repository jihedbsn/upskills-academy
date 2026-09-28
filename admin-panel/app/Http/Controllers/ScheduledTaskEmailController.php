<?php

namespace App\Http\Controllers;

use App\Services\ScheduledTaskEmailService;
use Illuminate\Http\Request;

class ScheduledTaskEmailController extends Controller
{
    public function __construct(private ScheduledTaskEmailService $service) {}

    public function index()
    {
        $data = $this->service->index();
        return view('admin.scheduled-task-emails', $data);
    }

    public function sendEmails(Request $request)
    {
        return $this->service->sendEmails($request);
    }

    public function sendTestEmail(Request $request)
    {
        return $this->service->sendTestEmail($request);
    }
}
