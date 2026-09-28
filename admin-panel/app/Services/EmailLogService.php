<?php

namespace App\Services;

use App\Models\EmailLog;
use App\Models\Student;
use Illuminate\Http\Request;

class EmailLogService
{
    public function index(Request $request)
    {
        $query = EmailLog::query()->latest();

        $filteredStudent = null;

        if ($studentId = $request->get('student_id')) {
            $filteredStudent = Student::find($studentId);

            if ($filteredStudent) {
                $query->where(function ($q) use ($filteredStudent) {
                    $q->where('student_id', $filteredStudent->id)
                        ->orWhere('student_email', $filteredStudent->email);
                });
            }
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('student_email', 'like', "%{$search}%")
                    ->orWhere('student_name', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%");
            });
        }

        if ($type = $request->get('type')) {
            $query->where('email_type', $type);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($readStatus = $request->get('read_status')) {
            match ($readStatus) {
                'unread' => $query->where('email_type', 'custom_email')
                    ->where('status', 'sent')
                    ->whereNotNull('tracking_token')
                    ->whereNull('read_at'),
                'read' => $query->where('email_type', 'custom_email')
                    ->whereNotNull('read_at'),
                default => null,
            };
        }

        if ($dateFrom = $request->get('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->get('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $logs = $query->paginate(25)->appends($request->query());

        $emailTypes = EmailLog::select('email_type')
            ->distinct()
            ->orderBy('email_type')
            ->pluck('email_type');

        $stats = [
            'total' => EmailLog::count(),
            'sent' => EmailLog::where('status', 'sent')->count(),
            'failed' => EmailLog::where('status', 'failed')->count(),
            'today' => EmailLog::whereDate('created_at', today())->count(),
        ];

        return view('email-logs.index', compact('logs', 'emailTypes', 'stats', 'filteredStudent'));
    }
}
