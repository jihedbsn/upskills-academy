<?php

namespace App\Services;

use App\Mail\PracticalExamStartedNotification;
use App\Mail\PracticalExamSubmittedToAdmin;
use App\Mail\PracticalExamUploadConfirmation;
use App\Models\Notification;
use App\Models\PracticalExamAttempt;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PracticalExamService
{
    public function index($locale)
    {
        $studentId = Auth::id();

        $attempts = PracticalExamAttempt::with(['product', 'trainingCase', 'reviewer'])
            ->where('user_id', $studentId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('student-dashboard.pages.practical-exams-list', compact('attempts'));
    }

    public function show($locale, $productId)
    {
        $product = Product::findOrFail($productId);

        if (!$product->hasStudentAccess(Auth::id())) {
            return redirect()->back()->with('error', __('messages.no_access_to_course'));
        }

        $blockResponse = app(BlockStudentCourseService::class)->denyIfBlocked($productId, $locale);
        if ($blockResponse) {
            return $blockResponse;
        }

        if ($product->is_stage) {
            $stageValidated = \App\Models\StudentStageCourse::where('student_id', Auth::id())
                ->where('product_id', $productId)
                ->where('is_valid', 1)
                ->exists();

            if (!$stageValidated) {
                return redirect()->back()->with('error', __('messages.stage_must_be_validated_first'));
            }
        }

        $trainingCases = \App\Models\TrainingCase::where('is_active', true)->get();

        $attempts = PracticalExamAttempt::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->with(['trainingCase', 'trainingCaseFile', 'reviewer'])
            ->orderBy('created_at', 'desc')
            ->get();

        $currentAttempt = $attempts->whereIn('status', ['pending_submission', 'pending_review'])->first();
        $hasPassed = $attempts->where('status', 'passed')->count() > 0;

        return view('student-dashboard.pages.practical-exam', compact('product', 'trainingCases', 'attempts', 'currentAttempt', 'hasPassed'));
    }

    public function start(Request $request, $locale, $productId)
    {
        $product = Product::findOrFail($productId);

        $blockResponse = app(BlockStudentCourseService::class)->denyIfBlocked($productId, $locale);
        if ($blockResponse) {
            return $blockResponse;
        }

        if ($product->is_stage) {
            $stageValidated = \App\Models\StudentStageCourse::where('student_id', Auth::id())
                ->where('product_id', $productId)
                ->where('is_valid', 1)
                ->exists();

            if (!$stageValidated) {
                return redirect()->back()->with('error', __('messages.stage_must_be_validated_first'));
            }
        }

        $lastAttempt = PracticalExamAttempt::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->latest()
            ->first();

        if ($lastAttempt && $lastAttempt->status === 'passed') {
            return redirect()->back()->with('error', __('messages.already_passed_exam'));
        }

        if ($lastAttempt && in_array($lastAttempt->status, ['pending_submission', 'pending_review'])) {
            return redirect()->back()->with('info', __('messages.exam_already_in_progress'));
        }

        $product = Product::with('trainingCases')->find($productId);

        if (!$product) {
            return redirect()->back()->with('error', __('messages.course_not_found'));
        }

        $productTrainingCaseIds = $product->trainingCases()->pluck('training_cases.id');

        if ($productTrainingCaseIds->isEmpty()) {
            return redirect()->back()->with('error', __('messages.no_training_cases_assigned_to_course'));
        }

        $randomFile = \App\Models\TrainingCaseFile::whereHas('trainingCase', function ($query) use ($productTrainingCaseIds) {
            $query->where('is_active', true)
                ->whereIn('id', $productTrainingCaseIds);
        })->inRandomOrder()->first();

        if (!$randomFile) {
            return redirect()->back()->with('error', __('messages.no_training_cases_available'));
        }

        $attemptNumber = $lastAttempt ? $lastAttempt->attempt_number + 1 : 1;

        $attempt = PracticalExamAttempt::create([
            'user_id' => Auth::id(),
            'product_id' => $productId,
            'training_case_id' => $randomFile->training_case_id,
            'training_case_file_id' => $randomFile->id,
            'attempt_number' => $attemptNumber,
            'status' => $product->practical_exam_type === 'classroom' ? 'pending_review' : 'pending_submission',
            'submitted_at' => $product->practical_exam_type === 'classroom' ? now() : null,
        ]);

        try {
            $actionUrl = route('student.practical-exam.show', ['locale' => app()->getLocale(), 'product' => $productId]);

            Notification::notifyStudent(
                Auth::id(),
                'practical_exam_started',
                __('messages.practical_exam_started'),
                __('messages.practical_exam_started_message', ['course' => $product->titre]),
                $actionUrl,
                [
                    'product_id' => $productId,
                    'attempt_number' => $attemptNumber,
                    'training_case' => $randomFile->trainingCase->title ?? $randomFile->trainingCase->name ?? 'Training Case',
                ],
                null,
                'success',
                false
            );

            Mail::to(Auth::user()->email)->send(new PracticalExamStartedNotification($attempt));
        } catch (\Exception $e) {
            Log::error('Failed to send practical exam started notification: ' . $e->getMessage());
        }

        return redirect()->route('student.practical-exam.show', $productId);
    }

    public function submit(Request $request, $locale, $productId)
    {
        $request->validate([
            'video_url' => 'required|url',
        ], [
            'video_url.required' => __('messages.video_url_required'),
            'video_url.url' => __('messages.video_url_invalid'),
        ]);

        $attempt = PracticalExamAttempt::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->where('status', 'pending_submission')
            ->firstOrFail();

        $attempt->update([
            'video_url' => $request->video_url,
            'submitted_at' => now(),
            'status' => 'pending_review',
        ]);

        try {
            $student = Auth::user();
            $product = Product::find($productId);
            $actionUrl = route('student.practical-exam.show', ['locale' => app()->getLocale(), 'product' => $productId]);

            Notification::notifyStudent(
                Auth::id(),
                'practical_exam_submitted',
                __('messages.submission_successful'),
                __('messages.video_submitted_successfully_message', ['course' => $product->titre]),
                $actionUrl,
                [
                    'product_id' => $productId,
                    'attempt_number' => $attempt->attempt_number,
                    'video_url' => $request->video_url,
                ],
                null,
                'success',
                false
            );

            Mail::to($student->email)->send(new PracticalExamUploadConfirmation($attempt));

            $adminActionUrl = config('app.admin_url') . '/practical-exams/' . $attempt->id . '/grade';

            Notification::notifyAllAdmins(
                'practical_exam_new_submission',
                'New Practical Exam Submission',
                'A student has submitted a practical exam for ' . $product->titre . ' and it is awaiting review.',
                $adminActionUrl,
                [
                    'student_name' => $student->first_name . ' ' . $student->last_name,
                    'course_name' => $product->titre,
                    'attempt_number' => $attempt->attempt_number,
                ],
                null,
                'primary',
                true
            );

            $isRetake = $attempt->attempt_number > 1;
            $admins = \App\Models\Admin::all();

            if ($admins->count() > 0) {
                $firstAdmin = $admins->first();
                $otherAdmins = $admins->slice(1)->pluck('email')->toArray();

                $mailInstance = Mail::to($firstAdmin->email);

                if (!empty($otherAdmins)) {
                    $mailInstance->bcc($otherAdmins);
                }

                $mailInstance->send(new PracticalExamSubmittedToAdmin($attempt, $isRetake));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send practical exam submission notification: ' . $e->getMessage());
        }

        return redirect()->route('student.practical-exam.show', $productId);
    }

    public function markCompleted(Request $request, $locale, $productId)
    {
        $request->validate([
            'response_file' => 'required|file|max:51200|mimes:pdf,doc,docx,zip,rar',
        ]);

        $attempt = PracticalExamAttempt::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->where('status', 'pending_submission')
            ->firstOrFail();

        if ($request->hasFile('response_file')) {
            $file = $request->file('response_file');
            $filename = 'practical_exam_' . $attempt->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('practical-exam-responses', $filename, 'public');

            $attempt->update([
                'video_url' => $path,
                'submitted_at' => now(),
                'status' => 'pending_review',
            ]);
        }

        try {
            $student = Auth::user();
            $product = Product::find($productId);
        } catch (\Exception $e) {
            Log::error('Failed to send classroom exam completion notification: ' . $e->getMessage());
        }

        return redirect()->route('student.practical-exam.show', $productId)
            ->with('success', __('messages.response_submitted_successfully'));
    }

    public function downloadCaseFiles($locale, $attemptId)
    {
        $attempt = PracticalExamAttempt::with('trainingCaseFile')
            ->where('id', $attemptId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $file = $attempt->trainingCaseFile;

        if (!$file) {
            return redirect()->back()->with('error', __('messages.no_files_to_download'));
        }

        $adminStoragePath = base_path('../admin/storage/app/public/');
        $filePath = $adminStoragePath . $file->file_path;

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', __('messages.no_files_to_download'));
        }

        $attempt->case_downloaded = true;
        $attempt->case_downloaded_at = now();
        $attempt->save();

        return response()->download($filePath, $file->file_name);
    }
}
