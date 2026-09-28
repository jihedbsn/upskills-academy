<?php

namespace App\Services;

use App\Models\StudentSuccess;
use App\Models\StudentVideoExam;
use App\Models\StudentStageCourse;
use App\Models\ResultatQuiz;
use App\Models\Product;
use App\Models\Student;
use App\Models\Notification;
use App\Mail\StudentSuccessApproved as StudentSuccessApprovedMail;
use App\Mail\StudentSuccessRejected;
use App\Services\CertificateGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mail;
use Illuminate\Support\Facades\Log;

class StudentSuccessService
{
    public function index(Request $request): array
    {
        $query = StudentSuccess::with(['student', 'product.variations']);

        if ($request->filled('status')) {
            switch ($request->status) {
                case 'pending':
                    $query->where('success', 0);
                    break;
                case 'approved':
                    $query->where('success', 1);
                    break;
                case 'rejected':
                    $query->where('success', -1);
                    break;
            }
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($studentQuery) use ($search) {
                    $studentQuery->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('product.variations', function ($productQuery) use ($search) {
                    $productQuery->where('name', 'like', "%{$search}%");
                });
            });
        }

        $studentSuccesses = $query->orderBy('created_at', 'desc')->paginate(15);
        $products         = Product::with('variations')->get();

        return compact('studentSuccesses', 'products');
    }

    public function getCreateData(): array
    {
        return [
            'products' => Product::with('variations')->get(),
            'students' => Student::all(),
        ];
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id'  => 'required|exists:students,id',
            'product_id'  => 'required|exists:products,id',
            'lien_video'  => 'nullable|url',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            StudentSuccess::create([
                'student_id'   => $request->student_id,
                'product_id'   => $request->product_id,
                'lien_video'   => $request->lien_video,
                'admin_notes'  => $request->admin_notes,
                'success'      => 0,
                'submitted_at' => now(),
            ]);

            DB::commit();
            return redirect()->route('student-successes.index')->with('success', 'Succès étudiant créé avec succès.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Erreur lors de la création du succès étudiant.')->withInput();
        }
    }

    public function show(StudentSuccess $studentSuccess): StudentSuccess
    {
        $studentSuccess->load(['student', 'product.variations']);
        return $studentSuccess;
    }

    public function getEditData(StudentSuccess $studentSuccess): array
    {
        $studentSuccess->load(['student', 'product.variations']);

        $quizResults = ResultatQuiz::with(['quiz', 'product'])
            ->where('student_id', $studentSuccess->student_id)
            ->where('product_id', $studentSuccess->product_id)
            ->orderBy('created_at', 'desc')
            ->get();

        $stageSubmissions = StudentStageCourse::with(['product.variations'])
            ->where('student_id', $studentSuccess->student_id)
            ->where('product_id', $studentSuccess->product_id)
            ->orderBy('created_at', 'desc')
            ->get();

        $videoExams = StudentVideoExam::with(['product.variations'])
            ->where('student_id', $studentSuccess->student_id)
            ->where('product_id', $studentSuccess->product_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'studentSuccess'   => $studentSuccess,
            'products'         => Product::with('variations')->get(),
            'students'         => Student::all(),
            'quizResults'      => $quizResults,
            'stageSubmissions' => $stageSubmissions,
            'videoExams'       => $videoExams,
        ];
    }

    public function update(Request $request, StudentSuccess $studentSuccess)
    {
        $request->validate([
            'lien_video'  => 'nullable|url',
            'admin_notes' => 'nullable|string|max:1000',
            'success'     => 'required|in:0,1,-1',
        ]);

        DB::beginTransaction();
        try {
            $updateData = [
                'lien_video'  => $request->lien_video,
                'admin_notes' => $request->admin_notes,
                'success'     => $request->success,
            ];

            $oldStatus = $studentSuccess->success;
            $newStatus = $request->success;

            if ($oldStatus != $newStatus) {
                $updateData['validated_at'] = now();
            }

            $studentSuccess->update($updateData);

            if ($oldStatus != $newStatus) {
                try {
                    $studentSuccess->load(['student', 'product']);

                    if ($newStatus == 1) {
                        $message = 'Student success updated successfully and approval email sent to the student.';

                        Notification::notifyAllAdmins(
                            Notification::TYPE_STUDENT_SUCCESS,
                            'Student Success Approved',
                            $studentSuccess->student->first_name . ' ' . $studentSuccess->student->last_name . ' passed "' . $studentSuccess->product->title . '"',
                            route('admin.student-successes.show', $studentSuccess->id),
                            ['student_success_id' => $studentSuccess->id],
                            '🎓',
                            'green',
                            true
                        );

                        if ($studentSuccess->product->certif_id) {
                            $certificateService = new CertificateGeneratorService();

                            if (!$certificateService->certificateExists($studentSuccess)) {
                                try {
                                    $certificate = $certificateService->generateCertificate($studentSuccess);
                                    $message    .= ' Certificate generated automatically.';

                                    try {
                                        \Mail::to($studentSuccess->student->email)->send(new \App\Mail\CertificateReady($studentSuccess, $certificate));
                                        $message .= ' Certificate notification email sent.';

                                        \App\Models\EmailLog::logSent(
                                            $studentSuccess->student->email,
                                            'certificate_ready',
                                            'Certificate Ready: ' . ($studentSuccess->product->titre ?? 'N/A'),
                                            $studentSuccess->student->id,
                                            ($studentSuccess->student->first_name ?? '') . ' ' . ($studentSuccess->student->last_name ?? ''),
                                            'Certificate',
                                            $certificate->id
                                        );
                                    } catch (\Exception $emailError) {
                                        $message .= ' Error sending certificate notification: ' . $emailError->getMessage();

                                        \App\Models\EmailLog::logFailed(
                                            $studentSuccess->student->email ?? 'unknown',
                                            'certificate_ready',
                                            'Certificate Ready: ' . ($studentSuccess->product->titre ?? 'N/A'),
                                            $emailError->getMessage(),
                                            $studentSuccess->student->id ?? null,
                                            ($studentSuccess->student->first_name ?? '') . ' ' . ($studentSuccess->student->last_name ?? ''),
                                            'Certificate',
                                            $certificate->id ?? null
                                        );
                                    }
                                } catch (\Exception $certError) {
                                    $message .= ' Error generating certificate: ' . $certError->getMessage();
                                }
                            } else {
                                $message .= ' Certificate already exists.';
                            }
                        } else {
                            $message .= ' No certificate template associated with this product.';
                        }

                        // NOTE: uses unqualified StudentSuccessApproved (pre-existing behaviour)
                        Mail::to($studentSuccess->student->email)->send(new \App\Mail\StudentSuccessApproved($studentSuccess));

                        \App\Models\EmailLog::logSent(
                            $studentSuccess->student->email,
                            'student_success_approved',
                            'Course Completion Approved: ' . ($studentSuccess->product->titre ?? 'N/A'),
                            $studentSuccess->student->id,
                            ($studentSuccess->student->first_name ?? '') . ' ' . ($studentSuccess->student->last_name ?? ''),
                            'StudentSuccess',
                            $studentSuccess->id
                        );

                        DB::commit();
                        return redirect()->route('student-successes.index')->with('success', $message);
                    } elseif ($newStatus == -1) {
                        Mail::to($studentSuccess->student->email)->send(new StudentSuccessRejected($studentSuccess));

                        \App\Models\EmailLog::logSent(
                            $studentSuccess->student->email,
                            'student_success_rejected',
                            'Course Completion Rejected: ' . ($studentSuccess->product->titre ?? 'N/A'),
                            $studentSuccess->student->id,
                            ($studentSuccess->student->first_name ?? '') . ' ' . ($studentSuccess->student->last_name ?? ''),
                            'StudentSuccess',
                            $studentSuccess->id
                        );

                        DB::commit();
                        return redirect()->route('student-successes.index')
                            ->with('success', 'Student success updated successfully and requirements notification email sent to the student.');
                    }
                } catch (\Exception $e) {
                    DB::commit();
                    return redirect()->route('student-successes.index')
                        ->with('success', 'Student success updated successfully.')
                        ->with('warning', 'Error sending email: ' . $e->getMessage());
                }
            }

            DB::commit();
            return redirect()->route('student-successes.index')->with('success', 'Student success updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error updating student success: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(StudentSuccess $studentSuccess)
    {
        DB::beginTransaction();
        try {
            $studentSuccess->delete();
            DB::commit();
            return redirect()->route('student-successes.index')->with('success', 'Succès étudiant supprimé avec succès.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Erreur lors de la suppression du succès étudiant.');
        }
    }

    public function validate(StudentSuccess $studentSuccess)
    {
        DB::beginTransaction();
        try {
            $studentSuccess->approve();
            $message = 'Student success approved successfully.';

            $studentSuccess->load(['student', 'product']);

            Notification::notifyAllAdmins(
                Notification::TYPE_STUDENT_SUCCESS,
                'Student Success Approved - ' . $studentSuccess->student->first_name . ' ' . $studentSuccess->student->last_name,
                $studentSuccess->student->first_name . ' ' . $studentSuccess->student->last_name . ' passed "' . $studentSuccess->product->title . '"',
                route('admin.student-successes.show', $studentSuccess->id),
                ['student_success_id' => $studentSuccess->id],
                '🎓',
                'green',
                true
            );

            Log::info('🔍 VALIDATE - Checking certificate generation', [
                'student_success_id'         => $studentSuccess->id,
                'product_id'                 => $studentSuccess->product_id,
                'certif_id'                  => $studentSuccess->product->certif_id,
                'certificate_generation_mode' => $studentSuccess->product->certificate_generation_mode,
                'is_automatic'               => $studentSuccess->product->certificate_generation_mode === 'automatic',
            ]);

            if ($studentSuccess->product->certif_id) {
                if ($studentSuccess->product->certificate_generation_mode === 'automatic') {
                    Log::info('✅ AUTOMATIC MODE - Generating certificate and sending email');
                    try {
                        $certificateService = new CertificateGeneratorService();

                        if (!$certificateService->certificateExists($studentSuccess)) {
                            $certificate = $certificateService->generateCertificate($studentSuccess);

                            Log::info('Certificate generated automatically', [
                                'student_success_id' => $studentSuccess->id,
                                'certificate_id'     => $certificate->id,
                                'serial_number'      => $certificate->serial_number,
                            ]);

                            Mail::to($studentSuccess->student->email)
                                ->send(new \App\Mail\CertificateGeneratedNotification($certificate, $studentSuccess));

                            \App\Models\EmailLog::logSent(
                                $studentSuccess->student->email,
                                'certificate_generated',
                                'Certificate Generated: ' . ($studentSuccess->product->titre ?? 'N/A'),
                                $studentSuccess->student->id,
                                ($studentSuccess->student->first_name ?? '') . ' ' . ($studentSuccess->student->last_name ?? ''),
                                'Certificate',
                                $certificate->id
                            );

                            Log::info('Certificate email sent', ['student_email' => $studentSuccess->student->email]);
                            $message .= ' Certificate generated and sent to student automatically.';
                        } else {
                            $message .= ' Certificate already exists.';
                        }
                    } catch (\Exception $certError) {
                        Log::error('Failed to generate automatic certificate', [
                            'error'              => $certError->getMessage(),
                            'student_success_id' => $studentSuccess->id,
                        ]);
                        $message .= ' Error generating certificate: ' . $certError->getMessage();
                    }
                } else {
                    Log::info('📋 MANUAL MODE - Certificate left as pending');
                    $message .= ' Certificate is pending manual generation by admin.';

                    \App\Models\Notification::notifyAllAdmins(
                        \App\Models\Notification::TYPE_MANUAL_CERTIFICATE,
                        'Manual Certificate Required',
                        "Certificate needs to be generated for {$studentSuccess->student->fullname} - {$studentSuccess->product->titre}",
                        route('admin.student-successes.show', $studentSuccess->id),
                        [
                            'student_success_id' => $studentSuccess->id,
                            'student_name'       => $studentSuccess->student->fullname,
                            'course_name'        => $studentSuccess->product->titre,
                        ],
                        'fa-certificate',
                        'orange'
                    );
                }
            } else {
                $message .= ' No certificate associated with this course.';
            }

            try {
                $studentSuccess->load(['student', 'product']);
                Mail::to($studentSuccess->student->email)->send(new StudentSuccessApprovedMail($studentSuccess));

                \App\Models\EmailLog::logSent(
                    $studentSuccess->student->email,
                    'student_success_approved',
                    'Course Completion Approved: ' . ($studentSuccess->product->titre ?? 'N/A'),
                    $studentSuccess->student->id,
                    ($studentSuccess->student->first_name ?? '') . ' ' . ($studentSuccess->student->last_name ?? ''),
                    'StudentSuccess',
                    $studentSuccess->id
                );

                $message .= ' Approval email sent to the student.';
            } catch (\Exception $e) {
                $message .= ' Error sending email: ' . $e->getMessage();
            }

            DB::commit();
            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error approving student success: ' . $e->getMessage());
        }
    }

    public function reject(StudentSuccess $studentSuccess)
    {
        DB::beginTransaction();
        try {
            $studentSuccess->reject();

            try {
                $studentSuccess->load(['student', 'product']);
                Mail::to($studentSuccess->student->email)->send(new StudentSuccessRejected($studentSuccess));

                \App\Models\EmailLog::logSent(
                    $studentSuccess->student->email,
                    'student_success_rejected',
                    'Course Completion Rejected: ' . ($studentSuccess->product->titre ?? 'N/A'),
                    $studentSuccess->student->id,
                    ($studentSuccess->student->first_name ?? '') . ' ' . ($studentSuccess->student->last_name ?? ''),
                    'StudentSuccess',
                    $studentSuccess->id
                );

                DB::commit();
                return back()->with('success', 'Student success rejected successfully and requirements notification email sent to the student.');
            } catch (\Exception $e) {
                DB::commit();
                return back()
                    ->with('success', 'Student success rejected successfully.')
                    ->with('warning', 'Error sending email: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error rejecting student success: ' . $e->getMessage());
        }
    }

    public function downloadCertificate(StudentSuccess $studentSuccess)
    {
        $certificateService = new CertificateGeneratorService();
        $certificate        = $certificateService->getCertificate($studentSuccess);

        if (!$certificate || !$certificate->file_path) {
            return back()->with('error', 'Certificat non trouvé.');
        }

        $filePath = public_path($certificate->file_path);

        if (!file_exists($filePath)) {
            return back()->with('error', 'Fichier certificat non trouvé.');
        }

        return response()->download($filePath, 'certificat_' . $certificate->serial_number . '.png');
    }

    public function testGenerateCertificate(StudentSuccess $studentSuccess)
    {
        try {
            if (!$studentSuccess->product->certif_id) {
                return back()->with('error', 'Aucun certificat associé à ce produit. Veuillez d\'abord associer un certificat au produit.');
            }

            $certificateService = new CertificateGeneratorService();

            Log::info('📊 GÉNÉRATION - Données avant génération depuis StudentSuccess:', [
                'student_success_id'        => $studentSuccess->id,
                'certif_id'                 => $studentSuccess->product->certif_id,
                'template_data_raw'         => $studentSuccess->product->certif->template_data,
                'template_data_json'        => json_encode($studentSuccess->product->certif->template_data),
                'template_data_type'        => gettype($studentSuccess->product->certif->template_data),
                'name_student_position'     => $studentSuccess->product->certif->template_data['name_student'] ?? 'NOT_FOUND',
                'date_position'             => $studentSuccess->product->certif->template_data['date'] ?? 'NOT_FOUND',
                'serial_position'           => $studentSuccess->product->certif->template_data['serial_number'] ?? 'NOT_FOUND',
                'qr_position'               => $studentSuccess->product->certif->template_data['qr_code'] ?? 'NOT_FOUND',
            ]);

            $studentName  = $studentSuccess->student->first_name . ' ' . $studentSuccess->student->last_name;
            $currentDate  = now()->format('d/m/Y');
            $serialNumber = 'TEST-' . $studentSuccess->id . '-' . time();

            $testResult = $certificateService->generateTestCertificateWithRealData(
                $studentSuccess,
                $studentName,
                $currentDate,
                $serialNumber
            );

            session(['test_certificate_file' => $testResult['file_path']]);

            return back()->with('success', 'Certificat de test généré avec succès ! Numéro de série: ' . $testResult['serial_number'] . ' - <a href="' . route('student-successes.download-test-certificate', $studentSuccess) . '">Télécharger le certificat de test</a>');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la génération de test: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la génération du certificat de test: ' . $e->getMessage());
        }
    }

    public function testGenerateCertificateAjax(StudentSuccess $studentSuccess)
    {
        try {
            if (!$studentSuccess->product->certif_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun certificat associé à ce produit. Veuillez d\'abord associer un certificat au produit.',
                ], 400);
            }

            $certificateService = new CertificateGeneratorService();

            Log::info('📊 GÉNÉRATION - Données avant génération depuis StudentSuccess:', [
                'student_success_id'    => $studentSuccess->id,
                'certif_id'             => $studentSuccess->product->certif_id,
                'template_data_raw'     => $studentSuccess->product->certif->template_data,
                'template_data_json'    => json_encode($studentSuccess->product->certif->template_data),
                'template_data_type'    => gettype($studentSuccess->product->certif->template_data),
                'name_student_position' => $studentSuccess->product->certif->template_data['name_student'] ?? 'NOT_FOUND',
                'date_position'         => $studentSuccess->product->certif->template_data['date'] ?? 'NOT_FOUND',
                'serial_position'       => $studentSuccess->product->certif->template_data['serial_number'] ?? 'NOT_FOUND',
                'qr_position'           => $studentSuccess->product->certif->template_data['qr_code'] ?? 'NOT_FOUND',
            ]);

            $studentName  = $studentSuccess->student->first_name . ' ' . $studentSuccess->student->last_name;
            $currentDate  = now()->format('d/m/Y');
            $serialNumber = 'TEST-' . $studentSuccess->id . '-' . time();

            $testResult = $certificateService->generateTestCertificateWithRealData(
                $studentSuccess,
                $studentName,
                $currentDate,
                $serialNumber
            );

            session(['test_certificate_file' => $testResult['file_path']]);

            return response()->json([
                'success'      => true,
                'message'      => 'Certificat de test généré avec succès !',
                'serial_number' => $serialNumber,
                'download_url' => route('student-successes.download-test-certificate', $studentSuccess),
                'student_name' => $studentName,
                'date'         => $currentDate,
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la génération de test AJAX: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération du certificat de test: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function downloadTestCertificate(StudentSuccess $studentSuccess)
    {
        $testFileName = session('test_certificate_file');

        if (!$testFileName) {
            return back()->with('error', 'Aucun certificat de test trouvé. Veuillez d\'abord générer un certificat de test.');
        }

        $filePath = public_path('upload/certif-student/' . $testFileName);

        if (!file_exists($filePath)) {
            return back()->with('error', 'Fichier certificat de test non trouvé.');
        }

        return response()->download($filePath, 'certificat_test_' . $studentSuccess->id . '.png');
    }

    public function byProduct(Request $request)
    {
        $query = StudentSuccess::with(['student', 'product.variations']);

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('status')) {
            switch ($request->status) {
                case 'pending':
                    $query->where('success', 0);
                    break;
                case 'approved':
                    $query->where('success', 1);
                    break;
                case 'rejected':
                    $query->where('success', -1);
                    break;
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($studentQuery) use ($search) {
                    $studentQuery->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('product.variations', function ($productQuery) use ($search) {
                    $productQuery->where('name', 'like', "%{$search}%");
                });
            });
        }

        $studentSuccesses = $query->orderBy('created_at', 'desc')->get();

        if ($request->ajax()) {
            return response()->json([
                'success'           => true,
                'student_successes' => $studentSuccesses->map(function ($success) {
                    return [
                        'id'           => $success->id,
                        'student_name' => $success->student->first_name . ' ' . $success->student->last_name,
                        'product_name' => $success->product->variation_title ?? $success->product->titre,
                        'success'      => $success->success,
                    ];
                }),
            ]);
        }

        $groupedByProduct = $studentSuccesses->groupBy('product_id');
        return view('student-successes.by-product', compact('groupedByProduct'));
    }

    public function generateCertificateDirect(StudentSuccess $studentSuccess)
    {
        try {
            if (!$studentSuccess->product->certif_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun certificat associé à ce produit. Veuillez d\'abord associer un certificat au produit.',
                ], 400);
            }

            $approvalMessage = '';
            if ($studentSuccess->success != 1) {
                $studentSuccess->update(['success' => 1, 'validated_at' => now()]);

                try {
                    $studentSuccess->load(['student', 'product']);
                    \Mail::to($studentSuccess->student->email)->send(new \App\Mail\StudentSuccessApproved($studentSuccess));
                    $approvalMessage = ' Student success approved automatically and approval email sent.';

                    \App\Models\EmailLog::logSent(
                        $studentSuccess->student->email,
                        'student_success_approved',
                        'Course Completion Approved: ' . ($studentSuccess->product->titre ?? 'N/A'),
                        $studentSuccess->student->id,
                        ($studentSuccess->student->first_name ?? '') . ' ' . ($studentSuccess->student->last_name ?? ''),
                        'StudentSuccess',
                        $studentSuccess->id
                    );
                } catch (\Exception $e) {
                    $approvalMessage = ' Student success approved automatically but error sending approval email: ' . $e->getMessage();
                }
            }

            $certificateService = new CertificateGeneratorService();

            if ($certificateService->certificateExists($studentSuccess)) {
                $existingCertificate = $certificateService->getCertificate($studentSuccess);

                return response()->json([
                    'success'        => true,
                    'already_exists' => true,
                    'message'        => 'Un certificat existe déjà pour ce succès étudiant.',
                    'certificate'    => [
                        'id'           => $existingCertificate->id,
                        'serial_number' => $existingCertificate->serial_number,
                        'download_url' => route('student-successes.download-certificate', $studentSuccess),
                    ],
                ]);
            }

            $certificate  = $certificateService->generateCertificate($studentSuccess);
            $emailMessage = '';

            try {
                \Mail::to($studentSuccess->student->email)->send(new \App\Mail\CertificateReady($studentSuccess, $certificate));
                $emailMessage = ' Certificate notification email sent to student.';

                \App\Models\EmailLog::logSent(
                    $studentSuccess->student->email,
                    'certificate_ready',
                    'Certificate Ready: ' . ($studentSuccess->product->titre ?? 'N/A'),
                    $studentSuccess->student->id,
                    ($studentSuccess->student->first_name ?? '') . ' ' . ($studentSuccess->student->last_name ?? ''),
                    'Certificate',
                    $certificate->id
                );
            } catch (\Exception $emailError) {
                $emailMessage = ' Error sending certificate notification: ' . $emailError->getMessage();

                \App\Models\EmailLog::logFailed(
                    $studentSuccess->student->email ?? 'unknown',
                    'certificate_ready',
                    'Certificate Ready: ' . ($studentSuccess->product->titre ?? 'N/A'),
                    $emailError->getMessage(),
                    $studentSuccess->student->id ?? null,
                    ($studentSuccess->student->first_name ?? '') . ' ' . ($studentSuccess->student->last_name ?? ''),
                    'Certificate',
                    $certificate->id ?? null
                );
            }

            return response()->json([
                'success'     => true,
                'message'     => 'Certificat généré avec succès !' . $approvalMessage . $emailMessage,
                'certificate' => [
                    'id'           => $certificate->id,
                    'serial_number' => $certificate->serial_number,
                    'download_url' => route('student-successes.download-certificate', $studentSuccess),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la génération directe du certificat: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération du certificat: ' . $e->getMessage(),
            ], 500);
        }
    }
}
