<?php

namespace App\Http\Controllers;

use App\Services\ContactService;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    protected ContactService $contactService;

    public function __construct(ContactService $contactService)
    {
        $this->contactService = $contactService;
    }

    /**
     * Display the contact page
     */
    public function index()
    {
        return $this->contactService->index();
    }

    /**
     * Handle contact form submission
     */
    public function submit(Request $request)
    {
        return $this->contactService->submit($request);
    }
}


