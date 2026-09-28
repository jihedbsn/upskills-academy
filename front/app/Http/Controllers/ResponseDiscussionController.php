<?php

namespace App\Http\Controllers;

use App\Models\ResponseDiscussion;
use App\Services\ResponseDiscussionService;
use Illuminate\Http\Request;

class ResponseDiscussionController extends Controller
{
    protected ResponseDiscussionService $responseDiscussionService;

    public function __construct(ResponseDiscussionService $responseDiscussionService)
    {
        $this->responseDiscussionService = $responseDiscussionService;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->responseDiscussionService->store($request);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ResponseDiscussion $responseDiscussion)
    {
        return $this->responseDiscussionService->update($request, $responseDiscussion);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ResponseDiscussion $responseDiscussion)
    {
        return $this->responseDiscussionService->destroy($responseDiscussion);
    }

    /**
     * Méthodes pour l'admin
     */
    public function approve(ResponseDiscussion $responseDiscussion)
    {
        return $this->responseDiscussionService->approve($responseDiscussion);
    }

    public function disapprove(ResponseDiscussion $responseDiscussion)
    {
        return $this->responseDiscussionService->disapprove($responseDiscussion);
    }
}
