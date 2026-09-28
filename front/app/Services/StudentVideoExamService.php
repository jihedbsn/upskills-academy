<?php

namespace App\Services;

use App\Mail\AdminNewVideoExamNotification;
use App\Models\Notification;
use App\Models\Product;
use App\Models\Student;
use App\Models\StudentVideoExam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class StudentVideoExamService
{
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:products,id',
                'lien' => 'required|url|max:500',
                'video_description' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $userId = Auth::id();
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                ], 401);
            }

            $studentExists = Student::where('id', $userId)->exists();
            if (!$studentExists) {
                Log::error('Student not found in database', [
                    'student_id' => $userId,
                    'auth_user' => Auth::user(),
                    'auth_check' => Auth::check(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Student account not found in database. Please contact support.',
                ], 401);
            }

            $product = Product::find($request->product_id);
            if (!$product || !$product->is_exam_video) {
                return response()->json([
                    'success' => false,
                    'message' => 'Video exam not available for this course',
                ], 400);
            }

            $existingSubmission = StudentVideoExam::where('student_id', $userId)
                ->where('product_id', $request->product_id)
                ->first();

            if ($existingSubmission) {
                if ($existingSubmission->is_valid != -1) {
                    return response()->json([
                        'success' => false,
                        'message' => __('messages.already_submitted_video_exam'),
                    ], 400);
                }

                $existingSubmission->update([
                    'lien' => $request->lien,
                    'video_description' => $request->video_description,
                    'is_valid' => 0,
                    'admin_notes' => null,
                    'submitted_at' => now(),
                ]);

                try {
                    $student = Auth::user();
                    Notification::notifyAllAdmins(
                        Notification::TYPE_EVALUATION,
                        'Video Exam Re-Submission Awaiting Validation',
                        $student->first_name . ' ' . $student->last_name . ' re-submitted a video exam for "' . ($product->name_en ?? $product->name_ar) . '" - Requires validation',
                        config('app.admin_url') . '/student-video-exams/' . $existingSubmission->id,
                        ['submission_id' => $existingSubmission->id, 'student_id' => $student->id, 'product_id' => $product->id, 'type' => 'video_exam', 'action' => 'resubmit'],
                        '🎥',
                        'red',
                        true
                    );
                } catch (\Exception $e) {
                    Log::error('Admin video exam notification creation error: ' . $e->getMessage());
                }

                try {
                    $existingSubmission->load(['student', 'product']);
                    Mail::to(config('mail.admin_email', env('ADMIN_EMAIL')))->send(
                        new AdminNewVideoExamNotification($existingSubmission, true)
                    );
                } catch (\Exception $e) {
                    Log::error('Admin video exam email notification error: ' . $e->getMessage());
                }

                return response()->json([
                    'success' => true,
                    'message' => __('messages.video_exam_submitted_successfully'),
                    'submission' => $existingSubmission,
                ]);
            }

            $submission = StudentVideoExam::create([
                'student_id' => $userId,
                'product_id' => $request->product_id,
                'lien' => $request->lien,
                'video_description' => $request->video_description,
                'is_valid' => 0,
                'submitted_at' => now(),
            ]);

            try {
                $student = Auth::user();
                Notification::notifyAllAdmins(
                    Notification::TYPE_EVALUATION,
                    'New Video Exam Awaiting Validation',
                    $student->first_name . ' ' . $student->last_name . ' submitted a video exam for "' . ($product->name_en ?? $product->name_ar) . '" - Requires validation',
                    config('app.admin_url') . '/student-video-exams/' . $submission->id,
                    ['submission_id' => $submission->id, 'student_id' => $student->id, 'product_id' => $product->id, 'type' => 'video_exam', 'action' => 'new'],
                    '🎥',
                    'red',
                    true
                );
            } catch (\Exception $e) {
                Log::error('Admin video exam notification creation error: ' . $e->getMessage());
            }

            try {
                $submission->load(['student', 'product']);
                Mail::to(config('mail.admin_email', env('ADMIN_EMAIL')))->send(
                    new AdminNewVideoExamNotification($submission, false)
                );
            } catch (\Exception $e) {
                Log::error('Admin video exam email notification error: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => __('messages.video_exam_submitted_successfully'),
                'submission' => $submission,
            ]);
        } catch (\Exception $e) {
            Log::error('Error submitting video exam', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'product_id' => $request->product_id ?? null,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again.',
            ], 500);
        }
    }

    public function getStudentSubmission($productId)
    {
        try {
            $userId = Auth::id();
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                ], 401);
            }

            $submission = StudentVideoExam::where('student_id', $userId)
                ->where('product_id', $productId)
                ->first();

            return response()->json([
                'success' => true,
                'submission' => $submission,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting video exam submission status', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'product_id' => $productId,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching submission status',
            ], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $query = StudentVideoExam::with(['student', 'product']);

            if ($request->has('is_valid')) {
                $query->where('is_valid', $request->is_valid);
            }

            if ($request->has('product_id')) {
                $query->where('product_id', $request->product_id);
            }

            $submissions = $query->orderBy('created_at', 'desc')->paginate(20);

            return response()->json([
                'success' => true,
                'submissions' => $submissions,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching video exam submissions', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching submissions',
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'is_valid' => 'required|integer|in:-1,0,1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $submission = StudentVideoExam::findOrFail($id);
            $submission->update([
                'is_valid' => $request->is_valid,
            ]);

            return response()->json([
                'success' => true,
                'message' => __('messages.video_exam_status_updated'),
                'submission' => $submission,
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating video exam status', [
                'error' => $e->getMessage(),
                'submission_id' => $id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating status',
            ], 500);
        }
    }
}
