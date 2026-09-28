<?php

namespace App\Http\Controllers;

use App\Models\ZoomMeeting;
use App\Services\ZoomMeetingService;
use Illuminate\Http\Request;

class ZoomMeetingController extends Controller
{
    public function __construct(private ZoomMeetingService $service) {}

    public function index()
    {
        $meetings = $this->service->index();
        return view('zoom-meetings.index', compact('meetings'));
    }

    public function create()
    {
        $data = $this->service->getCreateData();
        return view('zoom-meetings.create', $data);
    }

    public function store(Request $request)
    {
        return $this->service->store($request);
    }

    public function show(ZoomMeeting $zoomMeeting)
    {
        $data = $this->service->show($zoomMeeting);
        return view('zoom-meetings.show', $data);
    }

    public function edit(ZoomMeeting $zoomMeeting)
    {
        $data = $this->service->getEditData($zoomMeeting);
        return view('zoom-meetings.edit', $data);
    }

    public function update(Request $request, ZoomMeeting $zoomMeeting)
    {
        return $this->service->update($request, $zoomMeeting);
    }

    public function destroy(ZoomMeeting $zoomMeeting)
    {
        return $this->service->destroy($zoomMeeting);
    }

    public function addRecordingForm()
    {
        $data = $this->service->getAddRecordingData();
        return view('zoom-meetings.add-recording', $data);
    }

    public function storeRecording(Request $request)
    {
        return $this->service->storeRecording($request);
    }
}
