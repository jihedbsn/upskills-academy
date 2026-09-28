<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReferralController extends Controller
{
    private string $adminUrl;

    public function __construct()
    {
        $this->adminUrl = env('ADMIN_URL', 'http://localhost:8100');
    }

    public function index()
    {
        $studentId = Auth::id();

        try {
            $statsRes   = Http::timeout(5)->get("{$this->adminUrl}/api/referrals/student/{$studentId}/stats");
            $historyRes = Http::timeout(5)->get("{$this->adminUrl}/api/referrals/student/{$studentId}/history");
            $creditRes  = Http::timeout(5)->get("{$this->adminUrl}/api/referrals/student/{$studentId}/credit-balance");

            $stats   = $statsRes->successful()   ? $statsRes->json()   : $this->defaultStats();
            $history = $historyRes->successful()  ? $historyRes->json() : ['data' => []];
            $stats['creditBalance'] = $creditRes->successful() ? (float) ($creditRes->json()['balance'] ?? 0) : 0.0;
        } catch (\Exception $e) {
            Log::error('Referral stats fetch failed: ' . $e->getMessage());
            $stats   = $this->defaultStats();
            $history = ['data' => []];
        }

        $referralLink = $stats['code']
            ? route('signup', ['locale' => app()->getLocale()]) . '?ref=' . $stats['code']
            : '';

        return view('student-dashboard.pages.referrals', compact('stats', 'history', 'referralLink'));
    }

    public function setPreference(Request $request)
    {
        $request->validate(['type' => 'required|in:cash,credit']);

        try {
            Http::timeout(5)->post("{$this->adminUrl}/api/referrals/set-preference", [
                'student_id' => Auth::id(),
                'type'       => $request->type,
            ]);
        } catch (\Exception $e) {
            Log::error('Referral preference set failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Reward preference updated.');
    }

    public function claimReward(Request $request)
    {
        $request->validate(['reward_id' => 'required|integer']);

        try {
            $res = Http::timeout(5)->post("{$this->adminUrl}/api/referrals/claim-reward", [
                'student_id' => Auth::id(),
                'reward_id'  => $request->reward_id,
            ]);

            $data = $res->json();
            return back()->with($data['claimed'] ? 'success' : 'error', $data['message'] ?? 'Error.');
        } catch (\Exception $e) {
            Log::error('Referral claim failed: ' . $e->getMessage());
            return back()->with('error', 'Could not process claim. Try again later.');
        }
    }

    private function defaultStats(): array
    {
        return [
            'code'           => null,
            'invited'        => 0,
            'completed'      => 0,
            'pending'        => 0,
            'earnedRewards'  => 0,
            'pendingRewards' => 0,
            'rewardType'     => 'credit',
            'creditBalance'  => 0.0,
        ];
    }
}
