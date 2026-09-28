<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\StudentSuccess;
use App\Models\CourseRating;

class EvaluationNotificationComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $student = Auth::user();

        if (!$student) {
            $view->with([
                'pendingEvaluationsCount' => 0,
                'pendingEvaluations' => collect()
            ]);
            return;
        }

        // Get completed courses (success = 1) that haven't been rated yet
        $completedCourses = StudentSuccess::with(['product'])
            ->where('student_id', $student->id)
            ->where('success', 1) // Only successful completions
            ->get();

        // Filter out courses that have already been rated
        $pendingCourses = $completedCourses->filter(function($success) use ($student) {
            return !CourseRating::where('student_id', $student->id)
                                ->where('product_id', $success->product_id)
                                ->exists();
        });

        $view->with([
            'pendingEvaluationsCount' => $pendingCourses->count(),
            'pendingEvaluations' => $pendingCourses->pluck('product')->filter()
        ]);
    }
}
