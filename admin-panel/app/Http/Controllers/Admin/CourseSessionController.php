<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseSession;
use App\Models\Product;
use App\Models\Teacher;
use App\Mail\CourseSessionScheduled;
use App\Mail\CourseSessionUpdated;
use App\Mail\CourseSessionCancelled;
use App\Jobs\SendCourseSessionNotifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class CourseSessionController extends Controller
{
    /**
     * Display a listing of course sessions
     */
    public function index(Request $request)
    {
        $query = CourseSession::with(['product']);

        // Filter by product/course
        if ($request->has('product_id') && $request->product_id) {
            $query->where('product_id', $request->product_id);
        }

        // Filter by session type
        if ($request->has('session_type') && $request->session_type) {
            $query->where('session_type', $request->session_type);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->where('session_date', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->where('session_date', '<=', $request->date_to);
        }

        $sessions = $query->orderBy('session_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(15);

        $products = Product::orderBy('id', 'desc')->get();

        return view('admin.course-sessions.index', compact('sessions', 'products'));
    }

    /**
     * Show the form for creating a new session
     */
    public function create(Request $request)
    {
        $products = Product::orderBy('id', 'desc')->get();
        $teachers = Teacher::orderBy('prenom')->get();

        // Pre-select product if passed in query
        $selectedProductId = $request->get('product_id');

        return view('admin.course-sessions.create', compact('products', 'teachers', 'selectedProductId'));
    }

    /**
     * Store a newly created session
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'session_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'session_type' => 'required|in:theory,practical,online,classroom',
            'instructor_name' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'zoom_meeting_id' => 'nullable|string|max:255',
            'zoom_join_url' => 'nullable|url',
            'notes' => 'nullable|string',
        ]);

        try {
            // Convert time strings to datetime for storage
            $validated['start_time'] = Carbon::parse($validated['session_date'] . ' ' . $validated['start_time']);
            $validated['end_time'] = Carbon::parse($validated['session_date'] . ' ' . $validated['end_time']);

            // Create the session
            $session = CourseSession::create($validated);

            // Get enrolled students and dispatch notification job
            $students = $session->getEnrolledStudents();
            $studentCount = $students->count();

            // Dispatch job to send emails in background
            if ($studentCount > 0) {
                SendCourseSessionNotifications::dispatch($session, $students, 'scheduled');
            }

            return redirect()->route('admin.course-sessions.index')
                ->with('success', "Session created successfully! {$studentCount} notification(s) queued for delivery.");

        } catch (\Exception $e) {
            \Log::error('Failed to create course session: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Failed to create session: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified session
     */
    public function show(CourseSession $courseSession)
    {
        $courseSession->load(['product']);
        $enrolledStudents = $courseSession->getEnrolledStudents();

        return view('admin.course-sessions.show', compact('courseSession', 'enrolledStudents'));
    }

    /**
     * Show the form for editing the session
     */
    public function edit(CourseSession $courseSession)
    {
        $products = Product::orderBy('id', 'desc')->get();
        $teachers = Teacher::orderBy('prenom')->get();

        return view('admin.course-sessions.edit', compact('courseSession', 'products', 'teachers'));
    }

    /**
     * Update the specified session
     */
    public function update(Request $request, CourseSession $courseSession)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'session_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'session_type' => 'required|in:theory,practical,online,classroom',
            'instructor_name' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'zoom_meeting_id' => 'nullable|string|max:255',
            'zoom_join_url' => 'nullable|url',
            'status' => 'required|in:scheduled,ongoing,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        try {
            // Check if important fields changed (to send update notification)
            $importantFieldsChanged =
                $courseSession->session_date != $validated['session_date'] ||
                Carbon::parse($courseSession->start_time)->format('H:i') != $validated['start_time'] ||
                Carbon::parse($courseSession->end_time)->format('H:i') != $validated['end_time'] ||
                $courseSession->location != $validated['location'] ||
                $courseSession->zoom_join_url != $validated['zoom_join_url'];

            // Convert time strings to datetime for storage
            $validated['start_time'] = Carbon::parse($validated['session_date'] . ' ' . $validated['start_time']);
            $validated['end_time'] = Carbon::parse($validated['session_date'] . ' ' . $validated['end_time']);

            // Update the session
            $courseSession->update($validated);

            // Send notifications if important fields changed and status is not cancelled
            $studentCount = 0;
            if ($importantFieldsChanged && $courseSession->status !== CourseSession::STATUS_CANCELLED) {
                $students = $courseSession->getEnrolledStudents();
                $studentCount = $students->count();

                // Dispatch job to send emails in background
                if ($studentCount > 0) {
                    SendCourseSessionNotifications::dispatch($courseSession, $students, 'updated');
                }
            }

            $message = "Session updated successfully!";
            if ($studentCount > 0) {
                $message .= " {$studentCount} notification(s) queued for delivery.";
            }

            return redirect()->route('admin.course-sessions.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Failed to update course session: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Failed to update session: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified session
     */
    public function destroy(CourseSession $courseSession)
    {
        try {
            // Get enrolled students before deleting
            $students = $courseSession->getEnrolledStudents();
            $studentCount = $students->count();

            // Dispatch job to send cancellation notifications in background
            if ($studentCount > 0) {
                SendCourseSessionNotifications::dispatch($courseSession, $students, 'cancelled');
            }

            $courseSession->delete();

            return redirect()->route('admin.course-sessions.index')
                ->with('success', "Session deleted successfully! {$studentCount} notification(s) queued for delivery.");

        } catch (\Exception $e) {
            \Log::error('Failed to delete course session: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete session: ' . $e->getMessage());
        }
    }

    /**
     * Get sessions for a specific course (AJAX)
     */
    public function getByCourse($productId)
    {
        $sessions = CourseSession::where('product_id', $productId)
            ->orderBy('session_date')
            ->orderBy('start_time')
            ->get();

        return response()->json($sessions);
    }

    /**
     * Bulk update session status
     */
    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'session_ids' => 'required|array',
            'session_ids.*' => 'exists:course_sessions,id',
            'status' => 'required|in:scheduled,ongoing,completed,cancelled',
        ]);

        try {
            CourseSession::whereIn('id', $validated['session_ids'])
                ->update(['status' => $validated['status']]);

            return back()->with('success', 'Sessions updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update sessions: ' . $e->getMessage());
        }
    }
}
