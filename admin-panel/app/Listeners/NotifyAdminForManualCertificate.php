<?php

namespace App\Listeners;

use App\Events\StudentSuccessApproved;
use App\Mail\AdminManualCertificateNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotifyAdminForManualCertificate implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(StudentSuccessApproved $event): void
    {
        $studentSuccess = $event->studentSuccess;
        $product = $studentSuccess->product;

        // Debug logging
        Log::info('NotifyAdminForManualCertificate listener triggered', [
            'student_success_id' => $studentSuccess->id,
            'product_id' => $product->id,
            'product_name' => $product->titre ?? 'N/A',
            'certif_id' => $product->certif_id,
            'certificate_generation_mode' => $product->certificate_generation_mode,
            'has_certif' => !empty($product->certif_id),
            'is_manual' => $product->certificate_generation_mode === 'manual' || is_null($product->certificate_generation_mode)
        ]);

        // Check if certificate generation mode is manual and product has certificate template
        // If certificate_generation_mode is NULL or empty, treat it as manual (default behavior)
        if ($product->certif_id && ($product->certificate_generation_mode === 'manual' || is_null($product->certificate_generation_mode))) {
            try {
                // Get admin email from config or environment
                $adminEmail = config('mail.admin_email', env('ADMIN_EMAIL', 'admin@example.com'));

                // Send notification to admin
                Mail::to($adminEmail)
                    ->send(new AdminManualCertificateNotification($studentSuccess));

                Log::info('Admin notified for manual certificate generation', [
                    'student_success_id' => $studentSuccess->id,
                    'student_name' => $studentSuccess->student->full_name,
                    'course' => $product->titre,
                    'admin_email' => $adminEmail
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send admin notification for manual certificate', [
                    'error' => $e->getMessage(),
                    'student_success_id' => $studentSuccess->id
                ]);
            }
        }
    }
}
