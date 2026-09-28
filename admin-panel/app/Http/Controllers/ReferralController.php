<?php

namespace App\Http\Controllers;

use App\Models\Referral;
use App\Models\ReferralReward;
use App\Services\ReferralService;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function __construct(protected ReferralService $referralService) {}

    public function index(Request $request)
    {
        $query = Referral::with([
            'referrer:id,first_name,last_name,email',
            'referred:id,first_name,last_name,email',
            'rewards',
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        if ($request->filled('reward_type')) {
            $query->whereHas('rewards', fn($q) => $q->where('type', $request->reward_type)->where('role', 'referrer'));
        }

        $referrals  = $query->orderByDesc('created_at')->paginate(20)->withQueryString();
        $stats      = $this->referralService->getAdminStats();
        $chartData  = $this->referralService->getChartData(30);

        return view('referrals.index', compact('referrals', 'stats', 'chartData'));
    }

    public function override(Request $request, int $id)
    {
        $validated = $request->validate([
            'status'        => 'nullable|in:pending,completed',
            'reward_amount' => 'nullable|numeric|min:0',
            'type'          => 'nullable|in:cash,credit',
        ]);

        $success = $this->referralService->overrideReward($id, $validated);

        return back()->with($success ? 'success' : 'error', $success ? 'Referral updated.' : 'Update failed.');
    }

    public function destroy(int $id)
    {
        Referral::findOrFail($id)->delete();

        return back()->with('success', 'Referral deleted.');
    }
}
