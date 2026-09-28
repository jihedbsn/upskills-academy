<?php

namespace App\Services;

use App\Mail\AdminNewStageSubmissionNotification;
use App\Mail\StageSubmissionConfirmation;
use App\Models\Notification;
use App\Models\Product;
use App\Models\Student;
use App\Models\StudentStageCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class StudentStageCourseService
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'file1' => 'required|file|mimes:jpg,jpeg,png,gif,pdf|max:10240',
                'file2' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf|max:10240',
                'description' => 'nullable|string|max:1000',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            try {
                if (Auth::check()) {
                    Notification::notifyStudent(
                        Auth::id(),
                        Notification::TYPE_EVALUATION,
                        __('messages.stage_course_submission_failed'),
                        __('messages.validation_failed_please_fix'),
                        null,
                        ['errors' => $e->errors(), 'product_id' => $request->input('product_id')],
                        '⚠️',
                        'red',
                        true
                    );
                }
            } catch (\Exception $ne) {
                Log::error('Failed to create validation failure notification: ' . $ne->getMessage());
            }
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated',
            ], 401);
        }

        $user = Auth::user();
        if (!$user || !$user->exists) {
            return response()->json([
                'success' => false,
                'message' => 'User account not found. Please log in again.',
            ], 401);
        }

        $userId = Auth::id();
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'User ID not found. Please log in again.',
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

        $product = Product::findOrFail($request->product_id);
        if (!$product->is_stage) {
            try {
                Notification::notifyStudent(
                    $userId,
                    Notification::TYPE_EVALUATION,
                    __('messages.stage_course_submission_failed'),
                    __('messages.course_not_stage_course'),
                    null,
                    ['product_id' => $request->product_id],
                    '⚠️',
                    'red',
                    true
                );
            } catch (\Exception $ne) {
                Log::error('Failed to create not-stage-course notification: ' . $ne->getMessage());
            }
            return response()->json([
                'success' => false,
                'message' => __('messages.course_not_stage_course'),
            ], 400);
        }

        $existingSubmission = StudentStageCourse::where('student_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($existingSubmission) {
            if ($existingSubmission->is_valid == -1) {
                if ($existingSubmission->file1 && file_exists(public_path('uploads/student_stage_courses/' . $existingSubmission->file1))) {
                    unlink(public_path('uploads/student_stage_courses/' . $existingSubmission->file1));
                }
                if ($existingSubmission->file2 && file_exists(public_path('uploads/student_stage_courses/' . $existingSubmission->file2))) {
                    unlink(public_path('uploads/student_stage_courses/' . $existingSubmission->file2));
                }
                $existingSubmission->delete();
            } else {
                try {
                    Notification::notifyStudent(
                        $userId,
                        Notification::TYPE_EVALUATION,
                        __('messages.stage_course_submission_failed'),
                        __('messages.already_submitted_stage_course'),
                        null,
                        ['product_id' => $request->product_id],
                        '⚠️',
                        'orange',
                        false
                    );
                } catch (\Exception $ne) {
                    Log::error('Failed to create already-submitted notification: ' . $ne->getMessage());
                }
                return response()->json([
                    'success' => false,
                    'message' => __('messages.already_submitted_stage_course'),
                ], 400);
            }
        }

        try {
            $uploadPath = public_path('uploads/student_stage_courses');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $file1Name = null;
            $file2Name = null;

            if ($request->hasFile('file1')) {
                $file1 = $request->file('file1');
                $file1Name = time() . '_' . Str::random(10) . '_' . $file1->getClientOriginalName();
                $file1->move($uploadPath, $file1Name);
            }

            if ($request->hasFile('file2')) {
                $file2 = $request->file('file2');
                $file2Name = time() . '_' . Str::random(10) . '_' . $file2->getClientOriginalName();
                $file2->move($uploadPath, $file2Name);
            }

            try {
                $submission = StudentStageCourse::create([
                    'student_id' => Auth::id(),
                    'product_id' => $request->product_id,
                    'file1' => $file1Name,
                    'file2' => $file2Name,
                    'description' => $request->description,
                    'submitted_at' => now(),
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
                Log::error('Database error creating stage submission', [
                    'error' => $e->getMessage(),
                    'user_id' => Auth::id(),
                    'product_id' => $request->product_id,
                    'file1' => $file1Name,
                    'file2' => $file2Name,
                ]);

                if ($file1Name && file_exists(public_path('uploads/student_stage_courses/' . $file1Name))) {
                    unlink(public_path('uploads/student_stage_courses/' . $file1Name));
                }
                if ($file2Name && file_exists(public_path('uploads/student_stage_courses/' . $file2Name))) {
                    unlink(public_path('uploads/student_stage_courses/' . $file2Name));
                }

                try {
                    Notification::notifyStudent(
                        Auth::id(),
                        Notification::TYPE_EVALUATION,
                        __('messages.stage_course_submission_failed'),
                        __('messages.database_error_try_again_later'),
                        null,
                        ['product_id' => $request->product_id],
                        '⚠️',
                        'red',
                        true
                    );
                } catch (\Exception $ne) {
                    Log::error('Failed to create DB failure notification: ' . $ne->getMessage());
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Database error: ' . $e->getMessage(),
                ], 500);
            }

            try {
                $student = Auth::user();
                Notification::notifyAllAdmins(
                    Notification::TYPE_EVALUATION,
                    'New Stage Submission',
                    $student->first_name . ' ' . $student->last_name . ' submitted a stage evaluation for "' . ($product->name_en ?? $product->name_ar) . '"',
                    config('app.admin_url') . '/student-stage-courses/' . $submission->id,
                    ['submission_id' => $submission->id, 'student_id' => $student->id, 'product_id' => $product->id],
                    '📝',
                    'purple',
                    true
                );
            } catch (\Exception $e) {
                Log::error('Admin stage submission notification creation error: ' . $e->getMessage());
            }

            try {
                $submission->load(['student', 'product']);
                Mail::to(config('mail.admin_email', env('ADMIN_EMAIL')))->send(
                    new AdminNewStageSubmissionNotification($submission)
                );
            } catch (\Exception $e) {
                Log::error('Admin stage submission email notification error: ' . $e->getMessage());
            }

            try {
                $submission->load(['student', 'product']);
                Mail::to($submission->student->email)->send(
                    new StageSubmissionConfirmation($submission)
                );
            } catch (\Exception $e) {
                Log::error('Student stage submission confirmation email error: ' . $e->getMessage());
            }

            try {
                Notification::notifyStudent(
                    Auth::id(),
                    Notification::TYPE_EVALUATION,
                    __('messages.stage_course_submitted_successfully'),
                    __('messages.stage_course_under_review'),
                    route('stage-documents.index', ['productId' => $product->id]),
                    ['submission_id' => $submission->id, 'product_id' => $product->id],
                    '✅',
                    'green',
                    false
                );
            } catch (\Exception $ne) {
                Log::error('Failed to create success notification: ' . $ne->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => __('messages.stage_course_submitted_successfully'),
                'submission' => $submission,
            ]);
        } catch (\Exception $e) {
            try {
                if (Auth::check()) {
                    Notification::notifyStudent(
                        Auth::id(),
                        Notification::TYPE_EVALUATION,
                        __('messages.stage_course_submission_failed'),
                        __('messages.error_submitting_stage_course') . ': ' . $e->getMessage(),
                        null,
                        ['product_id' => $request->input('product_id')],
                        '⚠️',
                        'red',
                        true
                    );
                }
            } catch (\Exception $ne) {
                Log::error('Failed to create unexpected failure notification: ' . $ne->getMessage());
            }
            return response()->json([
                'success' => false,
                'message' => __('messages.error_submitting_stage_course') . ': ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show(StudentStageCourse $studentStageCourse)
    {
        if (Auth::id() !== $studentStageCourse->student_id && !Auth::user()->is_admin) {
            abort(403);
        }

        $studentStageCourse->load(['student', 'product']);
        return view('admin.student-stage-courses.show', compact('studentStageCourse'));
    }

    public function updateValidation(Request $request, StudentStageCourse $studentStageCourse)
    {
        $request->validate([
            'is_valid' => 'required|integer|in:-1,0,1',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $studentStageCourse->load(['student', 'product']);

        $studentStageCourse->update([
            'is_valid' => $request->is_valid,
            'admin_notes' => $request->admin_notes,
            'validated_at' => $request->is_valid !== 0 ? now() : null,
        ]);

        $message = match ($request->is_valid) {
            1 => __('messages.stage_course_validated_successfully'),
            -1 => __('messages.stage_course_rejected_successfully'),
            0 => __('messages.stage_course_pending_successfully'),
        };

        try {
            $status = (int) $request->is_valid;
            $icon = $status === 1 ? '✅' : ($status === -1 ? '❌' : '🕒');
            $color = $status === 1 ? 'green' : ($status === -1 ? 'red' : 'blue');

            $courseName = $studentStageCourse->product->name_en ?? $studentStageCourse->product->name_ar;
            $title = match ($status) {
                1 => __('messages.stage_course_validated_title', ['course' => $courseName]),
                -1 => __('messages.stage_course_rejected_title', ['course' => $courseName]),
                default => __('messages.stage_course_pending_title', ['course' => $courseName]),
            };

            $fullMessage = $message;
            if (!empty($request->admin_notes)) {
                $fullMessage .= ' — ' . $request->admin_notes;
            }

            Notification::notifyStudent(
                $studentStageCourse->student_id,
                Notification::TYPE_EVALUATION,
                $title,
                $fullMessage,
                route('stage-documents.index', ['productId' => $studentStageCourse->product_id]),
                [
                    'submission_id' => $studentStageCourse->id,
                    'product_id' => $studentStageCourse->product_id,
                    'status' => $status,
                    'admin_notes' => $request->admin_notes,
                ],
                $icon,
                $color,
                true
            );
        } catch (\Exception $ne) {
            Log::error('Failed to create student validation notification: ' . $ne->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    public function getSubmissionsForProduct($productId)
    {
        $submissions = StudentStageCourse::with(['student'])
            ->where('product_id', $productId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'submissions' => $submissions,
        ]);
    }

    public function getStudentSubmission($productId)
    {
        $submission = StudentStageCourse::where('student_id', Auth::id())
            ->where('product_id', $productId)
            ->first();

        return response()->json([
            'success' => true,
            'submission' => $submission,
        ]);
    }

    public function downloadFile(StudentStageCourse $studentStageCourse, $fileNumber)
    {
        if (!in_array($fileNumber, ['1', '2'])) {
            abort(404, 'Invalid file number');
        }

        $fileField = 'file' . $fileNumber;
        $fileName = $studentStageCourse->{$fileField};

        if (!$fileName) {
            abort(404, 'File not found');
        }

        $filePath = public_path('uploads/student_stage_courses/' . $fileName);

        if (!file_exists($filePath)) {
            abort(404, 'File not found on server');
        }

        return response()->download($filePath, $fileName);
    }
}
