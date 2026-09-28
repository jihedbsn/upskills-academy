<?php

namespace App\Services;

use App\Models\StudentStageCourse;
use App\Models\Student;
use App\Models\Product;
use App\Mail\StageValidated;
use App\Mail\StageRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StudentStageCourseService
{
    public function index(Request $request): array
    {
        $query = StudentStageCourse::with(['student', 'product.variations']);

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
                $q->where('description', 'like', '%' . $searchTerm . '%')
                  ->orWhere('admin_notes', 'like', '%' . $searchTerm . '%')
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

        $studentStageCourses = $query->orderBy('created_at', 'desc')->paginate(15);
        $students             = Student::all();
        $products             = Product::with('variations')->get();

        return compact('studentStageCourses', 'students', 'products');
    }

    public function byProduct(Request $request): array
    {
        $query = StudentStageCourse::with(['student', 'product.variations']);

        if ($request->has('status') && $request->status !== '') {
            $query->where('is_valid', $request->status);
        }

        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('description', 'like', '%' . $searchTerm . '%')
                  ->orWhere('admin_notes', 'like', '%' . $searchTerm . '%')
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

        $submissions        = $query->orderBy('created_at', 'desc')->get();
        $groupedSubmissions = $submissions->groupBy('product_id');

        return compact('groupedSubmissions');
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
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'student_id'   => 'required|exists:students,id',
            'product_id'   => 'required|exists:products,id',
            'description'  => 'required|string',
            'file1'        => 'nullable|file|mimes:pdf,jpg,jpeg,png,gif|max:10240',
            'file2'        => 'nullable|file|mimes:pdf,jpg,jpeg,png,gif|max:10240',
            'is_valid'     => 'required|in:0,1,-1',
            'admin_notes'  => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $data                 = $request->only(['student_id', 'product_id', 'description', 'is_valid', 'admin_notes']);
            $data['submitted_at'] = now();

            if ($request->hasFile('file1')) {
                $file1              = $request->file('file1');
                $filename1          = time() . '_' . $file1->getClientOriginalName();
                $file1->storeAs('public/student_stage_courses', $filename1);
                $data['file1']      = $filename1;
            }

            if ($request->hasFile('file2')) {
                $file2              = $request->file('file2');
                $filename2          = time() . '_' . $file2->getClientOriginalName();
                $file2->storeAs('public/student_stage_courses', $filename2);
                $data['file2']      = $filename2;
            }

            if ($data['is_valid'] == 1) {
                $data['validated_at'] = now();
            }

            StudentStageCourse::create($data);

            return redirect()->route('student-stage-courses.index')
                ->with('success', 'Soumission de stage créée avec succès !');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Erreur lors de la création: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(StudentStageCourse $studentStageCourse): StudentStageCourse
    {
        $studentStageCourse->load(['student', 'product.variations']);
        return $studentStageCourse;
    }

    public function getEditData(StudentStageCourse $studentStageCourse): array
    {
        return [
            'studentStageCourse' => $studentStageCourse,
            'students'           => Student::all(),
            'products'           => Product::with('variations')->get(),
        ];
    }

    public function update(Request $request, StudentStageCourse $studentStageCourse)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'student_id'       => 'required|exists:students,id',
            'product_id'       => 'required|exists:products,id',
            'description'      => 'required|string',
            'file1'            => 'nullable|file|mimes:pdf,jpg,jpeg,png,gif|max:10240',
            'file2'            => 'nullable|file|mimes:pdf,jpg,jpeg,png,gif|max:10240',
            'is_valid'         => 'required|in:0,1,-1',
            'admin_notes'      => 'nullable|string',
            'approval_message' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $data      = $request->only(['student_id', 'product_id', 'description', 'is_valid', 'admin_notes']);
            $oldStatus = $studentStageCourse->is_valid;
            $newStatus = $data['is_valid'];

            if ($request->hasFile('file1')) {
                if ($studentStageCourse->file1) {
                    Storage::delete('public/student_stage_courses/' . $studentStageCourse->file1);
                }
                $file1         = $request->file('file1');
                $filename1     = time() . '_' . $file1->getClientOriginalName();
                $file1->storeAs('public/student_stage_courses', $filename1);
                $data['file1'] = $filename1;
            }

            if ($request->hasFile('file2')) {
                if ($studentStageCourse->file2) {
                    Storage::delete('public/student_stage_courses/' . $studentStageCourse->file2);
                }
                $file2         = $request->file('file2');
                $filename2     = time() . '_' . $file2->getClientOriginalName();
                $file2->storeAs('public/student_stage_courses', $filename2);
                $data['file2'] = $filename2;
            }

            if ($data['is_valid'] == 1 && $studentStageCourse->is_valid != 1) {
                $data['validated_at'] = now();
            }

            $studentStageCourse->update($data);

            if ($oldStatus != $newStatus) {
                try {
                    $studentStageCourse->refresh();
                    $studentStageCourse->load(['student', 'product']);

                    if ($newStatus == StudentStageCourse::STATUS_VALIDATED) {
                        Mail::to($studentStageCourse->student->email)->send(new StageValidated($studentStageCourse));

                        \App\Models\EmailLog::logSent(
                            $studentStageCourse->student->email,
                            'stage_validated',
                            'Internship Validated: ' . ($studentStageCourse->product->titre ?? 'N/A'),
                            $studentStageCourse->student->id,
                            ($studentStageCourse->student->first_name ?? '') . ' ' . ($studentStageCourse->student->last_name ?? ''),
                            'StudentStageCourse',
                            $studentStageCourse->id
                        );

                        try {
                            \App\Models\Notification::notifyStudent(
                                $studentStageCourse->student_id,
                                \App\Models\Notification::TYPE_EVALUATION,
                                'Internship submission approved',
                                $studentStageCourse->approval_message ?: 'Your internship submission was approved.',
                                config('app.user_url') . '/courses/' . $studentStageCourse->product_id,
                                [
                                    'submission_id'    => $studentStageCourse->id,
                                    'product_id'       => $studentStageCourse->product_id,
                                    'status'           => 1,
                                    'approval_message' => $studentStageCourse->approval_message,
                                ],
                                '✅',
                                'green',
                                true
                            );
                        } catch (\Exception $ne) {
                            // swallow notification errors
                        }

                        return redirect()->route('student-stage-courses.index')
                            ->with('success', 'Internship updated successfully and validation email sent to the student.');
                    } elseif ($newStatus == StudentStageCourse::STATUS_REJECTED) {
                        Mail::to($studentStageCourse->student->email)->send(new StageRejected($studentStageCourse));

                        \App\Models\EmailLog::logSent(
                            $studentStageCourse->student->email,
                            'stage_rejected',
                            'Internship Rejected: ' . ($studentStageCourse->product->titre ?? 'N/A'),
                            $studentStageCourse->student->id,
                            ($studentStageCourse->student->first_name ?? '') . ' ' . ($studentStageCourse->student->last_name ?? ''),
                            'StudentStageCourse',
                            $studentStageCourse->id
                        );

                        try {
                            $rejectionMessage = $studentStageCourse->admin_notes
                                ? 'Reason: ' . Str::limit($studentStageCourse->admin_notes, 100, '...')
                                : 'Please review your submission and resubmit.';

                            \App\Models\Notification::notifyStudent(
                                $studentStageCourse->student_id,
                                \App\Models\Notification::TYPE_EVALUATION,
                                'Internship submission rejected',
                                $rejectionMessage,
                                config('app.user_url') . '/courses/' . $studentStageCourse->product_id,
                                [
                                    'submission_id' => $studentStageCourse->id,
                                    'product_id'    => $studentStageCourse->product_id,
                                    'status'        => -1,
                                    'admin_notes'   => $studentStageCourse->admin_notes,
                                ],
                                '❌',
                                'red',
                                true
                            );
                        } catch (\Exception $ne) {
                            // swallow notification errors
                        }

                        return redirect()->route('student-stage-courses.index')
                            ->with('success', 'Internship updated successfully and revision notification email sent to the student.');
                    }
                } catch (\Exception $e) {
                    return redirect()->route('student-stage-courses.index')
                        ->with('success', 'Internship updated successfully.')
                        ->with('warning', 'Error sending email: ' . $e->getMessage());
                }
            }

            return redirect()->route('student-stage-courses.index')
                ->with('success', 'Internship updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Error updating internship: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(StudentStageCourse $studentStageCourse)
    {
        try {
            if ($studentStageCourse->file1) {
                Storage::delete('public/student_stage_courses/' . $studentStageCourse->file1);
            }
            if ($studentStageCourse->file2) {
                Storage::delete('public/student_stage_courses/' . $studentStageCourse->file2);
            }

            $studentStageCourse->delete();

            return redirect()->route('student-stage-courses.index')
                ->with('success', 'Soumission de stage supprimée avec succès !');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Erreur lors de la suppression: ' . $e->getMessage()]);
        }
    }

    public function downloadFile(StudentStageCourse $studentStageCourse, $fileNumber)
    {
        $fileField = 'file' . $fileNumber;
        $filename  = $studentStageCourse->$fileField;

        if (!$filename) {
            abort(404, 'Fichier non trouvé');
        }

        $userAppPath      = base_path('../user/public/uploads/student_stage_courses/' . $filename);
        $adminStoragePath = storage_path('app/public/student_stage_courses/' . $filename);

        if (file_exists($userAppPath)) {
            return response()->download($userAppPath, $filename);
        } elseif (file_exists($adminStoragePath)) {
            return response()->download($adminStoragePath, $filename);
        }

        abort(404, 'Fichier non trouvé sur le serveur');
    }

    public function validateSubmission(Request $request, StudentStageCourse $studentStageCourse)
    {
        $request->validate(['approval_message' => 'nullable|string|max:1000']);

        try {
            $studentStageCourse->update([
                'is_valid'         => StudentStageCourse::STATUS_VALIDATED,
                'validated_at'     => now(),
                'approval_message' => $request->approval_message,
            ]);

            try {
                $studentStageCourse->load(['student', 'product']);
                Mail::to($studentStageCourse->student->email)->send(new StageValidated($studentStageCourse));

                \App\Models\EmailLog::logSent(
                    $studentStageCourse->student->email,
                    'stage_validated',
                    'Internship Validated: ' . ($studentStageCourse->product->titre ?? 'N/A'),
                    $studentStageCourse->student->id,
                    ($studentStageCourse->student->first_name ?? '') . ' ' . ($studentStageCourse->student->last_name ?? ''),
                    'StudentStageCourse',
                    $studentStageCourse->id
                );

                try {
                    \App\Models\Notification::notifyStudent(
                        $studentStageCourse->student_id,
                        \App\Models\Notification::TYPE_EVALUATION,
                        'Internship submission approved',
                        $request->approval_message ?: 'Your internship submission was approved.',
                        config('app.user_url') . '/courses/' . $studentStageCourse->product_id,
                        [
                            'submission_id'    => $studentStageCourse->id,
                            'product_id'       => $studentStageCourse->product_id,
                            'status'           => 1,
                            'approval_message' => $request->approval_message,
                        ],
                        '✅',
                        'green',
                        true
                    );
                } catch (\Exception $ne) {
                    // swallow
                }

                return redirect()->back()
                    ->with('success', 'Internship validated successfully and notification email sent to the student.');
            } catch (\Exception $e) {
                return redirect()->back()
                    ->with('success', 'Internship validated successfully.')
                    ->with('warning', 'Error sending email: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Error validating internship: ' . $e->getMessage()]);
        }
    }

    public function rejectSubmission(Request $request, StudentStageCourse $studentStageCourse)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:1000',
        ], [
            'rejection_reason.required' => 'Please provide a reason for rejection.',
            'rejection_reason.min'      => 'The rejection reason must be at least 10 characters.',
            'rejection_reason.max'      => 'The rejection reason must not exceed 1000 characters.',
        ]);

        try {
            $studentStageCourse->update([
                'is_valid'    => StudentStageCourse::STATUS_REJECTED,
                'admin_notes' => $request->rejection_reason,
            ]);

            try {
                $studentStageCourse->load(['student', 'product']);
                Mail::to($studentStageCourse->student->email)->send(new StageRejected($studentStageCourse));

                \App\Models\EmailLog::logSent(
                    $studentStageCourse->student->email,
                    'stage_rejected',
                    'Internship Rejected: ' . ($studentStageCourse->product->titre ?? 'N/A'),
                    $studentStageCourse->student->id,
                    ($studentStageCourse->student->first_name ?? '') . ' ' . ($studentStageCourse->student->last_name ?? ''),
                    'StudentStageCourse',
                    $studentStageCourse->id
                );

                try {
                    $rejectionMessage = 'Reason: ' . Str::limit($request->rejection_reason, 100, '...');

                    \App\Models\Notification::notifyStudent(
                        $studentStageCourse->student_id,
                        \App\Models\Notification::TYPE_EVALUATION,
                        'Internship submission rejected',
                        $rejectionMessage,
                        config('app.user_url') . '/courses/' . $studentStageCourse->product_id,
                        [
                            'submission_id' => $studentStageCourse->id,
                            'product_id'    => $studentStageCourse->product_id,
                            'status'        => -1,
                            'admin_notes'   => $request->rejection_reason,
                        ],
                        '❌',
                        'red',
                        true
                    );
                } catch (\Exception $ne) {
                    // swallow
                }

                return redirect()->back()
                    ->with('success', 'Internship rejected successfully and revision notification email sent to the student.');
            } catch (\Exception $e) {
                return redirect()->back()
                    ->with('success', 'Internship rejected successfully.')
                    ->with('warning', 'Error sending email: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Error rejecting internship: ' . $e->getMessage()]);
        }
    }
}
