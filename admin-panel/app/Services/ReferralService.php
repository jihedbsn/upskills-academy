<?php

namespace App\Services;

use App\Mail\ReferralNotificationEmail;
use App\Models\Notification;
use App\Models\Referral;
use App\Models\ReferralCode;
use App\Models\ReferralReward;
use App\Models\Student;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReferralService
{
    const REFERRER_REWARD_AMOUNT = 5.00;
    const REFERRED_DISCOUNT_PERCENT = 5;

    public function generateCodeForStudent(int $studentId): ReferralCode
    {
        $existing = ReferralCode::where('user_id', $studentId)->first();
        if ($existing) {
            return $existing;
        }

        do {
            $code = strtoupper(Str::random(8));
        } while (ReferralCode::where('code', $code)->exists());

        return ReferralCode::create([
            'user_id' => $studentId,
            'code'    => $code,
        ]);
    }

    public function registerReferral(int $referredStudentId, string $referralCode): ?Referral
    {
        $codeRecord = ReferralCode::where('code', $referralCode)->first();
        if (!$codeRecord) {
            return null;
        }

        // Can't refer yourself
        if ($codeRecord->user_id === $referredStudentId) {
            return null;
        }

        // Already referred
        if (Referral::where('referred_id', $referredStudentId)->exists()) {
            return null;
        }

        $referral = Referral::create([
            'referrer_id'   => $codeRecord->user_id,
            'referred_id'   => $referredStudentId,
            'status'        => 'pending',
            'reward_amount' => self::REFERRER_REWARD_AMOUNT,
        ]);

        $this->notifyReferrerOfSignup($referral);

        return $referral;
    }

    /**
     * Called after referred student's first successful purchase.
     * Blocked if the order used a coupon (no stacking).
     */
    public function processFirstPurchaseReward(int $studentId, int $orderId): bool
    {
        $referral = Referral::where('referred_id', $studentId)
            ->where('status', 'pending')
            ->first();

        if (!$referral) {
            return false;
        }

        // Block reward if order used a coupon (no stacking)
        $hasCoupon = DB::table('coupon_order_usage')->where('order_id', $orderId)->exists();
        if ($hasCoupon) {
            Log::info("Referral reward blocked for order {$orderId}: coupon was applied.");
            return false;
        }

        // Ensure the order itself was created AFTER the referral
        // (we don't reward retroactively on pre-existing orders)
        $order = Order::find($orderId);
        if (!$order || $order->created_at->lt($referral->created_at)) {
            return false;
        }

        // First purchase AFTER being referred — only count orders
        // created after the referral itself, not the student's lifetime history
        $previousOrdersAfterReferral = Order::where('student_id', $studentId)
            ->where('payment_success', true)
            ->where('id', '!=', $orderId)
            ->where('created_at', '>=', $referral->created_at)
            ->count();

        if ($previousOrdersAfterReferral > 0) {
            return false;
        }

        try {
            DB::transaction(function () use ($referral, $studentId, $orderId) {
                $referral->update([
                    'status'             => 'completed',
                    'completed_order_id' => $orderId,
                    'completed_at'       => now(),
                ]);

                // Referrer gets $5 cash/credit (default credit, user can change preference)
                $referrerPreference = $this->getRewardPreference($referral->referrer_id);
                ReferralReward::create([
                    'user_id'     => $referral->referrer_id,
                    'referral_id' => $referral->id,
                    'type'        => $referrerPreference,
                    'amount'      => self::REFERRER_REWARD_AMOUNT,
                    'role'        => 'referrer',
                ]);

                // Referred student gets 5% discount tracked as a reward record
                ReferralReward::create([
                    'user_id'     => $studentId,
                    'referral_id' => $referral->id,
                    'type'        => 'credit',
                    'amount'      => 0, // discount applied at checkout, tracked here
                    'role'        => 'referred',
                    'claimed_at'  => now(),
                ]);
            });

            $this->notifyReferralCompleted($referral->fresh());

            return true;
        } catch (\Exception $e) {
            Log::error('Referral reward processing failed: ' . $e->getMessage());
            return false;
        }
    }

    public function setRewardPreference(int $studentId, string $type): void
    {
        if (!in_array($type, ['cash', 'credit'])) {
            return;
        }

        // Persist on the student so it survives cache flushes / restarts
        Student::where('id', $studentId)->update(['referral_reward_preference' => $type]);

        // Update all unclaimed referrer rewards for this student
        ReferralReward::where('user_id', $studentId)
            ->where('role', 'referrer')
            ->whereNull('claimed_at')
            ->update(['type' => $type]);
    }

    public function getRewardPreference(int $studentId): string
    {
        return Student::where('id', $studentId)->value('referral_reward_preference') ?? 'credit';
    }

    public function claimReward(int $rewardId, int $studentId): bool
    {
        $reward = ReferralReward::where('id', $rewardId)
            ->where('user_id', $studentId)
            ->whereNull('claimed_at')
            ->first();

        if (!$reward) {
            return false;
        }

        $reward->update(['claimed_at' => now()]);
        return true;
    }

    public function overrideReward(int $referralId, array $data): bool
    {
        $referral = Referral::find($referralId);
        if (!$referral) {
            return false;
        }

        $wasCompletedBefore = $referral->status === 'completed';

        try {
            DB::transaction(function () use ($referral, $data) {
                $oldStatus = $referral->status;

                if (isset($data['status'])) {
                    $referral->update([
                        'status'       => $data['status'],
                        'completed_at' => $data['status'] === 'completed' ? now() : null,
                    ]);
                }

                if (isset($data['reward_amount'])) {
                    $referral->update(['reward_amount' => $data['reward_amount']]);
                }

                // If flipping pending → completed and no rewards exist yet, create them
                $isFlippingToCompleted = isset($data['status'])
                    && $data['status'] === 'completed'
                    && $oldStatus !== 'completed';

                if ($isFlippingToCompleted) {
                    $existingReferrerReward = ReferralReward::where('referral_id', $referral->id)
                        ->where('role', 'referrer')
                        ->exists();

                    if (!$existingReferrerReward) {
                        $type   = $data['type'] ?? $this->getRewardPreference($referral->referrer_id);
                        $amount = $data['reward_amount'] ?? $referral->reward_amount ?? self::REFERRER_REWARD_AMOUNT;

                        ReferralReward::create([
                            'user_id'     => $referral->referrer_id,
                            'referral_id' => $referral->id,
                            'type'        => $type,
                            'amount'      => $amount,
                            'role'        => 'referrer',
                        ]);

                        ReferralReward::create([
                            'user_id'     => $referral->referred_id,
                            'referral_id' => $referral->id,
                            'type'        => 'credit',
                            'amount'      => 0,
                            'role'        => 'referred',
                            'claimed_at'  => now(),
                        ]);
                    }
                }

                // Update existing referrer reward if amount/type changed
                if (isset($data['reward_amount'])) {
                    ReferralReward::where('referral_id', $referral->id)
                        ->where('role', 'referrer')
                        ->update(['amount' => $data['reward_amount']]);
                }

                if (isset($data['type'])) {
                    ReferralReward::where('referral_id', $referral->id)
                        ->where('role', 'referrer')
                        ->update(['type' => $data['type']]);
                }
            });

            $fresh = $referral->fresh();
            if (!$wasCompletedBefore && $fresh && $fresh->status === 'completed') {
                $this->notifyReferralCompleted($fresh);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Referral override failed: ' . $e->getMessage());
            return false;
        }
    }

    public function getAdminStats(): array
    {
        $total     = Referral::count();
        $pending   = Referral::pending()->count();
        $completed = Referral::completed()->count();

        $totalRewards = ReferralReward::where('role', 'referrer')->sum('amount');
        $cashRewards  = ReferralReward::where('role', 'referrer')->where('type', 'cash')->sum('amount');
        $creditRewards = ReferralReward::where('role', 'referrer')->where('type', 'credit')->sum('amount');

        return compact('total', 'pending', 'completed', 'totalRewards', 'cashRewards', 'creditRewards');
    }

    public function getChartData(int $days = 30): array
    {
        $start = Carbon::now()->subDays($days);

        $data = Referral::where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total,
                SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels'    => $data->pluck('date'),
            'total'     => $data->pluck('total'),
            'completed' => $data->pluck('completed'),
        ];
    }

    public function getStudentStats(int $studentId): array
    {
        // Auto-generate code if this student doesn't have one yet
        // (covers users who registered before the referral system existed)
        $referralCode = ReferralCode::where('user_id', $studentId)->first()
            ?? $this->generateCodeForStudent($studentId);

        $invited   = Referral::where('referrer_id', $studentId)->count();
        $completed = Referral::where('referrer_id', $studentId)->completed()->count();
        $pending   = Referral::where('referrer_id', $studentId)->pending()->count();

        $earnedRewards = ReferralReward::where('user_id', $studentId)
            ->where('role', 'referrer')
            ->whereNotNull('claimed_at')
            ->sum('amount');

        $pendingRewards = ReferralReward::where('user_id', $studentId)
            ->where('role', 'referrer')
            ->whereNull('claimed_at')
            ->sum('amount');

        $rewardType = $this->getRewardPreference($studentId);

        return [
            'code'           => $referralCode?->code,
            'invited'        => $invited,
            'completed'      => $completed,
            'pending'        => $pending,
            'earnedRewards'  => $earnedRewards,
            'pendingRewards' => $pendingRewards,
            'rewardType'     => $rewardType,
        ];
    }

    public function getStudentHistory(int $studentId)
    {
        return Referral::where('referrer_id', $studentId)
            ->with(['referred:id,first_name,last_name,email', 'rewards'])
            ->orderByDesc('created_at')
            ->paginate(15);
    }

    /**
     * Total unspent credit balance from claimed referrer rewards of type credit.
     */
    public function getAvailableCreditForStudent(int $studentId): float
    {
        return (float) ReferralReward::where('user_id', $studentId)
            ->where('role', 'referrer')
            ->where('type', 'credit')
            ->whereNotNull('claimed_at')
            ->whereNull('spent_at')
            ->sum('amount');
    }

    /**
     * Consume up to $maxAmount of available credit for an order.
     * Marks rewards as spent (oldest first) until the requested amount is covered.
     * Returns the actual amount consumed.
     */
    public function consumeCreditForOrder(int $studentId, int $orderId, float $maxAmount): float
    {
        if ($maxAmount <= 0) {
            return 0.0;
        }

        $rewards = ReferralReward::where('user_id', $studentId)
            ->where('role', 'referrer')
            ->where('type', 'credit')
            ->whereNotNull('claimed_at')
            ->whereNull('spent_at')
            ->orderBy('claimed_at')
            ->get();

        $remaining = round($maxAmount, 2);
        $consumed = 0.0;

        foreach ($rewards as $reward) {
            if ($remaining <= 0) {
                break;
            }

            $amount = (float) $reward->amount;
            if ($amount <= $remaining + 0.0001) {
                $reward->update([
                    'spent_at'       => now(),
                    'spent_order_id' => $orderId,
                ]);
                $consumed += $amount;
                $remaining = round($remaining - $amount, 2);
                continue;
            }

            // Partial spend: split the reward into a spent slice and a remaining slice.
            $reward->update([
                'amount'         => round($amount - $remaining, 2),
            ]);

            ReferralReward::create([
                'user_id'        => $reward->user_id,
                'referral_id'    => $reward->referral_id,
                'type'           => 'credit',
                'amount'         => $remaining,
                'role'           => 'referrer',
                'claimed_at'     => $reward->claimed_at,
                'spent_at'       => now(),
                'spent_order_id' => $orderId,
            ]);

            $consumed += $remaining;
            $remaining = 0;
        }

        return round($consumed, 2);
    }

    /**
     * Check if a referred student is eligible for a 5% first-purchase discount.
     */
    public function getDiscountForStudent(int $studentId): float
    {
        $referral = Referral::where('referred_id', $studentId)
            ->where('status', 'pending')
            ->first();

        if (!$referral) {
            return 0;
        }

        // Only for first purchase
        $hasPreviousOrder = Order::where('student_id', $studentId)
            ->where('payment_success', true)
            ->exists();

        return $hasPreviousOrder ? 0 : self::REFERRED_DISCOUNT_PERCENT;
    }

    /**
     * Notify the referrer that someone signed up using their link.
     * Creates an in-app notification and sends an email.
     */
    protected function notifyReferrerOfSignup(Referral $referral): void
    {
        try {
            $referrer = Student::find($referral->referrer_id);
            $referred = Student::find($referral->referred_id);
            if (!$referrer) {
                return;
            }

            $referredName = trim(($referred->first_name ?? '') . ' ' . ($referred->last_name ?? ''));
            if ($referredName === '') {
                $referredName = $referred->email ?? 'A new friend';
            }

            $referralsUrl = rtrim(config('app.user_url', env('USER_URL', '')), '/') . '/student-dashboard/referrals';
            $title = 'A friend joined with your referral link';
            $message = "{$referredName} just signed up. You'll earn \$" . number_format(self::REFERRER_REWARD_AMOUNT, 2)
                . ' once they complete their first purchase.';

            Notification::notifyStudent(
                $referrer->id,
                Notification::TYPE_REFERRAL,
                $title,
                $message,
                $referralsUrl,
                [
                    'referral_id'  => $referral->id,
                    'referred_id'  => $referred->id ?? null,
                    'event'        => 'signup',
                ],
                '👥',
                'blue',
                false
            );

            if (!empty($referrer->email)) {
                try {
                    Mail::to($referrer->email)->send(new ReferralNotificationEmail(
                        recipientName: trim(($referrer->first_name ?? '') . ' ' . ($referrer->last_name ?? '')) ?: 'there',
                        emailSubject: $title,
                        heading: 'Your referral just signed up!',
                        body: "{$referredName} joined Upskills Academy using your referral link.\n\nYou'll earn \$" . number_format(self::REFERRER_REWARD_AMOUNT, 2) . ' as soon as they complete their first purchase.',
                        ctaUrl: $referralsUrl,
                        ctaLabel: 'View my referrals',
                    ));

                    OutboundEmailLogger::logSent(
                        $referrer->email,
                        'referral_signup',
                        $title,
                        $referrer->id,
                        trim(($referrer->first_name ?? '') . ' ' . ($referrer->last_name ?? '')),
                        'Referral',
                        $referral->id
                    );
                } catch (\Exception $e) {
                    OutboundEmailLogger::logFailed(
                        $referrer->email,
                        'referral_signup',
                        $title,
                        $e->getMessage(),
                        $referrer->id,
                        trim(($referrer->first_name ?? '') . ' ' . ($referrer->last_name ?? '')),
                        'Referral',
                        $referral->id
                    );
                }
            }
        } catch (\Exception $e) {
            Log::error('Referral signup notification failed: ' . $e->getMessage());
        }
    }

    /**
     * Notify both parties when a referral completes (reward earned + discount used).
     */
    protected function notifyReferralCompleted(?Referral $referral): void
    {
        if (!$referral) {
            return;
        }

        try {
            $referrer = Student::find($referral->referrer_id);
            $referred = Student::find($referral->referred_id);

            $referrerReward = ReferralReward::where('referral_id', $referral->id)
                ->where('role', 'referrer')
                ->first();

            $amount = $referrerReward->amount ?? self::REFERRER_REWARD_AMOUNT;
            $type = $referrerReward->type ?? 'credit';
            $referralsUrl = rtrim(config('app.user_url', env('USER_URL', '')), '/') . '/student-dashboard/referrals';

            if ($referrer) {
                $referredName = trim(($referred->first_name ?? '') . ' ' . ($referred->last_name ?? ''));
                if ($referredName === '') {
                    $referredName = $referred->email ?? 'Your friend';
                }

                $typeLabel = $type === 'cash' ? 'cash reward' : 'credit (auto-applied at checkout)';
                $title = "You earned a \${$amount} referral reward!";
                $message = "{$referredName} completed their first purchase. Your \${$amount} {$typeLabel} is ready.";

                Notification::notifyStudent(
                    $referrer->id,
                    Notification::TYPE_REFERRAL,
                    $title,
                    $message,
                    $referralsUrl,
                    [
                        'referral_id' => $referral->id,
                        'reward_id'   => $referrerReward->id ?? null,
                        'amount'      => (float) $amount,
                        'type'        => $type,
                        'event'       => 'reward_earned',
                    ],
                    '🎉',
                    'green',
                    true
                );

                if (!empty($referrer->email)) {
                    try {
                        Mail::to($referrer->email)->send(new ReferralNotificationEmail(
                            recipientName: trim(($referrer->first_name ?? '') . ' ' . ($referrer->last_name ?? '')) ?: 'there',
                            emailSubject: $title,
                            heading: 'Your referral reward is ready!',
                            body: "{$referredName} just completed their first purchase using your referral.\n\nYou've earned a \${$amount} {$typeLabel}. Head to your referrals dashboard to view or claim it.",
                            ctaUrl: $referralsUrl,
                            ctaLabel: 'View my reward',
                        ));

                        OutboundEmailLogger::logSent(
                            $referrer->email,
                            'referral_reward_earned',
                            $title,
                            $referrer->id,
                            trim(($referrer->first_name ?? '') . ' ' . ($referrer->last_name ?? '')),
                            'Referral',
                            $referral->id
                        );
                    } catch (\Exception $e) {
                        OutboundEmailLogger::logFailed(
                            $referrer->email,
                            'referral_reward_earned',
                            $title,
                            $e->getMessage(),
                            $referrer->id,
                            trim(($referrer->first_name ?? '') . ' ' . ($referrer->last_name ?? '')),
                            'Referral',
                            $referral->id
                        );
                    }
                }
            }

            if ($referred && !empty($referred->email)) {
                $referredTitle = 'Your referral discount was applied';
                $referredMessage = 'Thanks for using a referral link — your 5% first-order discount was applied to your purchase.';

                Notification::notifyStudent(
                    $referred->id,
                    Notification::TYPE_REFERRAL,
                    $referredTitle,
                    $referredMessage,
                    $referralsUrl,
                    [
                        'referral_id' => $referral->id,
                        'event'       => 'discount_applied',
                    ],
                    '✅',
                    'green',
                    false
                );

                try {
                    Mail::to($referred->email)->send(new ReferralNotificationEmail(
                        recipientName: trim(($referred->first_name ?? '') . ' ' . ($referred->last_name ?? '')) ?: 'there',
                        emailSubject: $referredTitle,
                        heading: 'Welcome — your discount is in!',
                        body: "Thanks for joining Upskills Academy through a referral.\n\nYour 5% discount was automatically applied to your first order. You can also invite friends from your dashboard and earn rewards.",
                        ctaUrl: $referralsUrl,
                        ctaLabel: 'Invite friends',
                    ));

                    OutboundEmailLogger::logSent(
                        $referred->email,
                        'referral_discount_applied',
                        $referredTitle,
                        $referred->id,
                        trim(($referred->first_name ?? '') . ' ' . ($referred->last_name ?? '')),
                        'Referral',
                        $referral->id
                    );
                } catch (\Exception $e) {
                    OutboundEmailLogger::logFailed(
                        $referred->email,
                        'referral_discount_applied',
                        $referredTitle,
                        $e->getMessage(),
                        $referred->id,
                        trim(($referred->first_name ?? '') . ' ' . ($referred->last_name ?? '')),
                        'Referral',
                        $referral->id
                    );
                }
            }
        } catch (\Exception $e) {
            Log::error('Referral completion notification failed: ' . $e->getMessage());
        }
    }
}
