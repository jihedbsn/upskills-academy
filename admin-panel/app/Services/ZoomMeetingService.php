<?php

namespace App\Services;

use App\Models\ZoomMeeting;
use App\Models\Product;
use App\Models\User;
use App\Jobs\SendZoomMeetingNotifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Jubaer\Zoom\Facades\Zoom;
use Carbon\Carbon;

class ZoomMeetingService
{
    public function index(): \Illuminate\Pagination\LengthAwarePaginator
    {
        return ZoomMeeting::with(['product', 'creator'])
            ->orderBy('start_time', 'desc')
            ->paginate(15);
    }

    public function getCreateData(): array
    {
        return [
            'products'  => Product::orderBy('id', 'desc')->get(),
            'timezones' => timezone_identifiers_list(),
        ];
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id'      => 'required|exists:products,id',
            'topic'           => 'required|string|max:255',
            'start_time'      => 'required|date|after:now',
            'duration'        => 'required|integer|min:15|max:480',
            'timezone'        => 'required|string',
            'moderator_email' => 'required|email',
            'agenda'          => 'nullable|string',
        ]);

        try {
            $zoomMeeting = Zoom::createMeeting([
                'topic'      => $validated['topic'],
                'type'       => 2,
                'start_time' => Carbon::parse($validated['start_time'])->toIso8601String(),
                'duration'   => $validated['duration'],
                'timezone'   => $validated['timezone'],
                'agenda'     => $validated['agenda'] ?? '',
                'settings'   => [
                    'host_video'             => true,
                    'participant_video'      => true,
                    'join_before_host'       => true,
                    'mute_upon_entry'        => false,
                    'watermark'              => false,
                    'audio'                  => 'both',
                    'auto_recording'         => 'none',
                    'waiting_room'           => false,
                    'approval_type'          => 2,
                    'meeting_authentication' => false,
                ],
            ]);

            Log::info('Zoom API Response:', ['response' => $zoomMeeting]);

            if (!isset($zoomMeeting['status']) || $zoomMeeting['status'] !== true) {
                $errorMessage = $zoomMeeting['message'] ?? 'Unknown error from Zoom API';
                throw new \Exception('Zoom API Error: ' . $errorMessage);
            }

            $meetingData = $zoomMeeting['data'];

            if (!isset($meetingData['id'])) {
                throw new \Exception('Zoom API Error: Meeting ID not returned');
            }

            Log::info('Zoom Meeting Created', [
                'meeting_id'         => $meetingData['id'],
                'password'           => $meetingData['password'] ?? 'No password in response',
                'encrypted_password' => $meetingData['encrypted_password'] ?? 'No encrypted password',
                'h323_password'      => $meetingData['h323_password'] ?? 'No h323 password',
            ]);

            $createdBy  = null;
            $authUserId = Auth::id();
            if ($authUserId && User::where('id', $authUserId)->exists()) {
                $createdBy = $authUserId;
            }

            $meeting = ZoomMeeting::create([
                'product_id'      => $validated['product_id'],
                'zoom_meeting_id' => $meetingData['id'],
                'topic'           => $validated['topic'],
                'start_time'      => $validated['start_time'],
                'duration'        => $validated['duration'],
                'timezone'        => $validated['timezone'],
                'password'        => $meetingData['password'] ?? '',
                'join_url'        => $meetingData['join_url'],
                'start_url'       => $meetingData['start_url'] ?? null,
                'moderator_email' => $validated['moderator_email'],
                'created_by'      => $createdBy,
                'agenda'          => $validated['agenda'],
            ]);

            $students     = $meeting->getEnrolledStudents();
            $studentCount = $students->count();

            if ($studentCount > 0) {
                SendZoomMeetingNotifications::dispatch($meeting, $students, 'created');
            }

            return redirect()->route('zoom-meetings.index')
                ->with('success', "Meeting created successfully! {$studentCount} notification(s) queued for delivery.");
        } catch (\Exception $e) {
            Log::error('Failed to create Zoom meeting: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create meeting: ' . $e->getMessage());
        }
    }

    public function show(ZoomMeeting $zoomMeeting): array
    {
        $zoomMeeting->load(['product', 'creator']);
        $enrolledStudents = $zoomMeeting->getEnrolledStudents();

        return compact('zoomMeeting', 'enrolledStudents');
    }

    public function getEditData(ZoomMeeting $zoomMeeting): array
    {
        return [
            'zoomMeeting' => $zoomMeeting,
            'products'    => Product::orderBy('id', 'desc')->get(),
            'timezones'   => timezone_identifiers_list(),
        ];
    }

    public function update(Request $request, ZoomMeeting $zoomMeeting)
    {
        $validated = $request->validate([
            'product_id'      => 'required|exists:products,id',
            'topic'           => 'required|string|max:255',
            'start_time'      => 'required|date',
            'duration'        => 'required|integer|min:15|max:480',
            'timezone'        => 'required|string',
            'moderator_email' => 'required|email',
            'agenda'          => 'nullable|string',
            'status'          => 'required|in:scheduled,completed,cancelled',
            'recording_url'   => 'nullable|string|max:1000',
        ]);

        try {
            if ($zoomMeeting->zoom_meeting_id) {
                Zoom::updateMeeting($zoomMeeting->zoom_meeting_id, [
                    'topic'      => $validated['topic'],
                    'start_time' => Carbon::parse($validated['start_time'])->toIso8601String(),
                    'duration'   => $validated['duration'],
                    'timezone'   => $validated['timezone'],
                    'agenda'     => $validated['agenda'] ?? '',
                ]);
            }

            $zoomMeeting->update($validated);

            $recordingChanged = $zoomMeeting->wasChanged('recording_url');
            $shouldNotify     = $zoomMeeting->wasChanged(['topic', 'start_time', 'duration', 'status']);

            if ($shouldNotify) {
                $students     = $zoomMeeting->getEnrolledStudents();
                $studentCount = $students->count();

                if ($studentCount > 0) {
                    SendZoomMeetingNotifications::dispatch($zoomMeeting, $students, 'updated');
                }

                return redirect()->route('zoom-meetings.show', $zoomMeeting)
                    ->with('success', "Meeting updated successfully! {$studentCount} notification(s) queued for delivery.");
            }

            if ($recordingChanged && $validated['recording_url']) {
                return redirect()->route('zoom-meetings.show', $zoomMeeting)
                    ->with('success', 'Recording URL added successfully! Students can now access it from their dashboard.');
            }

            return redirect()->route('zoom-meetings.show', $zoomMeeting)->with('success', 'Meeting updated successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to update Zoom meeting: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update meeting: ' . $e->getMessage());
        }
    }

    public function destroy(ZoomMeeting $zoomMeeting)
    {
        try {
            $students     = $zoomMeeting->getEnrolledStudents();
            $studentCount = $students->count();

            if ($studentCount > 0) {
                SendZoomMeetingNotifications::dispatch($zoomMeeting, $students, 'cancelled');
            }

            if ($zoomMeeting->zoom_meeting_id) {
                try {
                    Zoom::deleteMeeting($zoomMeeting->zoom_meeting_id);
                } catch (\Exception $e) {
                    Log::warning('Failed to delete Zoom meeting from API: ' . $e->getMessage());
                }
            }

            $zoomMeeting->delete();

            return redirect()->route('zoom-meetings.index')
                ->with('success', "Meeting deleted successfully! {$studentCount} notification(s) queued for delivery.");
        } catch (\Exception $e) {
            Log::error('Failed to delete Zoom meeting: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete meeting: ' . $e->getMessage());
        }
    }

    public function getAddRecordingData(): array
    {
        return ['products' => Product::orderBy('id', 'desc')->get()];
    }

    public function storeRecording(Request $request)
    {
        $validated = $request->validate([
            'product_id'    => 'required|exists:products,id',
            'topic'         => 'required|string|max:255',
            'start_time'    => 'required|date|before:now',
            'duration'      => 'required|integer|min:15|max:480',
            'recording_url' => 'required|string|max:1000',
            'agenda'        => 'nullable|string',
        ]);

        try {
            ZoomMeeting::create([
                'product_id'      => $validated['product_id'],
                'zoom_meeting_id' => '',
                'topic'           => $validated['topic'],
                'start_time'      => $validated['start_time'],
                'duration'        => $validated['duration'],
                'timezone'        => config('app.timezone', 'UTC'),
                'password'        => '',
                'join_url'        => '',
                'start_url'       => '',
                'recording_url'   => $validated['recording_url'],
                'moderator_email' => Auth::user()->email ?? 'admin@example.com',
                'created_by'      => null,
                'status'          => 'completed',
                'agenda'          => $validated['agenda'],
            ]);

            return redirect()->route('zoom-meetings.index')
                ->with('success', 'Past meeting with recording added successfully! Students can now access it from their dashboard.');
        } catch (\Exception $e) {
            Log::error('Failed to add past meeting recording: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to add recording: ' . $e->getMessage());
        }
    }
}
