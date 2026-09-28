<?php

namespace App\Services;

use App\Models\CourseExtensionOrder;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CourseExtensionService
{
    public function index(Request $request)
    {
        $query = CourseExtensionOrder::with(['student', 'product'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($sq) use ($search) {
                    $sq->where('nom', 'like', "%{$search}%")
                        ->orWhere('prenom', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('product', function ($pq) use ($search) {
                    $pq->whereHas('variations', function ($vq) use ($search) {
                        $vq->where('name', 'like', "%{$search}%");
                    });
                });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $extensionOrders = $query->paginate(20);

        $stats = [
            'total' => CourseExtensionOrder::count(),
            'pending' => CourseExtensionOrder::where('payment_status', 'pending')->count(),
            'approved' => CourseExtensionOrder::where('payment_status', 'approved')->count(),
            'rejected' => CourseExtensionOrder::where('payment_status', 'rejected')->count(),
            'revenue' => CourseExtensionOrder::where('payment_status', 'approved')->sum('price'),
        ];

        return view('course-extensions.index', compact('extensionOrders', 'stats'));
    }

    public function approve(CourseExtensionOrder $extensionOrder)
    {
        if ($extensionOrder->payment_status !== 'pending') {
            return redirect()->back()->with('error', 'This order has already been processed.');
        }

        DB::beginTransaction();
        try {
            $extensionOrder->update([
                'payment_status' => 'approved',
                'payment_success' => true,
            ]);

            $extensionOrder->applyExtension();

            $product = $extensionOrder->product;
            Notification::create([
                'notifiable_type' => 'App\\Models\\Student',
                'notifiable_id' => $extensionOrder->student_id,
                'type' => 'course_extension',
                'title' => 'Course Extension Approved',
                'message' => 'Your extension request for "' . ($product->titre ?? 'Course') . '" has been approved. Your access has been extended by ' . $extensionOrder->extension_months . ' months.',
                'data' => json_encode(['extension_order_id' => $extensionOrder->id]),
                'is_read' => false,
            ]);

            $student = $extensionOrder->student;
            if ($student && $student->email) {
                $this->sendExtensionStatusEmail($extensionOrder, $product, $student, 'approved');
            }

            DB::commit();

            return redirect()->back()->with('success', 'Extension approved and access extended successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Extension approval failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to approve extension: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, CourseExtensionOrder $extensionOrder)
    {
        if ($extensionOrder->payment_status !== 'pending') {
            return redirect()->back()->with('error', 'This order has already been processed.');
        }

        $extensionOrder->update([
            'payment_status' => 'rejected',
            'payment_success' => false,
            'notes' => $request->rejection_reason ?? 'Rejected by administrator',
        ]);

        $product = $extensionOrder->product;
        Notification::create([
            'notifiable_type' => 'App\\Models\\Student',
            'notifiable_id' => $extensionOrder->student_id,
            'type' => 'course_extension',
            'title' => 'Course Extension Rejected',
            'message' => 'Your extension request for "' . ($product->titre ?? 'Course') . '" has been rejected. Reason: ' . ($request->rejection_reason ?? 'No reason provided'),
            'data' => json_encode(['extension_order_id' => $extensionOrder->id]),
            'is_read' => false,
        ]);

        $student = $extensionOrder->student;
        if ($student && $student->email) {
            $this->sendExtensionStatusEmail(
                $extensionOrder,
                $product,
                $student,
                'rejected',
                $request->rejection_reason ?? 'No reason provided'
            );
        }

        return redirect()->back()->with('success', 'Extension order rejected.');
    }

    public function downloadReceipt(CourseExtensionOrder $extensionOrder)
    {
        if (!$extensionOrder->payment_receipt) {
            return redirect()->back()->with('error', 'No receipt file found.');
        }

        $userStoragePath = base_path('../user/storage/app/public/' . $extensionOrder->payment_receipt);

        if (!file_exists($userStoragePath)) {
            return redirect()->back()->with('error', 'Receipt file not found on server.');
        }

        return response()->download($userStoragePath);
    }

    private function sendExtensionStatusEmail(
        CourseExtensionOrder $extensionOrder,
        $product,
        $student,
        string $status,
        ?string $reason = null
    ): void {
        $status = strtolower($status) === 'approved' ? 'approved' : 'rejected';

        $subject = $status === 'approved'
            ? 'Course Extension Approved'
            : 'Course Extension Disapproved';

        $logType = $status === 'approved'
            ? 'course_extension_approved'
            : 'course_extension_rejected';

        try {
            Mail::send('emails.extension-status', [
                'student' => $student,
                'product' => $product,
                'extensionOrder' => $extensionOrder,
                'status' => $status,
                'reason' => $reason,
            ], function ($message) use ($student, $subject) {
                $message->to($student->email, $student->full_name ?? $student->nom)
                    ->subject($subject);
            });

            \App\Models\EmailLog::logSent(
                $student->email,
                $logType,
                $subject,
                $student->id,
                ($student->prenom ?? '') . ' ' . ($student->nom ?? ''),
                'CourseExtensionOrder',
                $extensionOrder->id
            );
        } catch (\Exception $e) {
            Log::error("Failed to send extension {$status} email: " . $e->getMessage());
            \App\Models\EmailLog::logFailed(
                $student->email,
                $logType,
                $subject,
                $e->getMessage(),
                $student->id,
                ($student->prenom ?? '') . ' ' . ($student->nom ?? ''),
                'CourseExtensionOrder',
                $extensionOrder->id
            );
        }
    }
}
