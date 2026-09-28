<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReferralService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReferralApiController extends Controller
{
    public function __construct(protected ReferralService $referralService) {}

    /**
     * Auto-generate a referral code for a newly registered student.
     */
    public function generateCode(Request $request): JsonResponse
    {
        $request->validate(['student_id' => 'required|integer|exists:students,id']);

        $code = $this->referralService->generateCodeForStudent($request->student_id);

        return response()->json(['code' => $code->code]);
    }

    /**
     * Register a referral when a student signs up with a referral code.
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'student_id'    => 'required|integer|exists:students,id',
            'referral_code' => 'required|string',
        ]);

        $referral = $this->referralService->registerReferral(
            $request->student_id,
            $request->referral_code
        );

        if (!$referral) {
            return response()->json(['message' => 'Invalid or already-used referral code.'], 422);
        }

        return response()->json(['message' => 'Referral registered.', 'referral_id' => $referral->id]);
    }

    /**
     * Process reward after first purchase. Called by user-side CheckoutService.
     */
    public function processReward(Request $request): JsonResponse
    {
        $request->validate([
            'student_id' => 'required|integer|exists:students,id',
            'order_id'   => 'required|integer',
        ]);

        $processed = $this->referralService->processFirstPurchaseReward(
            $request->student_id,
            $request->order_id
        );

        return response()->json([
            'processed' => $processed,
            'message'   => $processed ? 'Referral reward issued.' : 'No eligible referral found.',
        ]);
    }

    /**
     * Get student's own referral stats (user dashboard).
     */
    public function studentStats(int $studentId): JsonResponse
    {
        return response()->json($this->referralService->getStudentStats($studentId));
    }

    /**
     * Get student's referral history (user dashboard).
     */
    public function studentHistory(int $studentId): JsonResponse
    {
        $history = $this->referralService->getStudentHistory($studentId);

        return response()->json($history);
    }

    /**
     * Set reward type preference (cash or credit) for a student.
     */
    public function setPreference(Request $request): JsonResponse
    {
        $request->validate([
            'student_id' => 'required|integer|exists:students,id',
            'type'       => 'required|in:cash,credit',
        ]);

        $this->referralService->setRewardPreference($request->student_id, $request->type);

        return response()->json(['message' => 'Preference saved.']);
    }

    /**
     * Claim a specific reward.
     */
    public function claimReward(Request $request): JsonResponse
    {
        $request->validate([
            'reward_id'  => 'required|integer',
            'student_id' => 'required|integer|exists:students,id',
        ]);

        $claimed = $this->referralService->claimReward($request->reward_id, $request->student_id);

        return response()->json([
            'claimed' => $claimed,
            'message' => $claimed ? 'Reward claimed.' : 'Reward not found or already claimed.',
        ]);
    }

    /**
     * Check if a student qualifies for a first-purchase discount (5%).
     */
    public function checkDiscount(int $studentId): JsonResponse
    {
        $discount = $this->referralService->getDiscountForStudent($studentId);

        return response()->json(['discount_percent' => $discount]);
    }

    /**
     * Get a student's available referral credit balance.
     */
    public function creditBalance(int $studentId): JsonResponse
    {
        return response()->json([
            'balance' => $this->referralService->getAvailableCreditForStudent($studentId),
        ]);
    }

    /**
     * Consume referral credit toward an order.
     */
    public function consumeCredit(Request $request): JsonResponse
    {
        $request->validate([
            'student_id' => 'required|integer|exists:students,id',
            'order_id'   => 'required|integer',
            'amount'     => 'required|numeric|min:0',
        ]);

        $consumed = $this->referralService->consumeCreditForOrder(
            (int) $request->student_id,
            (int) $request->order_id,
            (float) $request->amount,
        );

        return response()->json(['consumed' => $consumed]);
    }
}
