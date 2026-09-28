<?php

namespace App\Services;

use App\Models\OrderSpecifique;
use App\Models\Installment;
use App\Models\Product;
use App\Models\ProductStudent;
use App\Models\ContentMilestone;
use App\Models\ProductStudy;
use App\Models\Quiz;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * InstallmentService (User Project)
 * 
 * Handles installment-related business logic for the student-facing side:
 * - Creating installment orders from student checkout
 * - Querying payment schedules and content access
 * - Content gating checks
 */
class InstallmentService
{
    /**
     * Daily late fee amount in dollars.
     */
    const DAILY_LATE_FEE = 5.00;

    /**
     * Create an installment order from student checkout.
     *
     * Automatically calculates installments based on course duration.
     * First installment must be paid immediately during checkout.
     *
     * @param int   $studentId
     * @param int   $productId
     * @param float $totalPrice
     * @param int|null $productVariationId
     * @return OrderSpecifique
     */
    public function createInstallmentOrder(
        int $studentId,
        int $productId,
        float $totalPrice,
        ?int $productVariationId = null
    ): OrderSpecifique {
        $product = Product::findOrFail($productId);

        $totalInstallments = $product->validity_months ?? 1;
        if ($totalInstallments < 1) {
            $totalInstallments = 1;
        }

        $installmentAmount = round($totalPrice / $totalInstallments, 2);

        return DB::transaction(function () use (
            $studentId, $productId, $totalPrice, $productVariationId,
            $totalInstallments, $installmentAmount, $product
        ) {
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
                'is_suspended' => false,
                'late_fee_total' => 0,
                'payment_type' => 'student_checkout',
            ]);

            for ($i = 1; $i <= $totalInstallments; $i++) {
                Installment::create([
                    'order_specifique_id' => $order->id,
                    'amount' => $installmentAmount,
                    'due_date' => now()->addMonths($i),
                    'status' => 'pending',
                    'installment_number' => $i,
                ]);
            }

            Log::info("Installment order created from student checkout", [
                'order_id' => $order->id,
                'student_id' => $studentId,
                'product_id' => $productId,
                'total_installments' => $totalInstallments,
            ]);

            return $order;
        });
    }

    /**
     * Process an installment payment and grant/maintain course access.
     *
     * Works for any installment (first or subsequent):
     * 1. Marks the installment as paid
     * 2. Updates order totals
     * 3. Lifts suspension if applicable
     * 4. Grants course access (first installment) or restores it
     *
     * @param OrderSpecifique $order
     * @param Installment $installment The specific installment to mark as paid
     * @param string|null $paymentMethod
     * @return Installment
     */
    public function processFirstInstallment(
        OrderSpecifique $order,
        Installment $installment,
        ?string $paymentMethod = null
    ): Installment {
        return DB::transaction(function () use ($order, $installment, $paymentMethod) {
            // Skip if already paid (idempotency)
            if ($installment->status === 'paid') {
                return $installment;
            }

            // Mark installment as paid
            $installment->update([
                'status' => 'paid',
                'paid_date' => now(),
                'payment_method' => $paymentMethod,
            ]);

            // Recalculate order totals from all paid installments
            $totalPaid = $order->installments()->where('status', 'paid')->sum('amount');
            $paidCount = $order->installments()->where('status', 'paid')->count();

            $order->paid_amount = $totalPaid;
            $order->remaining_amount = max(0, $order->total_price - $totalPaid);
            $order->paid_installments = $paidCount;
            $order->status = $paidCount >= $order->total_installments ? 'paid' : 'partial';

            // Lift suspension if applicable
            if ($order->is_suspended) {
                $order->is_suspended = false;
                $order->suspended_at = null;
            }

            $order->save();

            // Grant or restore course access
            $product = Product::find($order->product_id);
            $expirationDate = null;
            if ($product && $product->validity_months) {
                $expirationDate = now()->addMonths($product->validity_months);
            }

            ProductStudent::updateOrCreate(
                [
                    'product_id' => $order->product_id,
                    'student_id' => $order->student_id,
                ],
                [
                    'date' => now()->toDateString(),
                    'is_active' => true,
                    'access_granted_at' => now(),
                    'expiration_date' => $expirationDate,
                    'is_expired' => false,
                ]
            );

            Log::info("Installment payment processed", [
                'order_id' => $order->id,
                'installment_id' => $installment->id,
                'installment_number' => $installment->installment_number,
                'student_id' => $order->student_id,
                'product_id' => $order->product_id,
                'paid_count' => $paidCount,
                'remaining' => $order->remaining_amount,
            ]);

            return $installment;
        });
    }

    /**
     * Get the student's installment orders with their installments.
     *
     * @param int $studentId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getStudentInstallmentOrders(int $studentId)
    {
        return OrderSpecifique::where('student_id', $studentId)
            ->with(['product', 'productVariation', 'installments'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get unlocked content for a student's course.
     *
     * @param int $productId
     * @param int $studentId
     * @return array
     */
    public function getUnlockedContent(int $productId, int $studentId): array
    {
        $order = OrderSpecifique::where('product_id', $productId)
            ->where('student_id', $studentId)
            ->whereIn('status', ['pending', 'partial', 'paid'])
            ->first();

        $enrollment = ProductStudent::where('product_id', $productId)
            ->where('student_id', $studentId)
            ->where('is_active', true)
            ->first();

        $orderedStudyIds = ProductStudy::where('products_id', $productId)
            ->orderBy('order')
            ->orderBy('id')
            ->pluck('id')
            ->toArray();

        if (!$order) {
            $product = Product::find($productId);
            $totalMonths = max(1, (int) ($product?->validity_months ?? 1));
            $effectiveMonths = $this->computeElapsedMonthsForOutrightPurchase($enrollment, $totalMonths);

            $hasMilestones = ContentMilestone::where('product_id', $productId)->exists();
            if ($hasMilestones && $effectiveMonths > 0) {
                $candidateStudyIds = $this->resolveUnlockedStudyIds($productId, $effectiveMonths, $totalMonths);
            } else {
                $candidateStudyIds = $orderedStudyIds;
            }
            $filteredStudyIds = $this->filterStudyIdsByPurchaseSchedule($enrollment, $candidateStudyIds);

            return [
                'has_installment_order' => false,
                'is_all_unlocked' => count($orderedStudyIds) > 0 && count($filteredStudyIds) >= count($orderedStudyIds),
                'unlocked_study_ids' => $filteredStudyIds,
                'unlocked_months' => $effectiveMonths,
                'total_months' => $totalMonths,
                'is_suspended' => false,
            ];
        }

        $paidInstallments = $order->paid_installments;
        $totalInstallments = $order->total_installments;

        $unlockedStudyIds = $this->resolveUnlockedStudyIds(
            $productId,
            $paidInstallments,
            $totalInstallments
        );

        $unlockedStudyIds = $this->filterStudyIdsByPurchaseSchedule($enrollment, $unlockedStudyIds);

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
     * True when enrollment has reached calendar-day N relative to {@see ProductStudent::access_granted_at}.
     * Null pivot / enrollment edge cases preserve legacy behavior for existing data.
     */
    public function isUnlockedAfterPurchaseDays(?ProductStudent $enrollment, mixed $opensAfterPurchaseDays): bool
    {
        if ($opensAfterPurchaseDays === null || $opensAfterPurchaseDays === '') {
            return true;
        }

        $days = (int) $opensAfterPurchaseDays;
        if ($days < 1) {
            return true;
        }

        if (!$enrollment) {
            return false;
        }

        $anchor = $enrollment->access_granted_at ?? $enrollment->date ?? null;
        if (!$anchor) {
            return true;
        }

        $startsOn = Carbon::parse($anchor)->startOfDay();
        $opensAt = $startsOn->copy()->addDays($days);

        return now()->greaterThanOrEqualTo($opensAt);
    }

    /**
     * @return string|null Reason: suspended, purchase_delay, installment_milestone, final_exam_installments — or null when allowed.
     */
    public function getQuizAccessBlockReason(Product $product, Quiz $quiz, int $studentId): ?string
    {
        $pivot = $product->quizzes()->where('quiz_id', $quiz->id)->first()?->pivot;

        $enrollment = ProductStudent::where('product_id', $product->id)
            ->where('student_id', $studentId)
            ->where('is_active', true)
            ->first();

        if (! $this->isUnlockedAfterPurchaseDays($enrollment, $pivot?->opens_after_purchase_days ?? null)) {
            return 'purchase_delay';
        }

        $order = OrderSpecifique::where('student_id', $studentId)
            ->where('product_id', $product->id)
            ->whereIn('status', ['pending', 'partial', 'paid'])
            ->first();

        if ($order && $order->is_suspended) {
            return 'suspended';
        }

        $typeId = (int) $quiz->type_id;

        if ($typeId === 1) {
            $examOrder = OrderSpecifique::where('student_id', $studentId)
                ->where('product_id', $product->id)
                ->where('status', '!=', 'cancelled')
                ->first();

            if ($examOrder && ! $examOrder->isAllPaid()) {
                return 'final_exam_installments';
            }

            return null;
        }

        if (in_array($typeId, [2, 3], true)) {
            if ($order) {
                $effectiveMonths = (int) $order->paid_installments;
            } else {
                $totalMonths = max(1, (int) ($product->validity_months ?? 1));
                $effectiveMonths = $this->computeElapsedMonthsForOutrightPurchase($enrollment, $totalMonths);
            }

            if (! $this->isQuizUnlockedForInstallmentProgress($effectiveMonths, $pivot?->installment_month ?? null)) {
                return 'installment_milestone';
            }
        }

        return null;
    }

    /**
     * Whether a course quiz or practical exam is unlocked for the student's paid-installment count,
     * using quiz_products.installment_month (null = first tranche).
     */
    public function isQuizUnlockedForInstallmentProgress(int $paidInstallments, mixed $pivotInstallmentMonth): bool
    {
        if ($paidInstallments <= 0) {
            return false;
        }

        $required = ($pivotInstallmentMonth !== null && $pivotInstallmentMonth !== '')
            ? max(1, (int) $pivotInstallmentMonth)
            : 1;

        return $paidInstallments >= $required;
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
        $study = ProductStudy::where('products_id', $productId)
            ->where('id', $productStudyId)
            ->first();

        $enrollment = ProductStudent::where('product_id', $productId)
            ->where('student_id', $studentId)
            ->where('is_active', true)
            ->first();

        if (! $this->isUnlockedAfterPurchaseDays($enrollment, $study?->opens_after_purchase_days)) {
            return false;
        }

        $order = OrderSpecifique::where('product_id', $productId)
            ->where('student_id', $studentId)
            ->whereIn('status', ['pending', 'partial', 'paid'])
            ->first();

        if (!$order) {
            $hasMilestones = ContentMilestone::where('product_id', $productId)->exists();
            if (!$hasMilestones) {
                return true;
            }
            $product = Product::find($productId);
            $totalMonths = max(1, (int) ($product?->validity_months ?? 1));
            $effectiveMonths = $this->computeElapsedMonthsForOutrightPurchase($enrollment, $totalMonths);
            $unlockedStudyIds = $this->resolveUnlockedStudyIds($productId, $effectiveMonths, $totalMonths);
            return in_array($productStudyId, $unlockedStudyIds, true);
        }

        if ($order->is_suspended) {
            return false;
        }

        $unlockedStudyIds = $this->resolveUnlockedStudyIds(
            $productId,
            (int) $order->paid_installments,
            (int) $order->total_installments
        );

        $unlockedStudyIds = $this->filterStudyIdsByPurchaseSchedule($enrollment, $unlockedStudyIds);

        return in_array($productStudyId, $unlockedStudyIds, true);
    }

    /**
     * Effective progress for an outright (non-installment) purchase: month 1 immediately, month N
     * after N-1 elapsed months. Lets `installment_month` splitting apply even without an order.
     */
    private function computeElapsedMonthsForOutrightPurchase(?ProductStudent $enrollment, int $totalMonths): int
    {
        if (!$enrollment) {
            return 0;
        }
        $anchor = $enrollment->access_granted_at ?? $enrollment->date ?? null;
        if (!$anchor) {
            return $totalMonths;
        }
        $elapsed = (int) Carbon::parse($anchor)->startOfDay()->diffInMonths(now()) + 1;
        return max(1, min($totalMonths, $elapsed));
    }

    /**
     * @param array<int> $studyIds
     * @return array<int>
     */
    private function filterStudyIdsByPurchaseSchedule(?ProductStudent $enrollment, array $studyIds): array
    {
        if ($studyIds === []) {
            return [];
        }

        $studies = ProductStudy::whereIn('id', $studyIds)->get()->keyBy('id');
        $ordered = [];
        foreach ($studyIds as $id) {
            $study = $studies->get($id);
            if ($this->isUnlockedAfterPurchaseDays($enrollment, $study?->opens_after_purchase_days)) {
                $ordered[] = $id;
            }
        }

        return $ordered;
    }

    /**
     * Resolve unlocked study IDs for an installment-based order.
     *
     * Priority:
     * 1) Use explicit content milestones if configured
     * 2) Fallback to progressive unlocking by study order
     *
     * @param int $productId
     * @param int $paidInstallments
     * @param int $totalInstallments
     * @return array<int>
     */
    private function resolveUnlockedStudyIds(
        int $productId,
        int $paidInstallments,
        int $totalInstallments
    ): array {
        if ($paidInstallments <= 0) {
            return [];
        }

        $milestoneStudyIds = ContentMilestone::where('product_id', $productId)
            ->where('milestone_month', '<=', $paidInstallments)
            ->pluck('product_study_id')
            ->unique()
            ->values()
            ->toArray();

        if (!empty($milestoneStudyIds)) {
            return $milestoneStudyIds;
        }

        $orderedStudyIds = ProductStudy::where('products_id', $productId)
            ->orderBy('order')
            ->orderBy('id')
            ->pluck('id')
            ->toArray();

        $totalStudies = count($orderedStudyIds);
        if ($totalStudies === 0) {
            return [];
        }

        $safeTotalInstallments = max(1, $totalInstallments);
        $unlockCount = (int) ceil(($paidInstallments / $safeTotalInstallments) * $totalStudies);
        $unlockCount = min($totalStudies, max(0, $unlockCount));

        return array_slice($orderedStudyIds, 0, $unlockCount);
    }

    /**
     * Check if a student can access the final exam for a course.
     * Requires Total_Due = 0 (all installments fully paid).
     *
     * @param int $productId
     * @param int $studentId
     * @return bool
     */
    public function canAccessFinalExam(int $productId, int $studentId): bool
    {
        $order = OrderSpecifique::where('product_id', $productId)
            ->where('student_id', $studentId)
            ->whereIn('status', ['pending', 'partial', 'paid'])
            ->first();

        if (!$order) {
            return true;
        }

        return $order->canAccessFinalExam();
    }

    /**
     * Check if a student can receive a certificate for a course.
     *
     * @param int $productId
     * @param int $studentId
     * @return bool
     */
    public function canReceiveCertificate(int $productId, int $studentId): bool
    {
        $order = OrderSpecifique::where('product_id', $productId)
            ->where('student_id', $studentId)
            ->whereIn('status', ['pending', 'partial', 'paid'])
            ->first();

        if (!$order) {
            return true;
        }

        return $order->canReceiveCertificate();
    }

    /**
     * Process overdue installments on due date (day 0) and suspend accounts.
     *
     * @return array<string,int>
     */
    public function processOverdueInstallments(): array
    {
        $stats = [
            'total_checked' => 0,
            'newly_overdue' => 0,
            'newly_suspended' => 0,
            'errors' => 0,
        ];

        $today = now()->startOfDay();

        $overdueInstallments = Installment::where('status', 'pending')
            ->whereNotNull('due_date')
            ->where('due_date', '<=', $today)
            ->with('orderSpecifique')
            ->get();

        $stats['total_checked'] = $overdueInstallments->count();

        foreach ($overdueInstallments as $installment) {
            try {
                DB::transaction(function () use ($installment, &$stats) {
                    $installment->update(['status' => 'overdue']);
                    $stats['newly_overdue']++;

                    $order = $installment->orderSpecifique;
                    if ($order && !$order->is_suspended) {
                        $order->update([
                            'is_suspended' => true,
                            'suspended_at' => now(),
                        ]);
                        $stats['newly_suspended']++;
                    }
                });
            } catch (\Exception $e) {
                $stats['errors']++;
                Log::error('Error processing overdue installment', [
                    'installment_id' => $installment->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $stats;
    }

    /**
     * Apply daily $5 late fee to overdue installments.
     *
     * @return array<string,int|float>
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

        $overdueInstallments = Installment::where('status', 'overdue')
            ->with('orderSpecifique')
            ->get();

        $stats['total_overdue'] = $overdueInstallments->count();

        foreach ($overdueInstallments as $installment) {
            try {
                $alreadyCharged = LateFeeLog::where('installment_id', $installment->id)
                    ->where('charged_date', $today)
                    ->exists();

                if ($alreadyCharged) {
                    continue;
                }

                DB::transaction(function () use ($installment, $today, &$stats) {
                    $feeAmount = self::DAILY_LATE_FEE;

                    LateFeeLog::create([
                        'order_specifique_id' => $installment->order_specifique_id,
                        'installment_id' => $installment->id,
                        'fee_amount' => $feeAmount,
                        'charged_date' => $today,
                        'notes' => "Daily late fee for overdue installment #{$installment->installment_number}",
                    ]);

                    $installment->increment('late_fee', $feeAmount);

                    $order = $installment->orderSpecifique;
                    if ($order) {
                        $order->increment('late_fee_total', $feeAmount);
                    }

                    $stats['fees_applied']++;
                    $stats['total_fee_amount'] += $feeAmount;
                });
            } catch (\Exception $e) {
                $stats['errors']++;
                Log::error('Error applying late fee', [
                    'installment_id' => $installment->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('Daily late fees applied', $stats);

        return $stats;
    }

    /**
     * Send pre-due reminders for pending installments.
     * Reminders are sent at 20, 10, 5 and 1 days before due date.
     *
     * @return array<string,int>
     */
    public function sendDueInstallmentReminders(): array
    {
        $stats = [
            'checked' => 0,
            'sent' => 0,
            'skipped' => 0,
            'errors' => 0,
        ];

        $reminderDays = [20, 10, 5, 1];
        $today = now()->startOfDay();
        $targetDates = collect($reminderDays)
            ->mapWithKeys(fn (int $days) => [$today->copy()->addDays($days)->toDateString() => $days])
            ->toArray();

        $installments = Installment::whereIn('status', ['pending', 'awaiting_payment'])
            ->whereNotNull('due_date')
            ->whereIn(DB::raw('DATE(due_date)'), array_keys($targetDates))
            ->with(['orderSpecifique.product'])
            ->get();

        $stats['checked'] = $installments->count();

        foreach ($installments as $installment) {
            try {
                $order = $installment->orderSpecifique;
                if (!$order || !$order->student_id) {
                    $stats['skipped']++;
                    continue;
                }

                $dueDate = optional($installment->due_date)->toDateString();
                $daysLeft = $targetDates[$dueDate] ?? null;
                if (!$daysLeft) {
                    $stats['skipped']++;
                    continue;
                }

                $alreadySent = Notification::where('notifiable_type', 'App\Models\User')
                    ->where('notifiable_id', $order->student_id)
                    ->where('type', Notification::TYPE_INSTALLMENT)
                    ->where('title', 'Installment payment reminder')
                    ->where('data->installment_id', (string) $installment->id)
                    ->where('data->days_left', (string) $daysLeft)
                    ->exists();

                if ($alreadySent) {
                    $stats['skipped']++;
                    continue;
                }

                $courseTitle = $order->product->titre ?? ('Course #' . $order->product_id);
                $message = "Your installment #{$installment->installment_number} for {$courseTitle} is due in {$daysLeft} day(s) on {$installment->formatted_due_date}.";

                Notification::notifyStudent(
                    $order->student_id,
                    Notification::TYPE_INSTALLMENT,
                    'Installment payment reminder',
                    $message,
                    route('student-dashboard.installments.show', ['locale' => app()->getLocale(), 'orderSpecifique' => $order->id]),
                    [
                        'order_id' => $order->id,
                        'installment_id' => $installment->id,
                        'installment_number' => $installment->installment_number,
                        'days_left' => $daysLeft,
                        'due_date' => $installment->formatted_due_date,
                        'amount' => (float) $installment->amount,
                    ],
                    'calendar',
                    'orange',
                    $daysLeft <= 5
                );

                $stats['sent']++;
            } catch (\Exception $e) {
                $stats['errors']++;
                Log::error('Error sending installment reminder', [
                    'installment_id' => $installment->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $stats;
    }
}
