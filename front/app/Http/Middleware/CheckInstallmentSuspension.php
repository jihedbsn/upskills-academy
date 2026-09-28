<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\OrderSpecifique;

/**
 * CheckInstallmentSuspension Middleware
 * 
 * Checks if the authenticated student has any suspended installment orders.
 * If suspended, restricts access to the dashboard and redirects to the
 * suspension page with the message:
 * "Please pay your overdue installment to restore access."
 * 
 * This middleware should be applied to student dashboard routes.
 */
class CheckInstallmentSuspension
{
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

        // Check for suspended installment orders
        $hasSuspension = OrderSpecifique::where('student_id', $student->id)
            ->where('is_suspended', true)
            ->exists();

        if ($hasSuspension) {
            // Allow access to specific routes even when suspended
            $allowedRoutes = [
                'student-dashboard.installments.index',
                'student-dashboard.installments.show',
                'student-dashboard.installments.suspended',
                'student-dashboard.installments.checkout',
                'student-dashboard.installments.checkout-start-payment',
                'student-dashboard.installments.process-checkout',
                'student-dashboard.installments.payment-options',
                'student-dashboard.installments.pay',
                'student-dashboard.installments.payment-success',
                'student-dashboard.installments.payment-cancel',
                'student-dashboard.profile',
                'student-dashboard.profile.update',
                'student-dashboard.support.index',
                'logout',
            ];

            $currentRoute = $request->route() ? $request->route()->getName() : '';

            if (!in_array($currentRoute, $allowedRoutes)) {
                return redirect()->route('student-dashboard.installments.suspended')
                    ->with('warning', __('messages.suspended_access_message'));
            }
        }

        return $next($request);
    }
}
