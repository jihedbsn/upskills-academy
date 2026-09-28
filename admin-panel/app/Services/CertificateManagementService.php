<?php

namespace App\Services;

use App\Mail\CertificateGeneratedNotification;
use App\Models\CertifStudent;
use App\Models\EmailLog;
use App\Models\Order;
use App\Models\Product;
use App\Models\Student;
use App\Models\StudentSuccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CertificateManagementService
{
    public function index(Request $request)
    {
        $query = StudentSuccess::with(['student', 'product.certif', 'certificates'])
            ->where('success', 1)
            ->whereHas('student')
            ->whereHas('product', function ($queryBuilder) {
                $queryBuilder->whereNotNull('certif_id');
            });

        if ($request->filled('course')) {
            $query->where('product_id', $request->course);
        }

        if ($request->filled('student')) {
            $query->whereHas('student', function ($queryBuilder) use ($request) {
                $queryBuilder->where('first_name', 'like', "%{$request->student}%")
                    ->orWhere('last_name', 'like', "%{$request->student}%")
                    ->orWhere('email', 'like', "%{$request->student}%");
            });
        }

        if ($request->filled('serial_number')) {
            $query->whereHas('certificates', function ($queryBuilder) use ($request) {
                $queryBuilder->where('serial_number', 'like', "%{$request->serial_number}%");
            });
        }

        $certificates = $query->orderBy('created_at', 'desc')->paginate(20);

        $courses = Product::whereNotNull('certif_id')
            ->with(['certif', 'variations'])
            ->orderBy('id')
            ->get();

        $stats = [
            'total' => StudentSuccess::where('success', 1)
                ->whereHas('product', function ($queryBuilder) {
                    $queryBuilder->whereNotNull('certif_id');
                })->count(),
            'with_certificate' => StudentSuccess::where('success', 1)
                ->whereHas('product', function ($queryBuilder) {
                    $queryBuilder->whereNotNull('certif_id');
                })
                ->whereHas('certificates')
                ->count(),
            'pending' => StudentSuccess::where('success', 1)
                ->whereHas('product', function ($queryBuilder) {
                    $queryBuilder->whereNotNull('certif_id');
                })
                ->whereDoesntHave('certificates')
                ->count(),
        ];

        return view('certificate-management.index', compact('certificates', 'courses', 'stats'));
    }

    public function show(StudentSuccess $studentSuccess)
    {
        if (!$studentSuccess->product->certif_id) {
            return back()->with('error', 'No certificate template associated with this course.');
        }

        $certificate = $studentSuccess->certificates()->first();

        return view('certificate-management.show', compact('studentSuccess', 'certificate'));
    }

    public function download(StudentSuccess $studentSuccess)
    {
        try {
            $certificate = $studentSuccess->certificates()->first();

            if (!$certificate) {
                return back()->with('error', 'No certificate found for this student success.');
            }

            $filePath = public_path($certificate->file_path);

            if (!file_exists($filePath)) {
                return back()->with('error', 'Certificate file not found.');
            }

            return response()->download($filePath, 'certificate_' . $certificate->serial_number . '.png');
        } catch (\Exception $e) {
            Log::error('Error downloading certificate: ' . $e->getMessage());
            return back()->with('error', 'Error downloading certificate: ' . $e->getMessage());
        }
    }

    public function generate(Request $request, StudentSuccess $studentSuccess)
    {
        try {
            if (!$studentSuccess->product->certif_id) {
                return back()->with('error', 'No certificate template associated with this course.');
            }

            if ($studentSuccess->success != 1) {
                return back()->with('error', 'Student success must be approved before generating certificate.');
            }

            $certificateService = new CertificateGeneratorService();

            if ($certificateService->certificateExists($studentSuccess)) {
                return back()->with('warning', 'A certificate already exists for this student success.');
            }

            $customDate = $request->input('certificate_date');
            $certificate = $certificateService->generateCertificate($studentSuccess, $customDate);

            try {
                Mail::to($studentSuccess->student->email)
                    ->send(new CertificateGeneratedNotification($certificate, $studentSuccess));

                EmailLog::logSent(
                    $studentSuccess->student->email,
                    'certificate_generated',
                    'Certificate Generated: ' . ($studentSuccess->product->titre ?? 'N/A'),
                    $studentSuccess->student->id,
                    ($studentSuccess->student->first_name ?? '') . ' ' . ($studentSuccess->student->last_name ?? ''),
                    'Certificate',
                    $certificate->id
                );

                Log::info('Certificate generated and email sent to student', [
                    'certificate_id' => $certificate->id,
                    'student_email' => $studentSuccess->student->email
                ]);

                return back()->with('success', 'Certificate generated successfully and sent to student! Serial number: ' . $certificate->serial_number);
            } catch (\Exception $emailError) {
                Log::error('Certificate generated but failed to send email', [
                    'error' => $emailError->getMessage(),
                    'certificate_id' => $certificate->id
                ]);

                EmailLog::logFailed(
                    $studentSuccess->student->email ?? 'unknown',
                    'certificate_generated',
                    'Certificate Generated: ' . ($studentSuccess->product->titre ?? 'N/A'),
                    $emailError->getMessage(),
                    $studentSuccess->student->id ?? null,
                    ($studentSuccess->student->first_name ?? '') . ' ' . ($studentSuccess->student->last_name ?? ''),
                    'Certificate',
                    $certificate->id
                );

                return back()->with('warning', 'Certificate generated successfully (Serial: ' . $certificate->serial_number . ') but failed to send email to student.');
            }
        } catch (\Exception $e) {
            Log::error('Error generating certificate: ' . $e->getMessage());
            return back()->with('error', 'Error generating certificate: ' . $e->getMessage());
        }
    }

    public function bulkGenerate(Request $request)
    {
        try {
            $studentSuccessIds = $request->input('student_success_ids', []);

            if (empty($studentSuccessIds)) {
                return back()->with('error', 'No student successes selected.');
            }

            $certificateService = new CertificateGeneratorService();
            $generated = 0;
            $errors = [];

            foreach ($studentSuccessIds as $id) {
                try {
                    $studentSuccess = StudentSuccess::findOrFail($id);

                    if (!$studentSuccess->product->certif_id) {
                        $errors[] = "No certificate template for student: {$studentSuccess->student->full_name}";
                        continue;
                    }

                    if ($studentSuccess->success != 1) {
                        $errors[] = "Student success not approved: {$studentSuccess->student->full_name}";
                        continue;
                    }

                    if ($certificateService->certificateExists($studentSuccess)) {
                        $errors[] = "Certificate already exists: {$studentSuccess->student->full_name}";
                        continue;
                    }

                    $certificateService->generateCertificate($studentSuccess);
                    $generated++;
                } catch (\Exception $e) {
                    $errors[] = "Error for student ID {$id}: " . $e->getMessage();
                }
            }

            $message = "Generated {$generated} certificates successfully.";
            if (!empty($errors)) {
                $message .= ' Errors: ' . implode(', ', $errors);
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Error in bulk certificate generation: ' . $e->getMessage());
            return back()->with('error', 'Error generating certificates: ' . $e->getMessage());
        }
    }

    public function updateDate(Request $request, CertifStudent $certificate)
    {
        try {
            $request->validate([
                'certificate_date' => 'required|date',
            ]);

            $certificate->update([
                'certificate_date' => $request->certificate_date,
            ]);

            return back()->with('success', 'Certificate date updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating certificate date: ' . $e->getMessage());
            return back()->with('error', 'Error updating certificate date: ' . $e->getMessage());
        }
    }

    public function regenerate(Request $request, CertifStudent $certificate)
    {
        try {
            $request->validate([
                'certificate_date' => 'required|date',
            ]);

            $certificateService = new CertificateGeneratorService();

            if ($certificate->file_path) {
                $oldFilePath = public_path($certificate->file_path);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }

            $certificate->update([
                'certificate_date' => $request->certificate_date,
            ]);

            $certif = $certificate->certif;
            $student = $certificate->student;
            $product = $certificate->product;

            $fileName = 'certificate_' . $certificate->id . '_' . time() . '.png';

            $filePath = $certificateService->regenerateCertificateWithDate(
                $certificate,
                $certif,
                $student,
                $product,
                $certificate->serial_number,
                $request->certificate_date,
                $fileName
            );

            $certificate->update([
                'file_path' => $filePath,
                'generated_at' => now(),
            ]);

            return back()->with('success', 'Certificate regenerated successfully with new date!');
        } catch (\Exception $e) {
            Log::error('Error regenerating certificate: ' . $e->getMessage());
            return back()->with('error', 'Error regenerating certificate: ' . $e->getMessage());
        }
    }

    public function delete($studentSuccessId)
    {
        try {
            $studentSuccess = StudentSuccess::findOrFail($studentSuccessId);

            foreach ($studentSuccess->certificates as $certificate) {
                $this->removeCertificateFile($certificate);
                $certificate->delete();
            }

            $studentSuccess->delete();

            return redirect()->route('certificate-management.index')->with('success', 'Certificate request deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Error deleting certificate request: ' . $e->getMessage());
            return redirect()->route('certificate-management.index')->with('error', 'Failed to delete certificate request: ' . $e->getMessage());
        }
    }

    public function deleteGeneratedCertificate(CertifStudent $certificate)
    {
        try {
            $studentSuccessId = $certificate->student_success_id;
            $this->removeCertificateFile($certificate);
            $certificate->delete();

            if ($studentSuccessId) {
                return redirect()
                    ->route('certificate-management.show', $studentSuccessId)
                    ->with('success', 'Certificate deleted successfully. You can generate a new one.');
            }

            return redirect()
                ->route('certificate-management.index')
                ->with('success', 'Certificate deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting generated certificate: ' . $e->getMessage());

            return back()->with('error', 'Failed to delete certificate: ' . $e->getMessage());
        }
    }

    private function removeCertificateFile(CertifStudent $certificate): void
    {
        if (!$certificate->file_path) {
            return;
        }

        $paths = [
            public_path($certificate->file_path),
            public_path('upload/certif-student/' . ltrim($certificate->file_path, '/')),
        ];

        foreach (array_unique($paths) as $filePath) {
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }
    }

    public function viewPublic($serialNumber)
    {
        try {
            $certificate = CertifStudent::where('serial_number', $serialNumber)
                ->with(['student', 'product', 'studentSuccess'])
                ->first();

            if (!$certificate) {
                return response()->view('errors.404', [], 404);
            }

            if (!$certificate->is_valid) {
                return response()->view('errors.404', [], 404);
            }

            return view('certificate-management.public', compact('certificate'));
        } catch (\Exception $e) {
            Log::error('Error viewing public certificate: ' . $e->getMessage());
            return response()->view('errors.500', [], 500);
        }
    }

    public function create()
    {
        $courses = Product::whereNotNull('certif_id')
            ->with('certif')
            ->orderBy('id')
            ->get();

        return view('certificate-management.create', compact('courses'));
    }

    public function getStudentsByCourse($courseId)
    {
        try {
            $students = Student::whereHas('orders', function ($queryBuilder) use ($courseId) {
                $queryBuilder->where('product_id', $courseId)
                    ->where('payment_success', 1);
            })
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get()
                ->map(function ($student) use ($courseId) {
                    $hasCertificate = CertifStudent::where('student_id', $student->id)
                        ->where('product_id', $courseId)
                        ->exists();

                    return [
                        'id' => $student->id,
                        'name' => $student->first_name . ' ' . $student->last_name,
                        'email' => $student->email,
                        'has_certificate' => $hasCertificate
                    ];
                });

            return response()->json([
                'success' => true,
                'students' => $students
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting students: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading students: ' . $e->getMessage()
            ], 500);
        }
    }

    public function manualGenerate(Request $request)
    {
        try {
            $request->validate([
                'course_id' => 'required|exists:products,id',
                'student_id' => 'required|exists:students,id',
                'certificate_date' => 'nullable|date',
            ]);

            $product = Product::findOrFail($request->course_id);
            $student = Student::findOrFail($request->student_id);

            if (!$product->certif_id) {
                return back()->with('error', 'This course does not have a certificate template assigned.');
            }

            $isEnrolled = Order::where('student_id', $student->id)
                ->where('product_id', $product->id)
                ->where('payment_success', 1)
                ->exists();

            if (!$isEnrolled) {
                return back()->with('error', 'This student is not enrolled in the selected course.');
            }

            $existingCertificate = CertifStudent::where('student_id', $student->id)
                ->where('product_id', $product->id)
                ->first();

            if ($existingCertificate) {
                return back()->with('warning', 'A certificate already exists for this student and course. Serial Number: ' . $existingCertificate->serial_number);
            }

            $studentSuccess = StudentSuccess::firstOrCreate(
                [
                    'student_id' => $student->id,
                    'product_id' => $product->id,
                ],
                [
                    'success' => 1,
                    'lien_video' => null,
                    'admin_notes' => 'Certificate generated manually by admin',
                    'submitted_at' => now(),
                    'validated_at' => now(),
                ]
            );

            if ($studentSuccess->success != 1) {
                $studentSuccess->update([
                    'success' => 1,
                    'validated_at' => now(),
                    'admin_notes' => ($studentSuccess->admin_notes ?? '') . "\nCertificate generated manually by admin on " . now()->format('Y-m-d H:i:s'),
                ]);
            }

            $certificateService = new CertificateGeneratorService();
            $customDate = $request->input('certificate_date');
            $certificate = $certificateService->generateCertificate($studentSuccess, $customDate);

            try {
                Mail::to($student->email)
                    ->send(new CertificateGeneratedNotification($certificate, $studentSuccess));

                EmailLog::logSent(
                    $student->email,
                    'certificate_generated',
                    'Certificate Generated: ' . ($product->titre ?? 'N/A'),
                    $student->id,
                    ($student->first_name ?? '') . ' ' . ($student->last_name ?? ''),
                    'Certificate',
                    $certificate->id
                );

                Log::info('Certificate manually generated and email sent', [
                    'certificate_id' => $certificate->id,
                    'student_id' => $student->id,
                    'course_id' => $product->id,
                    'generated_by' => Auth::user()->name ?? 'admin'
                ]);

                return redirect()->route('certificate-management.index')
                    ->with('success', 'Certificate generated successfully and sent to ' . $student->email . '! Serial Number: ' . $certificate->serial_number);
            } catch (\Exception $emailError) {
                Log::error('Certificate generated but failed to send email', [
                    'error' => $emailError->getMessage(),
                    'certificate_id' => $certificate->id
                ]);

                return redirect()->route('certificate-management.index')
                    ->with('warning', 'Certificate generated successfully (Serial: ' . $certificate->serial_number . ') but failed to send email to student.');
            }
        } catch (\Exception $e) {
            Log::error('Error manually generating certificate: ' . $e->getMessage());
            return back()->with('error', 'Error generating certificate: ' . $e->getMessage());
        }
    }
}
