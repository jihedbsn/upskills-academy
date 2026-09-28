<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Services\StudentDashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StudentDashboardController extends Controller
{

    protected StudentDashboardService $studentDashboardService;

    public function __construct(StudentDashboardService $studentDashboardService)
    {
        $this->studentDashboardService = $studentDashboardService;
    }

    public function index()
    {
        return $this->studentDashboardService->index();
    }

    public function courseSchedule()
    {
        return $this->studentDashboardService->courseSchedule();
    }

    public function calendar()
    {
        return $this->studentDashboardService->calendar();
    }

    public function coursesIndex()
    {
        return $this->studentDashboardService->coursesIndex();
    }

    public function historyIndex(Request $request)
    {
        return $this->studentDashboardService->historyIndex($request);
    }

    public function courseShow($id)
    {
        return $this->studentDashboardService->courseShow($id);
    }

    public function zoomMeetingsIndex()
    {
        return $this->studentDashboardService->zoomMeetingsIndex();
    }

    public function booksIndex()
    {
        return $this->studentDashboardService->booksIndex();
    }

    public function bookShow($id)
    {
        return $this->studentDashboardService->bookShow($id);
    }

    public function certificatesIndex(Request $request)
    {
        return $this->studentDashboardService->certificatesIndex($request);
    }

    public function certificateShow($id)
    {
        return $this->studentDashboardService->certificateShow($id);
    }

    public function oldCertificate(Request $request)
    {
        $student = Auth::user();

        if (! $student) {
            $student = Student::find(1);
            if (! $student) {
                return response()->json([
                    'error' => 'User not authenticated and no test student found',
                ], 401);
            }
            Log::info("Using test student for old certificates: {$student->full_name} (ID: {$student->id})");
        }

        $old_certif = [];
        if (! empty($student->email)) {
            $old_certif_url = 'https://files.upskillsacademy.com/certificates/api/certificates/search/email?email=' . rawurlencode($student->email);
            $old_certif_ch = curl_init($old_certif_url);
            curl_setopt($old_certif_ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($old_certif_ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($old_certif_ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($old_certif_ch, CURLOPT_TIMEOUT, 20);
            curl_setopt($old_certif_ch, CURLOPT_SSL_VERIFYPEER, true);
            $old_certif_raw = curl_exec($old_certif_ch);
            $old_certif_http = (int) curl_getinfo($old_certif_ch, CURLINFO_HTTP_CODE);
            curl_close($old_certif_ch);

            if ($old_certif_raw !== false && $old_certif_http >= 200 && $old_certif_http < 300) {
                $old_certif_decoded = json_decode($old_certif_raw, true);
                $old_certif = is_array($old_certif_decoded) ? $old_certif_decoded : [];
            }
        }

        $allRows = collect($old_certif['certificates'] ?? [])->values();
        $approved = $allRows->filter(fn ($c) => ($c['active'] ?? '1') === '1');
        $rejected = $allRows->filter(fn ($c) => ($c['active'] ?? '1') === '0');

        $certificateStats = [
            'total_certificates' => $allRows->count(),
            'pending_certificates' => $allRows->count() - $approved->count() - $rejected->count(),
            'rejected_certificates' => $rejected->count(),
            'this_month' => $approved->filter(function ($c) {
                $d = $c['date'] ?? $c['created_at'] ?? null;
                if (! $d) {
                    return false;
                }
                try {
                    return \Carbon\Carbon::parse($d)->gte(now()->startOfMonth());
                } catch (\Throwable $e) {
                    return false;
                }
            })->count(),
            'this_year' => $approved->filter(function ($c) {
                $d = $c['date'] ?? $c['created_at'] ?? null;
                if (! $d) {
                    return false;
                }
                try {
                    return \Carbon\Carbon::parse($d)->gte(now()->startOfYear());
                } catch (\Throwable $e) {
                    return false;
                }
            })->count(),
            'total_courses_completed' => $approved->count(),
        ];

        $page = max(1, (int) $request->get('page', 1));
        $perPage = 12;
        $total = $allRows->count();
        $slice = $allRows->slice(($page - 1) * $perPage, $perPage)->values();

        $certificates = new \Illuminate\Pagination\LengthAwarePaginator(
            $slice,
            $total,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        $enrolledProducts = $student->productAccess()
            ->with(['product'])
            ->get()
            ->pluck('product');

        return view('student-dashboard.pages.oldcertficates', compact('certificates', 'certificateStats', 'enrolledProducts'));
    }

    public function payment()
    {
        return $this->studentDashboardService->payment();
    }

    public function paymentHistory()
    {
        return $this->studentDashboardService->paymentHistory();
    }

    public function orders()
    {
        return $this->studentDashboardService->orders();
    }

    public function specialOffers()
    {
        return $this->studentDashboardService->specialOffers();
    }

    public function support()
    {
        return $this->studentDashboardService->support();
    }

    public function tickets()
    {
        return $this->studentDashboardService->tickets();
    }

    public function createTicket()
    {
        return $this->studentDashboardService->createTicket();
    }

    public function storeTicket(Request $request)
    {
        return $this->studentDashboardService->storeTicket($request);
    }

    public function storeTicketReply(Request $request, $locale, $ticketId)
    {
        return $this->studentDashboardService->storeTicketReply($request, $locale, $ticketId);
    }

    public function getTicketResponses($locale, $ticketId)
    {
        return $this->studentDashboardService->getTicketResponses($locale, $ticketId);
    }

    public function closeTicket(Request $request, $locale, $ticketId)
    {
        return $this->studentDashboardService->closeTicket($request, $locale, $ticketId);
    }

    public function profile()
    {
        return $this->studentDashboardService->profile();
    }

    public function updatePhoto(Request $request)
    {
        return $this->studentDashboardService->updatePhoto($request);
    }

    public function getTransactionDetails($transactionId)
    {
        return $this->studentDashboardService->getTransactionDetails($transactionId);
    }

    public function downloadReceipt($transactionId)
    {
        return $this->studentDashboardService->downloadReceipt($transactionId);
    }

    public function updateProfile(Request $request)
    {
        return $this->studentDashboardService->updateProfile($request);
    }

    public function settingsLanguage()
    {
        return $this->studentDashboardService->settingsLanguage();
    }

    public function deleteAccount(Request $request)
    {
        return $this->studentDashboardService->deleteAccount($request);
    }
}
