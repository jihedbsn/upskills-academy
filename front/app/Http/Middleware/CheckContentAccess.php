<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\InstallmentService;

/**
 * CheckContentAccess Middleware
 * 
 * Implements the Drip Content system tied to installment payments.
 * 
 * Content Access Rules:
 * - Level 1: Unlocked immediately after the first installment is paid
 *   (includes only Month 1 lectures)
 * - Subsequent Levels: Remain programmatically locked (Status: Locked)
 * - Automatic Unlock: When installment (X) payment succeeds, the system
 *   changes the associated content status from "Locked" to "Active"
 * 
 * This middleware checks if a student can access a specific study/lecture
 * based on their installment payment progress.
 */
class CheckContentAccess
{
    protected InstallmentService $installmentService;

    public function __construct(InstallmentService $installmentService)
    {
        $this->installmentService = $installmentService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $student = Auth::user();

        // Get the study from the route parameter
        $study = $request->route('study');

        if (!$study || !$study->product) {
            return $next($request);
        }

        $productId = $study->products_id ?? $study->product_id;
        $studyId = $study->id;

        // Check if content is accessible based on installment payments
        $isAccessible = $this->installmentService->isContentAccessible(
            $productId,
            $studyId,
            $student->id
        );

        if (!$isAccessible) {
            // Check if suspended
            $order = \App\Models\OrderSpecifique::where('product_id', $productId)
                ->where('student_id', $student->id)
                ->where('is_suspended', true)
                ->first();

            if ($order) {
                return redirect()->route('student-dashboard.installments.suspended')
                    ->with('warning', __('messages.suspended_access_message'));
            }

            // Content is locked due to unpaid installment
            return redirect()->back()
                ->with('error', __('messages.content_locked'));
        }

        return $next($request);
    }
}
