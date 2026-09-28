<?php

namespace App\Services;

use App\Models\StudentVideoExam;
use App\Models\StudentSuccess;
use App\Models\Student;
use App\Models\Product;
use App\Models\Notification;
use App\Mail\VideoExamValidated;
use App\Mail\VideoExamRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class StudentVideoExamService
{
    public function index(Request $request): array
    {
        $query = StudentVideoExam::with(['student', 'product.variations']);

        if ($request->has('student_id') && !empty($request->student_id)) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->has('product_id') && !empty($request->product_id)) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('is_valid', $request->status);
        }

        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('lien', 'like', '%' . $searchTerm . '%')
                  ->orWhere('video_description', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('student', function ($subQuery) use ($searchTerm) {
                      $subQuery->where('nom', 'like', '%' . $searchTerm . '%')
                               ->orWhere('prenom', 'like', '%' . $searchTerm . '%')
                               ->orWhere('email', 'like', '%' . $searchTerm . '%');
                  })
                  ->orWhereHas('product.variations', function ($subQuery) use ($searchTerm) {
                      $subQuery->where('name', 'like', '%' . $searchTerm . '%');
                  });
            });
        }

        $studentVideoExams = $query->orderBy('created_at', 'desc')->paginate(15);
        $students          = Student::all();
        $products          = Product::with('variations')->get();

        return compact('studentVideoExams', 'students', 'products');
    }

    public function byProduct(Request $request): array
    {
        $query = StudentVideoExam::with(['student', 'product.variations']);

        if ($request->has('status') && $request->status !== '') {
            $query->where('is_valid', $request->status);
        }

        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('lien', 'like', '%' . $searchTerm . '%')
                  ->orWhere('video_description', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('student', function ($subQuery) use ($searchTerm) {
                      $subQuery->where('nom', 'like', '%' . $searchTerm . '%')
                               ->orWhere('prenom', 'like', '%' . $searchTerm . '%')
                               ->orWhere('email', 'like', '%' . $searchTerm . '%');
                  })
                  ->orWhereHas('product.variations', function ($subQuery) use ($searchTerm) {
                      $subQuery->where('name', 'like', '%' . $searchTerm . '%');
                  });
            });
        }

        $videoExams        = $query->orderBy('created_at', 'desc')->get();
        $groupedVideoExams = $videoExams->groupBy('product_id');

        return compact('groupedVideoExams');
    }

    public function getCreateData(): array
    {
        return [
            'students' => Student::all(),
            'products' => Product::with('variations')->get(),
        ];
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id'        => 'required|exists:students,id',
            'product_id'        => 'required|exists:products,id',
            'lien'              => 'required|url',
            'video_description' => 'required|string',
            'is_valid'          => 'required|in:0,1,-1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $data                 = $request->only(['student_id', 'product_id', 'lien', 'video_description', 'is_valid']);
            $data['submitted_at'] = now();

            StudentVideoExam::create($data);

            return redirect()->route('student-video-exams.index')->with('success', 'Examen vidéo créé avec succès !');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Erreur lors de la création: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(StudentVideoExam $studentVideoExam): StudentVideoExam
    {
        $studentVideoExam->load(['student', 'product.variations']);
        return $studentVideoExam;
    }

    public function getEditData(StudentVideoExam $studentVideoExam): array
    {
        return [
            'studentVideoExam' => $studentVideoExam,
            'students'         => Student::all(),
            'products'         => Product::with('variations')->get(),
        ];
    }

    public function update(Request $request, StudentVideoExam $studentVideoExam)
    {
        $validator = Validator::make($request->all(), [
            'student_id'        => 'required|exists:students,id',
            'product_id'        => 'required|exists:products,id',
            'lien'              => 'required|url',
            'video_description' => 'required|string',
            'is_valid'          => 'required|in:0,1,-1',
            'admin_notes'       => $request->is_valid == -1 ? 'required|string|min:10|max:1000' : 'nullable|string|max:1000',
        ], [
            'admin_notes.required' => 'Admin notes are required when rejecting a video exam.',
            'admin_notes.min'      => 'Admin notes must be at least 10 characters.',
            'admin_notes.max'      => 'Admin notes must not exceed 1000 characters.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $data      = $request->only(['student_id', 'product_id', 'lien', 'video_description', 'is_valid', 'admin_notes']);
            $oldStatus = $studentVideoExam->is_valid;
            $newStatus = $data['is_valid'];

            $studentVideoExam->update($data);

            if ($request->is_valid == 1) {
                $this->handleStudentSuccessCreation($studentVideoExam);
            }

            if ($oldStatus != $newStatus) {
                try {
                    $studentVideoExam->load(['student', 'product']);

                    if ($newStatus == 1) {
                        Mail::to($studentVideoExam->student->email)->send(new VideoExamValidated($studentVideoExam));

                        \App\Models\EmailLog::logSent(
                            $studentVideoExam->student->email,
                            'video_exam_validated',
                            'Video Exam Validated: ' . ($studentVideoExam->product->titre ?? 'N/A'),
                            $studentVideoExam->student->id,
                            ($studentVideoExam->student->first_name ?? '') . ' ' . ($studentVideoExam->student->last_name ?? ''),
                            'StudentVideoExam',
                            $studentVideoExam->id
                        );

                        DB::commit();
                        return redirect()->route('student-video-exams.index')
                            ->with('success', 'Video exam updated successfully and validation email sent to the student.');
                    } elseif ($newStatus == -1) {
                        Mail::to($studentVideoExam->student->email)->send(new VideoExamRejected($studentVideoExam));

                        \App\Models\EmailLog::logSent(
                            $studentVideoExam->student->email,
                            'video_exam_rejected',
                            'Video Exam Rejected: ' . ($studentVideoExam->product->titre ?? 'N/A'),
                            $studentVideoExam->student->id,
                            ($studentVideoExam->student->first_name ?? '') . ' ' . ($studentVideoExam->student->last_name ?? ''),
                            'StudentVideoExam',
                            $studentVideoExam->id
                        );

                        DB::commit();
                        return redirect()->route('student-video-exams.index')
                            ->with('success', 'Video exam updated successfully and revision notification email sent to the student.');
                    }
                } catch (\Exception $e) {
                    DB::commit();
                    return redirect()->route('student-video-exams.index')
                        ->with('success', 'Video exam updated successfully.')
                        ->with('warning', 'Error sending email: ' . $e->getMessage());
                }
            }

            DB::commit();
            return redirect()->route('student-video-exams.index')->with('success', 'Video exam updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withErrors(['error' => 'Error updating video exam: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(StudentVideoExam $studentVideoExam)
    {
        try {
            $studentVideoExam->delete();
            return redirect()->route('student-video-exams.index')->with('success', 'Examen vidéo supprimé avec succès !');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Erreur lors de la suppression: ' . $e->getMessage()]);
        }
    }

    public function approve(Request $request, StudentVideoExam $studentVideoExam)
    {
        DB::beginTransaction();
        try {
            Log::info('Approve method started', ['video_exam_id' => $studentVideoExam->id]);

            $studentVideoExam->update(['is_valid' => 1]);

            Log::info('Video exam updated to valid');

            $this->handleStudentSuccessCreation($studentVideoExam);

            Log::info('Student success handled');

            try {
                $studentVideoExam->load(['student', 'product']);
                Mail::to($studentVideoExam->student->email)->send(new VideoExamValidated($studentVideoExam));

                \App\Models\EmailLog::logSent(
                    $studentVideoExam->student->email,
                    'video_exam_validated',
                    'Video Exam Validated: ' . ($studentVideoExam->product->titre ?? 'N/A'),
                    $studentVideoExam->student->id,
                    ($studentVideoExam->student->first_name ?? '') . ' ' . ($studentVideoExam->student->last_name ?? ''),
                    'StudentVideoExam',
                    $studentVideoExam->id
                );

                DB::commit();
                Log::info('Video exam approved successfully with email');
                return redirect()->back()
                    ->with('success', 'Video exam validated successfully and notification email sent to the student.');
            } catch (\Exception $e) {
                DB::commit();
                Log::error('Email sending failed: ' . $e->getMessage());
                return redirect()->back()
                    ->with('success', 'Video exam validated successfully.')
                    ->with('warning', 'Error sending email: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Video exam approval failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()
                ->withErrors(['error' => 'Error validating video exam: ' . $e->getMessage()])
                ->with('error', 'Error validating video exam: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, StudentVideoExam $studentVideoExam)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:1000',
        ], [
            'rejection_reason.required' => 'Please provide a reason for rejection.',
            'rejection_reason.min'      => 'The rejection reason must be at least 10 characters.',
            'rejection_reason.max'      => 'The rejection reason must not exceed 1000 characters.',
        ]);

        try {
            $studentVideoExam->update([
                'is_valid'    => -1,
                'admin_notes' => $request->rejection_reason,
            ]);

            try {
                $studentVideoExam->load(['student', 'product']);
                Mail::to($studentVideoExam->student->email)->send(new VideoExamRejected($studentVideoExam));

                \App\Models\EmailLog::logSent(
                    $studentVideoExam->student->email,
                    'video_exam_rejected',
                    'Video Exam Rejected: ' . ($studentVideoExam->product->titre ?? 'N/A'),
                    $studentVideoExam->student->id,
                    ($studentVideoExam->student->first_name ?? '') . ' ' . ($studentVideoExam->student->last_name ?? ''),
                    'StudentVideoExam',
                    $studentVideoExam->id
                );

                return redirect()->back()
                    ->with('success', 'Video exam rejected successfully and revision notification email sent to the student.');
            } catch (\Exception $e) {
                return redirect()->back()
                    ->with('success', 'Video exam rejected successfully.')
                    ->with('warning', 'Error sending email: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Error rejecting video exam: ' . $e->getMessage()]);
        }
    }

    private function handleStudentSuccessCreation(StudentVideoExam $studentVideoExam): void
    {
        $existingSuccess = StudentSuccess::where('student_id', $studentVideoExam->student_id)
            ->where('product_id', $studentVideoExam->product_id)
            ->first();

        if (!$existingSuccess) {
            $studentSuccess = StudentSuccess::create([
                'student_id'   => $studentVideoExam->student_id,
                'product_id'   => $studentVideoExam->product_id,
                'lien_video'   => $studentVideoExam->lien,
                'success'      => 0,
                'admin_notes'  => 'Créé automatiquement lors de la validation de l\'examen vidéo',
                'submitted_at' => now(),
            ]);

            $student = Student::find($studentVideoExam->student_id);
            $product = Product::find($studentVideoExam->product_id);

            if ($student && $product) {
                Notification::notifyAllAdmins(
                    Notification::TYPE_EVALUATION,
                    'Video Exam Submitted - ' . $student->first_name . ' ' . $student->last_name,
                    $student->first_name . ' ' . $student->last_name . ' submitted a video exam for "' . $product->titre . '"',
                    route('student-successes.show', $studentSuccess->id),
                    ['student_success_id' => $studentSuccess->id, 'exam_type' => 'video'],
                    '🎥',
                    'blue',
                    true
                );
            }
        }
    }
}
