<?php

namespace App\Http\Controllers;

use App\Services\StudentMessageService;
use Illuminate\Http\Request;

class StudentMessageController extends Controller
{
    protected StudentMessageService $studentMessageService;

    public function __construct(StudentMessageService $studentMessageService)
    {
        $this->studentMessageService = $studentMessageService;
    }

    public function inbox()
    {
        return $this->studentMessageService->inbox();
    }

    public function show($locale, $id)
    {
        return $this->studentMessageService->show($locale, $id);
    }

    public function markAsRead($locale, $id)
    {
        return $this->studentMessageService->markAsRead($locale, $id);
    }

    public function markAsUnread($locale, $id)
    {
        return $this->studentMessageService->markAsUnread($locale, $id);
    }

    public function getUnreadCount()
    {
        return $this->studentMessageService->getUnreadCount();
    }

    public function syncStatus($locale)
    {
        return $this->studentMessageService->syncStatus();
    }

    /**
     * Store student response to a message
     */
    public function storeResponse(Request $request, $locale, $id)
    {
        return $this->studentMessageService->storeResponse($request, $locale, $id);
    }
}
