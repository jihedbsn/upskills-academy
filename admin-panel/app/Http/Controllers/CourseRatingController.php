<?php

namespace App\Http\Controllers;

use App\Models\CourseRating;
use App\Services\CourseRatingService;
use Illuminate\Http\Request;

class CourseRatingController extends Controller
{
    protected CourseRatingService $courseRatingService;

    public function __construct(CourseRatingService $courseRatingService)
    {
        $this->courseRatingService = $courseRatingService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->courseRatingService->index($request);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return $this->courseRatingService->create();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->courseRatingService->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(CourseRating $courseRating)
    {
        return $this->courseRatingService->show($courseRating);
    }

    /**
     * Respond to a course rating
     */
    public function respond(Request $request, CourseRating $courseRating)
    {
        return $this->courseRatingService->respond($request, $courseRating);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CourseRating $courseRating)
    {
        return $this->courseRatingService->edit($courseRating);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CourseRating $courseRating)
    {
        return $this->courseRatingService->update($request, $courseRating);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseRating $courseRating)
    {
        return $this->courseRatingService->destroy($courseRating);
    }

    /**
     * Approve rating
     */
    public function approve(CourseRating $courseRating)
    {
        return $this->courseRatingService->approve($courseRating);
    }

    /**
     * Disapprove rating
     */
    public function disapprove(CourseRating $courseRating)
    {
        return $this->courseRatingService->disapprove($courseRating);
    }

    /**
     * Bulk approve ratings
     */
    public function bulkApprove(Request $request)
    {
        return $this->courseRatingService->bulkApprove($request);
    }
}
