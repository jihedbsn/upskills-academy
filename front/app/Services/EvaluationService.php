<?php

namespace App\Services;

use App\Models\StudentSuccess;
use App\Models\CourseRating;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class EvaluationService
{
    /**
     * Get products that the current student needs to evaluate.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPendingEvaluations()
    {
        $user = Auth::user();
        if (!$user) {
            return collect();
        }

        // Get IDs of products the student has successfully completed
        // Assuming success = 1 means approved/completed
        $completedProductIds = StudentSuccess::where('student_id', $user->id)
            ->where('success', 1) 
            ->pluck('product_id');

        if ($completedProductIds->isEmpty()) {
            return collect();
        }

        // Get IDs of products the student has already rated
        $ratedProductIds = CourseRating::where('student_id', $user->id)
            ->whereIn('product_id', $completedProductIds)
            ->pluck('product_id');

        // Find products that are completed but not rated
        $pendingIds = $completedProductIds->diff($ratedProductIds);

        if ($pendingIds->isEmpty()) {
            return collect();
        }

        return Product::whereIn('id', $pendingIds)->get();
    }

    /**
     * Get the count of pending evaluations.
     *
     * @return int
     */
    public function getPendingEvaluationsCount()
    {
        return $this->getPendingEvaluations()->count();
    }
}
