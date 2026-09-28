<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\EmailService;

class EmailController extends Controller
{
    protected EmailService $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Afficher la page d'envoi d'email
     */
    public function index()
    {
        return $this->emailService->index();
    }

    /**
     * Envoyer un email personnalisé
     */
    public function send(Request $request)
    {
        return $this->emailService->send($request);
    }
}
