<?php

namespace App\Services;

use App\Models\OrderSpecifique;
use App\Models\Installment;
use App\Models\Product;
use App\Models\ProductStudent;
use App\Models\ContentMilestone;
use App\Models\LateFeeLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * InstallmentService
 * 
 * Handles all installment-related business logic:
 * - Creating installment orders with payment schedules
 * - Processing installment payments and unlocking content
 * - Handling overdue installments and suspensions
 * - Applying daily late fees
 * - Content gating based on payment status
 */
class InstallmentService
{
    /**
     * Daily late fee amount in dollars.
     */
    const DAILY_LATE_FEE = 5.00;

    /**
     * Create a new installment order for a student.
     *
     * Automatically calculates installments based on course duration.
     * Example: A 3-month course = 3 installments.
     *
     * @param int    $studentId
     * @param int    $productId
     * @param float  $totalPrice
     * @param int|null $productVariationId
     * @param string|null $notes
     * @param string $paymentType  'admin_created' or 'student_checkout'
     * @return OrderSpecifique
     */
    public function createInstallmentOrder(
        int $studentId,
        int $productId,
        float $totalPrice,
        ?int $productVariationId = null,
        ?string $notes = null,
        string $paymentType = 'admin_created'
    ): OrderSpecifique {
        $product = Product::findOrFail($productId);

        // Calculate installments based on course duration (validity_months)
        $totalInstallments = $product->validity_months ?? 1;
        if ($totalInstallments < 1) {
            $totalInstallments = 1;
        }

        $installmentAmount = round($totalPrice / $totalInstallments, 2);

        return DB::transaction(function () use (
            $studentId, $productId, $totalPrice, $productVariationId,
            $notes, $paymentType, $totalInstallments, $installmentAmount, $product
        ) {
            // Create the installment order
            $order = OrderSpecifique::create([
                'student_id' => $studentId,
                'product_id' => $productId,
                'product_variation_id' => $productVariationId,
                'total_price' => $totalPrice,
                'paid_amount' => 0,
                'remaining_amount' => $totalPrice,
                'status' => 'pending',
                'total_installments' => $totalInstallments,
                'paid_installments' => 0,
                'notes' => $notes,
                'is_suspended' => false,
                'late_fee_total' => 0,
                'payment_type' => $paymentType,
            ]);

            // Create individual installments with due dates
            for ($i = 1; $i <= $totalInstallments; $i++) {
                Installment::create([
                    'order_specifique_id' => $order->id,
                    'amount' => $installmentAmount,
                    'due_date' => now()->addMonths($i),
                    'status' => 'pending',
                    'installment_number' => $i,
                ]);
            }

            Log::info("Installment order created", [
                'order_id' => $order->id,
                'student_id' => $studentId,
                'product_id' => $productId,
                'total_price' => $totalPrice,
                'total_installments' => $totalInstallments,
                'payment_type' => $paymentType,
            ]);

            return $order;
        });
    }

    /**
     * Process a payment for a specific installment.
     *
     * When an installment is paid:
     * 1. Mark the installment as paid
     * 2. Update the order totals
     * 3. Unlock the corresponding content milestone
     * 4. Grant/restore course access if suspended
     * 5. If first installment, grant initial course access
     *
     * @param Installment $installment
     * @param string|null $paymentMethod
     * @param string|null $notes
     * @return OrderSpecifique
     */
    public function processInstallmentPayment(
        Installment $installment,
        ?string $paymentMethod = null,
        ?string $notes = null,
        ?string $paidDate = null
    ): OrderSpecifique {
        return DB::transaction(function () use ($installment, $paymentMethod, $notes, $paidDate) {
            // Mark installment as paid
            $installment->markAsPaid($paymentMethod, $notes, $paidDate);

            // Update order totals
            $order = $installment->orderSpecifique;
            $order->paid_amount = $order->installments()->where('status', 'paid')->sum('amount');
            $order->remaining_amount = max(0, $order->total_price - $order->paid_amount);
            $order->paid_installments = $order->installments()->where('status', 'paid')->count();
            $order->updateStatus();

            // Lift suspension if applicable
            if ($order->is_suspended) {
                $order->liftSuspension();
            }

            // Grant course access (first installment unlocks Month 1)
            $this->ensureCourseAccess($order);

            Log::info("Installment payment processed", [
                'installment_id' => $installment->id,
                'order_id' => $order->id,
                'amount' => $installment->amount,
                'paid_installments' => $order->paid_installments,
                'remaining' => $order->remaining_amount,
            ]);

            return $order;
        });
    }

