<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CalendarService;

class CalendarController extends Controller
{
    protected CalendarService $calendarService;

    public function __construct(CalendarService $calendarService)
    {
        $this->calendarService = $calendarService;
    }

    /**
     * Display the calendar view
     */
    public function index(Request $request)
    {
        return $this->calendarService->index($request);
    }

    /**
     * Get events for a specific date range (FullCalendar compatible)
     */
    public function getEvents(Request $request)
    {
        return $this->calendarService->getEvents($request);
    }

    /**
     * Add a new event/task
     */
    public function store(Request $request)
    {
        return $this->calendarService->store($request);
    }

    /**
     * Update an event/task
     */
    public function update(Request $request, $id)
    {
        return $this->calendarService->update($request, $id);
    }

    /**
     * Delete an event/task
     */
    public function destroy($id)
    {
        return $this->calendarService->destroy($id);
    }

    /**
     * Mark task as completed
     */
    public function markCompleted($id)
    {
        return $this->calendarService->markCompleted($id);
    }

    /**
     * Get tasks statistics
     */
    public function getStatistics()
    {
        return $this->calendarService->getStatistics();
    }
}
