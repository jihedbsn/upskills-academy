<?php

namespace App\Http\Controllers;

use App\Models\ResponseDiscussion;
use App\Services\ResponseDiscussionService;
use Illuminate\Http\Request;

class ResponseDiscussionController extends Controller
{
    public function __construct(private ResponseDiscussionService $service) {}

    public function index(Request $request)
    {
        $data = $this->service->index($request);
        return view('response-discussions.index', $data);
    }

    public function create(Request $request)
    {
        $data = $this->service->getCreateData($request);
        return view('response-discussions.create', $data);
    }

    public function store(Request $request)
    {
        return $this->service->store($request);
    }

    public function show(ResponseDiscussion $responseDiscussion)
    {
        $responseDiscussion = $this->service->show($responseDiscussion);
        return view('response-discussions.show', compact('responseDiscussion'));
    }

    public function edit(ResponseDiscussion $responseDiscussion)
    {
        $data = $this->service->getEditData($responseDiscussion);
        return view('response-discussions.edit', $data);
    }

    public function update(Request $request, ResponseDiscussion $responseDiscussion)
    {
        return $this->service->update($request, $responseDiscussion);
    }

    public function destroy(ResponseDiscussion $responseDiscussion)
    {
        return $this->service->destroy($responseDiscussion);
    }

    public function approve(ResponseDiscussion $responseDiscussion)
    {
        return $this->service->approve($responseDiscussion);
    }

    public function disapprove(ResponseDiscussion $responseDiscussion)
    {
        return $this->service->disapprove($responseDiscussion);
    }
}
