<?php

namespace App\Http\Controllers;

use App\Services\SupportTicketService;

class SupportTicketController extends Controller
{
    protected SupportTicketService $supportTicketService;

    public function __construct(SupportTicketService $supportTicketService)
    {
        $this->supportTicketService = $supportTicketService;
    }
}
