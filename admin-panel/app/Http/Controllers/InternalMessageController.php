<?php

namespace App\Http\Controllers;

use App\Services\InternalMessageService;
use Illuminate\Http\Request;

class InternalMessageController extends Controller
{
    protected InternalMessageService $internalMessageService;

    public function __construct(InternalMessageService $internalMessageService)
    {
        $this->internalMessageService = $internalMessageService;
    }

    /**
     * Display list of sent messages
     */
    public function index()
    {
        return $this->internalMessageService->index();
    }

    /**
     * Show form to compose new message
     */
    public function create()
    {
        return $this->internalMessageService->create();
    }

    /**
     * Store new message and send to recipients
     */
    public function store(Request $request)
    {
        return $this->internalMessageService->store($request);
    }

    /**
     * Show message details
     */
    public function show($id)
    {
        return $this->internalMessageService->show($id);
    }

    /**
     * Search students via AJAX
     */
    public function searchStudents(Request $request)
    {
        return $this->internalMessageService->searchStudents($request);
    }

    /**
     * Store admin response to a student's message response
     */
    public function storeAdminResponse(Request $request, $responseId)
    {
        return $this->internalMessageService->storeAdminResponse($request, $responseId);
    }

    /**
     * Download an attachment file
     */
    public function downloadAttachment($filename)
    {
        return $this->internalMessageService->downloadAttachment($filename);
    }

    public function unreadSummary()
    {
        return response()->json([
            'success' => true,
            'data' => $this->internalMessageService->getUnreadSummary(),
        ]);
    }

    public function markRead($id)
    {
        $updated = $this->internalMessageService->markMessageResponsesRead((int) $id);

        return response()->json([
            'success' => true,
            'marked_read' => $updated,
            'data' => $this->internalMessageService->getUnreadSummary(),
        ]);
    }
}