    /**
     * Ensure the student has course access based on payment status.
     * Grants access after first installment is paid.
     */
    public function ensureCourseAccess(OrderSpecifique $order): void
    {
        if ($order->paid_installments > 0 && !$order->is_suspended) {
            $existing = ProductStudent::where('student_id', $order->student_id)
                ->where('product_id', $order->product_id)
                ->first();

            if ($existing) {
                if (!$existing->is_active) {
                    $existing->grantAccess();
                }
            } else {
                $product = Product::find($order->product_id);
                $expirationDate = null;
                if ($product && $product->validity_months) {
                    $expirationDate = now()->addMonths($product->validity_months);
                }

                ProductStudent::create([
                    'product_id' => $order->product_id,
                    'student_id' => $order->student_id,
                    'date' => now()->toDateString(),
                    'is_active' => true,
                    'access_granted_at' => now(),
                    'expiration_date' => $expirationDate,
                    'is_expired' => false,
                ]);
            }
        }
    }

    /**
     * Process all overdue installments.
     * 
     * Called by the daily cron job at midnight.
     * For each overdue installment:
     * 1. Mark it as overdue
     * 2. Suspend the student's access
     * 
     * @return array Statistics about processed installments
     */
    public function processOverdueInstallments(): array
    {
        $stats = [
            'total_checked' => 0,
            'newly_overdue' => 0,
            'newly_suspended' => 0,
            'errors' => 0,
        ];

        // Find all pending installments that are past due
        $overdueInstallments = Installment::where('status', 'pending')
            ->whereNotNull('due_date')
            ->where('due_date', '<', now()->startOfDay())
            ->with('orderSpecifique')
            ->get();

        $stats['total_checked'] = $overdueInstallments->count();

        foreach ($overdueInstallments as $installment) {
            try {
                DB::transaction(function () use ($installment, &$stats) {
                    // Mark as overdue
                    $installment->update(['status' => 'overdue']);
                    $stats['newly_overdue']++;

                    // Suspend the order if not already suspended
                    $order = $installment->orderSpecifique;
                    if ($order && !$order->is_suspended) {
                        $order->suspend();
                        $stats['newly_suspended']++;

                        Log::warning("Student suspended due to overdue installment", [
                            'order_id' => $order->id,
                            'student_id' => $order->student_id,
                            'product_id' => $order->product_id,
                            'installment_id' => $installment->id,
                            'due_date' => $installment->due_date->toDateString(),
                        ]);
                    }
                });
            } catch (\Exception $e) {
                $stats['errors']++;
                Log::error("Error processing overdue installment", [
                    'installment_id' => $installment->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $stats;
    }

    /**
     * Apply daily late fees to all overdue installments.
     * 
     * Called by the daily cron job at midnight.
     * Adds $5 per day to each overdue installment's outstanding balance.
     *
     * @return array Statistics about applied fees
     */
    public function applyDailyLateFees(): array
    {
        $stats = [
            'total_overdue' => 0,
            'fees_applied' => 0,
            'total_fee_amount' => 0,
            'errors' => 0,
        ];

        $today = now()->toDateString();

        // Find all overdue installments
        $overdueInstallments = Installment::where('status', 'overdue')
            ->with('orderSpecifique')
            ->get();

        $stats['total_overdue'] = $overdueInstallments->count();

        foreach ($overdueInstallments as $installment) {
            try {
                // Check if fee was already applied today
                $alreadyCharged = LateFeeLog::where('installment_id', $installment->id)
                    ->where('charged_date', $today)
                    ->exists();

                if ($alreadyCharged) {
                    continue;
                }

                DB::transaction(function () use ($installment, $today, &$stats) {
                    $feeAmount = self::DAILY_LATE_FEE;

                    // Create late fee log entry
                    LateFeeLog::create([
                        'order_specifique_id' => $installment->order_specifique_id,
                        'installment_id' => $installment->id,
                        'fee_amount' => $feeAmount,
                        'charged_date' => $today,
                        'notes' => "Daily late fee for overdue installment #{$installment->installment_number}",
                    ]);

                    // Update installment late fee
                    $installment->increment('late_fee', $feeAmount);

                    // Update order total late fees
                    $order = $installment->orderSpecifique;
                    if ($order) {
                        $order->increment('late_fee_total', $feeAmount);
                        $order->remaining_amount = max(0, $order->total_price - $order->paid_amount);
                        $order->save();
                    }

                    $stats['fees_applied']++;
                    $stats['total_fee_amount'] += $feeAmount;
                });
            } catch (\Exception $e) {
                $stats['errors']++;
                Log::error("Error applying late fee", [
                    'installment_id' => $installment->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info("Daily late fees applied", $stats);
        return $stats;
    }

    /**
     * Get unlocked content studies for a student's installment order.
     * 
     * Returns the IDs of ProductStudy records that the student
     * has unlocked based on their paid installments.
     *
     * @param int $productId
     * @param int $studentId
     * @return array ['unlocked_study_ids' => [...], 'unlocked_months' => int, 'total_months' => int]
     */
    public function getUnlockedContent(int $productId, int $studentId): array
    {
        $order = OrderSpecifique::where('product_id', $productId)
            ->where('student_id', $studentId)
            ->whereIn('status', ['pending', 'partial', 'paid'])
            ->first();

        // If no installment order, all content is accessible
        if (!$order) {
            return [
                'has_installment_order' => false,
                'unlocked_study_ids' => [],
                'unlocked_months' => 0,
                'total_months' => 0,
                'is_all_unlocked' => true,
            ];
        }

        $paidInstallments = $order->paid_installments;
        $totalInstallments = $order->total_installments;

        // Get unlocked content milestone study IDs
        $unlockedStudyIds = ContentMilestone::where('product_id', $productId)
            ->where('milestone_month', '<=', $paidInstallments)
            ->pluck('product_study_id')
            ->toArray();

        return [
            'has_installment_order' => true,
            'unlocked_study_ids' => $unlockedStudyIds,
            'unlocked_months' => $paidInstallments,
            'total_months' => $totalInstallments,
            'is_all_unlocked' => $paidInstallments >= $totalInstallments,
            'is_suspended' => $order->is_suspended,
            'order' => $order,
        ];
    }

    /**
     * Check if a specific study/lecture is accessible to the student.
     *
     * @param int $productId
     * @param int $productStudyId
     * @param int $studentId
     * @return bool
     */
    public function isContentAccessible(int $productId, int $productStudyId, int $studentId): bool
    {
        $order = OrderSpecifique::where('product_id', $productId)
            ->where('student_id', $studentId)
            ->whereIn('status', ['pending', 'partial', 'paid'])
            ->first();

        // No installment order — content is accessible normally
        if (!$order) {
            return true;
        }

        // If suspended, no content access
        if ($order->is_suspended) {
            return false;
        }

        // Check if there's a content milestone for this study
        $milestone = ContentMilestone::where('product_id', $productId)
            ->where('product_study_id', $productStudyId)
            ->first();

        // If no milestone is set for this study, it's accessible
        if (!$milestone) {
            return true;
        }

        // Content is accessible if the milestone month <= paid installments
        return $milestone->milestone_month <= $order->paid_installments;
    }
}
