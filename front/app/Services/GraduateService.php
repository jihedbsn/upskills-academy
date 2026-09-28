<?php

namespace App\Services;

use App\Models\CertifStudent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GraduateService
{
    private function getNewSiteGraduatesData($year = null)
    {
        try {
            $query = CertifStudent::with(['student', 'product', 'product.teacher', 'certif'])
                ->where('is_valid', true);

            if ($year) {
                $query->whereYear('certificate_date', $year);
            } else {
                $query->whereYear('certificate_date', '>=', 2026);
            }

            $certificates = $query->get();

            if ($year) {
                return $certificates;
            }

            $yearlyData = $certificates->groupBy(function ($cert) {
                return $cert->certificate_date ? (is_string($cert->certificate_date) ? Carbon::parse($cert->certificate_date)->year : $cert->certificate_date->year) : null;
            })->map(function ($certs, $groupYear) {
                return [
                    'year' => $groupYear,
                    'certificates' => $certs->count(),
                    'students' => $certs->pluck('student_id')->unique()->count(),
                    'courses' => $certs->pluck('product_id')->unique()->count(),
                ];
            })->values();

            return $yearlyData;
        } catch (\Exception $e) {
            Log::error('Failed to fetch from new site database: ' . $e->getMessage());
            return collect([]);
        }
    }

    private function getGraduatesData()
    {
        if (Storage::disk('local')->exists('graduates/complete_data.json')) {
            $jsonData = Storage::disk('local')->get('graduates/complete_data.json');
            return json_decode($jsonData, true);
        }

        try {
            $response = Http::timeout(15)->get('https://old-api.upskillsacademy.com/api/graduates');
            if ($response->successful()) {
                return ['summary' => $response->json(), 'years' => [], 'certificates' => []];
            }
        } catch (\Exception $e) {
            Log::error('Failed to fetch from API: ' . $e->getMessage());
        }

        return null;
    }

    public function apiData()
    {
        $data = $this->getGraduatesData();

        if ($data) {
            return response()->json($data);
        }

        return response()->json([
            'error' => 'No data available',
            'message' => 'Please run: php artisan graduates:scrape',
        ], 404);
    }

    public function index($locale)
    {
        try {
            Log::info('GraduateController: Starting to fetch graduates data');

            $data = $this->getGraduatesData();

            if ($data && isset($data['summary']['yearlyData'])) {
                $graduatesByYear = collect($data['summary']['yearlyData'])
                    ->map(function ($item) {
                        return (object) [
                            'year' => $item['year'],
                            'total_courses' => $item['courses'],
                            'total_students' => $item['students'],
                            'total_certificates' => $item['certificates'],
                        ];
                    });

                $newSiteData = $this->getNewSiteGraduatesData();

                foreach ($newSiteData as $newYearData) {
                    $year = $newYearData['year'];
                    if ($year && $year >= 2026) {
                        $existingIndex = $graduatesByYear->search(function ($item) use ($year) {
                            return $item->year == $year;
                        });

                        if ($existingIndex !== false) {
                            $existing = $graduatesByYear[$existingIndex];
                            $existing->total_courses += $newYearData['courses'];
                            $existing->total_students += $newYearData['students'];
                            $existing->total_certificates += $newYearData['certificates'];
                        } else {
                            $graduatesByYear->push((object) [
                                'year' => $year,
                                'total_courses' => $newYearData['courses'],
                                'total_students' => $newYearData['students'],
                                'total_certificates' => $newYearData['certificates'],
                            ]);
                        }
                    }
                }

                $graduatesByYear = $graduatesByYear->sortByDesc('year')->values();

                Log::info('Graduates By Year Count: ' . $graduatesByYear->count());

                return view('graduates.index', compact('graduatesByYear'));
            }

            Log::info('Calling graduates API: https://old-api.upskillsacademy.com/api/graduates');

            $response = Http::timeout(15)->get('https://old-api.upskillsacademy.com/api/graduates');

            Log::info('API Response Status: ' . $response->status());

            if ($response->successful()) {
                $graduatesData = $response->json();
                Log::info('API Data decoded successfully', [
                    'has_yearlyData' => isset($graduatesData['yearlyData']),
                    'count' => count($graduatesData['yearlyData'] ?? []),
                ]);

                $graduatesByYear = collect($graduatesData['yearlyData'] ?? [])
                    ->map(function ($item) {
                        return (object) [
                            'year' => $item['year'],
                            'total_courses' => $item['courses'],
                            'total_students' => $item['students'],
                            'total_certificates' => $item['certificates'],
                        ];
                    });

                $newSiteData = $this->getNewSiteGraduatesData();

                foreach ($newSiteData as $newYearData) {
                    $year = $newYearData['year'];
                    if ($year && $year >= 2026) {
                        $existingIndex = $graduatesByYear->search(function ($item) use ($year) {
                            return $item->year == $year;
                        });

                        if ($existingIndex !== false) {
                            $existing = $graduatesByYear[$existingIndex];
                            $existing->total_courses += $newYearData['courses'];
                            $existing->total_students += $newYearData['students'];
                            $existing->total_certificates += $newYearData['certificates'];
                        } else {
                            $graduatesByYear->push((object) [
                                'year' => $year,
                                'total_courses' => $newYearData['courses'],
                                'total_students' => $newYearData['students'],
                                'total_certificates' => $newYearData['certificates'],
                            ]);
                        }
                    }
                }

                $graduatesByYear = $graduatesByYear->sortByDesc('year')->values();

                Log::info('Graduates By Year Count: ' . $graduatesByYear->count());

                return view('graduates.index', compact('graduatesByYear'));
            }

            Log::error('API call failed with status: ' . $response->status());
            Log::error('API Response Body: ' . $response->body());

            $newSiteData = $this->getNewSiteGraduatesData();
            $graduatesByYear = $newSiteData->map(function ($item) {
                return (object) [
                    'year' => $item['year'],
                    'total_courses' => $item['courses'],
                    'total_students' => $item['students'],
                    'total_certificates' => $item['certificates'],
                ];
            })->sortByDesc('year')->values();

            return view('graduates.index', compact('graduatesByYear'));
        } catch (\Exception $e) {
            Log::error('Exception in GraduateController: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            $graduatesByYear = collect([]);
            return view('graduates.index', compact('graduatesByYear'));
        }
    }

    public function year($locale, $year)
    {
        try {
            $data = $this->getGraduatesData();

            $certificates = collect([]);

            if ($data && isset($data['years'][$year])) {
                $yearData = $data['years'][$year];
                $certificates = collect($yearData['certificates'] ?? [])
                    ->map(function ($cert) {
                        return (object) [
                            'id' => $cert['id'],
                            'student_name' => $cert['student']['user']['full_name_en'] ?? $cert['student']['user']['full_name_ar'] ?? 'N/A',
                            'course_name' => $cert['course_name'],
                            'exam_name' => $cert['exam_name'],
                            'teacher_name' => $cert['teacher_name'],
                            'serialnumber' => $cert['serialnumber'],
                            'image' => $cert['image'],
                            'date' => $cert['date'],
                            'nationality' => $cert['student']['user']['nationality'] ?? 'N/A',
                            'email' => $cert['student']['user']['email'] ?? 'N/A',
                            'source' => 'old',
                        ];
                    });
            } else {
                $response = Http::timeout(15)->get("https://old-api.upskillsacademy.com/api/graduates/{$year}");

                if ($response->successful()) {
                    $data = $response->json();
                    $certificates = collect($data['certificates'] ?? [])
                        ->map(function ($cert) {
                            return (object) [
                                'id' => $cert['id'],
                                'student_name' => $cert['student']['user']['full_name_en'] ?? $cert['student']['user']['full_name_ar'] ?? 'N/A',
                                'course_name' => $cert['course_name'],
                                'exam_name' => $cert['exam_name'],
                                'teacher_name' => $cert['teacher_name'],
                                'serialnumber' => $cert['serialnumber'],
                                'image' => $cert['image'],
                                'date' => $cert['date'],
                                'nationality' => $cert['student']['user']['nationality'] ?? 'N/A',
                                'email' => $cert['student']['user']['email'] ?? 'N/A',
                                'source' => 'old',
                            ];
                        });
                }
            }

            if ($year >= 2026) {
                $newCertificates = $this->getNewSiteGraduatesData($year);

                $newCerts = $newCertificates->map(function ($cert) {
                    $student = $cert->student;
                    $product = $cert->product;
                    $teacher = $product?->teacher;

                    $certDate = null;
                    if ($cert->certificate_date) {
                        $certDate = is_string($cert->certificate_date)
                            ? $cert->certificate_date
                            : $cert->certificate_date->format('Y-m-d');
                    }

                    $teacherName = $teacher?->full_name ?? 'N/A';

                    return (object) [
                        'id' => $cert->id,
                        'student_name' => $student->full_name ?? $student->name ?? 'N/A',
                        'course_name' => $product->titre ?? 'N/A',
                        'exam_name' => $product->titre ?? 'N/A',
                        'teacher_name' => $teacherName,
                        'serialnumber' => $cert->serial_number,
                        'image' => $cert->file_path ? env('ADMIN_URL', 'http://localhost:8001') . '/' . $cert->file_path : null,
                        'date' => $certDate,
                        'nationality' => $student->nationality ?? 'N/A',
                        'email' => $student->email ?? 'N/A',
                        'source' => 'new',
                    ];
                });

                $certificates = $certificates->merge($newCerts);
            }

            $certificatesByCourse = $certificates->groupBy('course_name')->map(function ($courseCerts, $courseName) {
                return (object) [
                    'course_name' => $courseName,
                    'certificates' => $courseCerts,
                    'total_students' => $courseCerts->count(),
                ];
            });

            $stats = (object) [
                'year' => $year,
                'total_certificates' => $certificates->count(),
                'total_courses' => $certificates->pluck('course_name')->unique()->count(),
                'total_students' => $certificates->pluck('student_name')->unique()->count(),
            ];

            return view('graduates.year', compact('certificatesByCourse', 'year', 'stats'));
        } catch (\Exception $e) {
            Log::error('Exception in GraduateController::year: ' . $e->getMessage());

            $certificatesByCourse = collect([]);
            $stats = (object) [
                'year' => $year,
                'total_certificates' => 0,
                'total_courses' => 0,
                'total_students' => 0,
            ];

            return view('graduates.year', compact('certificatesByCourse', 'year', 'stats'));
        }
    }

    public function certificate($locale, $serialnumber)
    {
        try {
            $newCertificate = CertifStudent::with(['student', 'product', 'product.teacher', 'certif'])
                ->where('serial_number', $serialnumber)
                ->where('is_valid', true)
                ->first();

            $certYear = null;
            if ($newCertificate && $newCertificate->certificate_date) {
                $certYear = is_string($newCertificate->certificate_date)
                    ? (int) substr($newCertificate->certificate_date, 0, 4)
                    : $newCertificate->certificate_date->year;
            }

            if ($newCertificate && $certYear && $certYear >= 2026) {
                $student = $newCertificate->student;
                $product = $newCertificate->product;
                $teacher = $product?->teacher;

                $certDate = null;
                if ($newCertificate->certificate_date) {
                    $certDate = is_string($newCertificate->certificate_date)
                        ? $newCertificate->certificate_date
                        : $newCertificate->certificate_date->format('Y-m-d');
                }

                $teacherName = $teacher?->full_name ?? 'N/A';

                $certificate = (object) [
                    'id' => $newCertificate->id,
                    'student_name' => $student->full_name ?? $student->name ?? 'N/A',
                    'course_name' => $product->titre ?? 'N/A',
                    'exam_name' => $product->titre ?? 'N/A',
                    'teacher_name' => $teacherName,
                    'serialnumber' => $newCertificate->serial_number,
                    'image' => $newCertificate->file_path ? env('ADMIN_URL', 'http://localhost:8001') . '/' . $newCertificate->file_path : null,
                    'date' => $certDate,
                    'nationality' => $student->nationality ?? 'N/A',
                    'email' => $student->email ?? 'N/A',
                    'mobile' => $student->mobile ?? 'N/A',
                    'gender' => $student->gender ?? 'N/A',
                    'date_of_birth' => $student->date_of_birth ?? 'N/A',
                    'course_period' => $product->period ?? 'N/A',
                    'course_language' => $product->language ?? 'N/A',
                    'source' => 'new',
                ];

                return view('graduates.certificate', compact('certificate'));
            }

            $data = $this->getGraduatesData();

            if ($data && isset($data['certificates'][$serialnumber])) {
                $certData = $data['certificates'][$serialnumber];

                $certificate = (object) [
                    'id' => $certData['id'],
                    'student_name' => $certData['student']['user']['full_name_en'] ?? $certData['student']['user']['full_name_ar'] ?? 'N/A',
                    'course_name' => $certData['course_name'],
                    'exam_name' => $certData['exam_name'],
                    'teacher_name' => $certData['teacher_name'],
                    'serialnumber' => $certData['serialnumber'],
                    'image' => $certData['image'],
                    'date' => $certData['date'],
                    'nationality' => $certData['student']['user']['nationality'] ?? 'N/A',
                    'email' => $certData['student']['user']['email'] ?? 'N/A',
                    'mobile' => $certData['student']['user']['mobile'] ?? 'N/A',
                    'gender' => $certData['student']['user']['gender'] ?? 'N/A',
                    'date_of_birth' => $certData['student']['user']['date_of_birth'] ?? 'N/A',
                    'course_period' => $certData['course']['period'] ?? 'N/A',
                    'course_language' => $certData['course']['language'] ?? 'N/A',
                    'source' => 'old',
                ];

                return view('graduates.certificate', compact('certificate'));
            }

            $response = Http::timeout(15)->get("https://old-api.upskillsacademy.com/api/graduates/id/{$serialnumber}");

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['certificates']) && count($data['certificates']) > 0) {
                    $certData = $data['certificates'][0];

                    $certificate = (object) [
                        'id' => $certData['id'],
                        'student_name' => $certData['student']['user']['full_name_en'] ?? $certData['student']['user']['full_name_ar'] ?? 'N/A',
                        'course_name' => $certData['course_name'],
                        'exam_name' => $certData['exam_name'],
                        'teacher_name' => $certData['teacher_name'],
                        'serialnumber' => $certData['serialnumber'],
                        'image' => $certData['image'],
                        'date' => $certData['date'],
                        'nationality' => $certData['student']['user']['nationality'] ?? 'N/A',
                        'email' => $certData['student']['user']['email'] ?? 'N/A',
                        'mobile' => $certData['student']['user']['mobile'] ?? 'N/A',
                        'gender' => $certData['student']['user']['gender'] ?? 'N/A',
                        'date_of_birth' => $certData['student']['user']['date_of_birth'] ?? 'N/A',
                        'course_period' => $certData['course']['period'] ?? 'N/A',
                        'course_language' => $certData['course']['language'] ?? 'N/A',
                        'source' => 'old',
                    ];

                    return view('graduates.certificate', compact('certificate'));
                }

                return redirect()->route('graduates.index', ['locale' => $locale])
                    ->with('error', __('messages.certificate_not_found'));
            }

            return redirect()->route('graduates.index', ['locale' => $locale])
                ->with('error', __('messages.certificate_not_found'));
        } catch (\Exception $e) {
            Log::error('Exception in GraduateController::certificate: ' . $e->getMessage());

            return redirect()->route('graduates.index', ['locale' => $locale])
                ->with('error', __('messages.certificate_not_found'));
        }
    }
}
