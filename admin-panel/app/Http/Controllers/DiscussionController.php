<?php

namespace App\Http\Controllers;

use App\Models\Discussion;
use App\Services\DiscussionService;
use Illuminate\Http\Request;

class DiscussionController extends Controller
{
    protected DiscussionService $discussionService;

    public function __construct(DiscussionService $discussionService)
    {
        $this->discussionService = $discussionService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->discussionService->index($request);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return $this->discussionService->create();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->discussionService->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(Discussion $discussion)
    {
        return $this->discussionService->show($discussion);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Discussion $discussion)
    {
        return $this->discussionService->edit($discussion);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Discussion $discussion)
    {
        return $this->discussionService->update($request, $discussion);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Discussion $discussion)
    {
        return $this->discussionService->destroy($discussion);
    }

    /**
     * Approve discussion
     */
    public function approve(Discussion $discussion)
    {
        return $this->discussionService->approve($discussion);
    }

    /**
     * Disapprove discussion
     */
    public function disapprove(Discussion $discussion)
    {
        return $this->discussionService->disapprove($discussion);
    }

    /**
     * Bulk approve discussions
     */
    public function bulkApprove(Request $request)
    {
        return $this->discussionService->bulkApprove($request);
    }

    /**
     * Bulk delete discussions
     */
    public function bulkDelete(Request $request)
    {
        return $this->discussionService->bulkDelete($request);
    }
}
