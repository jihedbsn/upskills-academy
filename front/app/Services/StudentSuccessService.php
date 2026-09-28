<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class StudentSuccessService
{
    public function submit(Request $request, $productId)
    {
        $validator = Validator::make($request->all(), [
            'lien_video' => 'nullable|url|max:500',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __('messages.validation_error'),
                'errors' => $validator->errors(),
            ], 422);
        }

        $product = Product::findOrFail($productId);
        $studentId = Auth::id();

        if (!$product->canStudentSubmitSuccess($studentId)) {
            return response()->json([
                'success' => false,
                'message' => __('messages.cannot_submit_success'),
            ], 403);
        }

        $success = $product->submitStudentSuccess($studentId, $request->lien_video);

        if ($success) {
            try {
                $student = Auth::user();
                $courseName = $product->current_variation ? $product->current_variation->name : $product->titre;

                Notification::notifyAllAdmins(
                    Notification::TYPE_STUDENT_SUCCESS,
                    'New Student Success Submission',
                    $student->first_name . ' ' . $student->last_name . ' submitted completion for "' . $courseName . '"',
                    config('app.admin_url') . '/student-successes',
                    [
                        'student_success_id' => $success->id,
                        'student_id' => $studentId,
                        'product_id' => $product->id,
                        'course_name' => $courseName,
                    ],
                    '🏆',
                    'orange'
                );
            } catch (\Exception $e) {
                Log::error('Student success notification creation error: ' . $e->getMessage());
            }

            $message = $product->is_exam_video
                ? __('messages.video_exam_submitted')
                : __('messages.success_submitted');

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $success,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => __('messages.submission_failed'),
        ], 500);
    }

    public function getStatus($productId)
    {
        $product = Product::findOrFail($productId);
        $studentId = Auth::id();

        $success = $product->getStudentSuccess($studentId);
        $status = $product->getStudentSuccessStatus($studentId);

        return response()->json([
            'success' => true,
            'data' => [
                'status' => $status,
                'submission' => $success,
                'can_submit' => $product->canStudentSubmitSuccess($studentId),
            ],
        ]);
    }

    public function validate(Request $request, $productId, $studentId)
    {
        $validator = Validator::make($request->all(), [
            'approved' => 'required|boolean',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __('messages.validation_error'),
                'errors' => $validator->errors(),
            ], 422);
        }

        $product = Product::findOrFail($productId);
        $success = $product->validateStudentSuccess(
            $studentId,
            $request->approved,
            $request->admin_notes
        );

        if ($success) {
            $message = $request->approved
                ? __('messages.success_approved')
                : __('messages.success_rejected');

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $success,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => __('messages.validation_failed'),
        ], 500);
    }

    public function getSubmissions($productId)
    {
        $product = Product::findOrFail($productId);

        $submissions = $product->studentSuccesses()
            ->with('student')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $submissions,
        ]);
    }

    public function getStatistics($productId)
    {
        $product = Product::findOrFail($productId);

        $stats = [
            'total_submissions' => $product->studentSuccesses()->count(),
            'pending_submissions' => $product->studentSuccesses()->pending()->count(),
            'approved_submissions' => $product->studentSuccesses()->approved()->count(),
            'rejected_submissions' => $product->studentSuccesses()->rejected()->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
