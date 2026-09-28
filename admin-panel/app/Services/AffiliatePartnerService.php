<?php

namespace App\Services;

use App\Models\AffiliatePartner;
use App\Models\PartnerCommission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AffiliatePartnerService
{
    /**
     * Get total commissions amount for dashboard statistics
     */
    public function getTotalCommissions(): float
    {
        return (float) PartnerCommission::sum('commission_amount');
    }

    /**
     * Get filtered affiliate partners with statistics
     */
    public function getFilteredPartners(Request $request)
    {
        $query = AffiliatePartner::withCount(['coupons', 'commissions'])
            ->with(['commissions' => function($query) {
                $query->select('partner_id', 'commission_amount', 'status');
            }]);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('referral_code', 'like', "%{$request->search}%");
            });
        }

        $partners = $query->orderBy('created_at', 'desc')->paginate(15);

        // Calculate total_earned for each partner
        $partners->getCollection()->transform(function ($partner) {
            $partner->total_earned = $partner->commissions->sum('commission_amount');
            return $partner;
        });

        return $partners;
    }

    /**
     * Create a new affiliate partner
     */
    public function createPartner(array $data): AffiliatePartner
    {
        try {
            DB::beginTransaction();

            // Generate unique referral code
            $referralCode = $this->generateUniqueReferralCode($data['name']);

            // Create partner instance
            $partner = new AffiliatePartner();
            $partner->name = $data['name'];
            $partner->email = $data['email'];
            $partner->phone = $data['phone'] ?? null;
            $partner->company_name = $data['company'] ?? null;
            $partner->website = $data['website'] ?? null;
            $partner->commission_rate = $data['commission_rate'];
            $partner->payment_details = $data['payment_details'] ?? null;
            $partner->payment_threshold = $data['payout_threshold'] ?? 0.00;
            $partner->social_links = $data['social_media'] ?? null;
            $partner->admin_notes = $data['notes'] ?? null;
            $partner->referral_code = $referralCode;

            // Set default values
            $partner->status = 'pending';
            $partner->is_active = false;
            $partner->total_earnings = 0;
            $partner->total_paid = 0;
            $partner->pending_earnings = 0;
            $partner->total_referrals = 0;
            $partner->successful_conversions = 0;
            $partner->conversion_rate = 0;
            $partner->total_revenue_generated = 0;
            $partner->partnership_start_date = now();
            $partner->approved_at = null;
            $partner->approved_by = null;

            $saved = $partner->save();

            if (!$saved || !$partner->id) {
                throw new \RuntimeException('Saving affiliate partner failed (no ID returned).');
            }

            DB::commit();

            Log::info('Affiliate partner created successfully', [
                'partner_id' => $partner->id,
                'name' => $partner->name,
                'referral_code' => $referralCode
            ]);

            return $partner;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create affiliate partner', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Update an existing affiliate partner
     */
    public function updatePartner(AffiliatePartner $partner, array $data): AffiliatePartner
    {
        try {
            $partner->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'company_name' => $data['company'] ?? null,
                'website' => $data['website'] ?? null,
                'commission_rate' => $data['commission_rate'],
                'payment_details' => $data['payment_details'] ?? null,
                'payment_threshold' => $data['payout_threshold'] ?? 0.00,
                'social_links' => $data['social_media'] ?? null,
                'admin_notes' => $data['notes'] ?? null,
            ]);

            Log::info('Affiliate partner updated successfully', [
                'partner_id' => $partner->id,
                'name' => $partner->name
            ]);

            return $partner;

        } catch (\Exception $e) {
            Log::error('Failed to update affiliate partner', [
                'partner_id' => $partner->id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Delete an affiliate partner with validation
     */
    public function deletePartner(AffiliatePartner $partner): void
    {
        // Check if partner has active coupons
        $activeCoupons = $partner->coupons()->where('is_active', true)->count();
        if ($activeCoupons > 0) {
            throw new \InvalidArgumentException(
                "Cannot delete partner '{$partner->name}' as they have {$activeCoupons} active coupon(s)."
            );
        }

        // Check if partner has unpaid commissions
        $unpaidCommissions = $partner->commissions()
            ->whereIn('status', ['pending', 'approved'])
            ->sum('commission_amount');

        if ($unpaidCommissions > 0) {
            throw new \InvalidArgumentException(
                "Cannot delete partner '{$partner->name}' as they have unpaid commissions totaling \${$unpaidCommissions}."
            );
        }

        try {
            $partnerName = $partner->name;
            $partner->delete();

            Log::info('Affiliate partner deleted successfully', [
                'partner_name' => $partnerName
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to delete affiliate partner', [
                'partner_id' => $partner->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get partner with detailed statistics
     */
    public function getPartnerWithStats(AffiliatePartner $partner): array
    {
        $partner->load([
            'coupons',
            'commissions' => function($query) {
                $query->with(['couponUsage.coupon'])->latest()->limit(20);
            }
        ]);

        // Calculate statistics from actual commission records
        $totalCommissionEarned = $partner->commissions()->sum('commission_amount');
        $totalCommissionPaid = $partner->commissions()->where('status', 'paid')->sum('commission_amount');
        $pendingCommission = $partner->commissions()->where('status', 'pending')->sum('commission_amount');
        $approvedCommission = $partner->commissions()->where('status', 'approved')->sum('commission_amount');

        $stats = [
            'total_coupons' => $partner->coupons()->count(),
            'active_coupons' => $partner->coupons()->where('is_active', true)->count(),
            'total_uses' => $partner->coupons()->sum('usage_count'),
            'total_revenue' => $partner->commissions()->sum('order_total'),
            'total_commission_earned' => $totalCommissionEarned,
            'total_commission_paid' => $totalCommissionPaid,
            'pending_commission' => $pendingCommission,
            'approved_commission' => $approvedCommission,
            'monthly_performance' => []
        ];

        return [
            'affiliatePartner' => $partner,
            'stats' => $stats,
            'partner' => $partner
        ];
    }

    /**
     * Approve a pending affiliate partner
     */
    public function approvePartner(AffiliatePartner $partner): AffiliatePartner
    {
        if ($partner->status !== 'pending') {
            throw new \InvalidArgumentException('Only pending partners can be approved.');
        }

        $partner->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => Carbon::now()
        ]);

        Log::info('Affiliate partner approved', [
            'partner_id' => $partner->id,
            'partner_name' => $partner->name,
            'approved_by' => Auth::id()
        ]);

        return $partner;
    }

    /**
     * Suspend an affiliate partner
     */
    public function suspendPartner(AffiliatePartner $partner): AffiliatePartner
    {
        $partner->update([
            'status' => 'suspended',
            'suspended_by' => Auth::id(),
            'suspended_at' => Carbon::now()
        ]);

        // Deactivate all partner's coupons
        $partner->coupons()->update(['is_active' => false]);

        Log::info('Affiliate partner suspended', [
            'partner_id' => $partner->id,
            'partner_name' => $partner->name,
            'suspended_by' => Auth::id()
        ]);

        return $partner;
    }

    /**
     * Reactivate a suspended affiliate partner
     */
    public function reactivatePartner(AffiliatePartner $partner): AffiliatePartner
    {
        if ($partner->status !== 'suspended') {
            throw new \InvalidArgumentException('Only suspended partners can be reactivated.');
        }

        $partner->update([
            'status' => 'approved',
            'suspended_by' => null,
            'suspended_at' => null
        ]);

        Log::info('Affiliate partner reactivated', [
            'partner_id' => $partner->id,
            'partner_name' => $partner->name,
            'reactivated_by' => Auth::id()
        ]);

        return $partner;
    }

    /**
     * Get partner performance report data
     */
    public function getPartnerReport(AffiliatePartner $partner, int $months = 12): array
    {
        // Calculate basic performance metrics
        $performance = [
            'total_commissions' => $partner->commissions()->count(),
            'total_earnings' => $partner->commissions()->sum('commission_amount'),
            'total_paid' => $partner->commissions()->where('status', 'paid')->sum('commission_amount'),
            'pending_amount' => $partner->commissions()->where('status', 'pending')->sum('commission_amount'),
            'approved_amount' => $partner->commissions()->where('status', 'approved')->sum('commission_amount'),
            'average_commission' => $partner->commissions()->avg('commission_amount') ?? 0,
            'conversion_rate' => $partner->conversion_rate ?? 0,
            'total_referrals' => $partner->total_referrals ?? 0
        ];

        $topCoupons = $partner->coupons()
            ->orderByDesc('usage_count')
            ->limit(10)
            ->get();

        return [
            'affiliatePartner' => $partner,
            'performance' => $performance,
            'topCoupons' => $topCoupons,
            'months' => $months,
            'partner' => $partner
        ];
    }

    /**
     * Get general affiliate partners report data
     */
    public function getGeneralReport(Request $request): array
    {
        $startDate = $request->get('start_date', now()->subMonths(3)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $partnerId = $request->get('partner_id');

        // Get all partners for dropdown
        $partners = AffiliatePartner::orderBy('name')->get();

        // Filter partners based on request
        $query = AffiliatePartner::with(['commissions', 'coupons']);

        if ($partnerId) {
            $query->where('id', $partnerId);
        }

        $filteredPartners = $query->get();

        // Calculate overall statistics - use date range with time
        $baseQuery = PartnerCommission::whereBetween('created_at', [
            $startDate . ' 00:00:00',
            $endDate . ' 23:59:59'
        ]);

        if ($partnerId) {
            $baseQuery->where('partner_id', $partnerId);
        }

        // Calculate each statistic separately to avoid query reuse issues
        $totalCommissions = clone $baseQuery;
        $totalEarnings = clone $baseQuery;
        $totalPaid = clone $baseQuery;
        $pendingAmount = clone $baseQuery;
        $approvedAmount = clone $baseQuery;

        $overallStats = [
            'total_partners' => $partners->count(),
            'active_partners' => $partners->where('status', 'approved')->count(),
            'total_commissions' => $totalCommissions->count(),
            'total_earnings' => $totalEarnings->sum('commission_amount'),
            'total_paid' => $totalPaid->where('status', 'paid')->sum('commission_amount'),
            'pending_amount' => $pendingAmount->where('status', 'pending')->sum('commission_amount'),
            'approved_amount' => $approvedAmount->where('status', 'approved')->sum('commission_amount'),
        ];

        // Calculate performance for each partner
        $partnerPerformance = [];
        foreach ($filteredPartners as $partner) {
            $partnerCommissions = $partner->commissions()
                ->whereBetween('created_at', [
                    $startDate . ' 00:00:00',
                    $endDate . ' 23:59:59'
                ]);

            // Calculate revenue generated (sum of order totals)
            $revenueGenerated = $partnerCommissions->sum('order_total');

            // Calculate total coupon uses
            $totalUses = $partner->coupons()->sum('usage_count');

            // Calculate conversion rate (commissions / total coupon uses)
            $conversionRate = $totalUses > 0 ? ($partnerCommissions->count() / $totalUses) * 100 : 0;

            $partnerPerformance[] = [
                'partner' => $partner,
                'coupons_count' => $partner->coupons()->count(),
                'total_uses' => $totalUses,
                'revenue_generated' => $revenueGenerated,
                'commission_earned' => $partnerCommissions->sum('commission_amount'),
                'conversion_rate' => round($conversionRate, 2),
                'total_commissions' => $partnerCommissions->count(),
                'total_earnings' => $partnerCommissions->sum('commission_amount'),
                'pending_amount' => $partnerCommissions->where('status', 'pending')->sum('commission_amount'),
                'paid_amount' => $partnerCommissions->where('status', 'paid')->sum('commission_amount'),
                'top_coupons' => $partner->coupons()->orderByDesc('usage_count')->limit(5)->get(),
            ];
        }

        // Get top performing coupons with their revenue and commission data
        $topCoupons = \App\Models\Coupon::with(['affiliatePartner', 'usageLogs'])
            ->whereHas('usageLogs', function($query) use ($startDate, $endDate) {
                $query->whereBetween('used_at', [$startDate, $endDate]);
            })
            ->withCount(['usageLogs' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('used_at', [$startDate, $endDate]);
            }])
            ->orderBy('usage_logs_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function($coupon) use ($startDate, $endDate) {
                // Calculate revenue and commission for this coupon
                $commissions = PartnerCommission::whereHas('couponUsage', function($query) use ($coupon) {
                    $query->where('coupon_id', $coupon->id);
                })->whereBetween('created_at', [$startDate, $endDate])->get();

                $coupon->total_revenue = $commissions->sum('order_total');
                $coupon->total_commission = $commissions->sum('commission_amount');

                // If no commission records, calculate from usage logs
                if ($coupon->total_revenue == 0) {
                    $coupon->total_revenue = $coupon->usageLogs()
                        ->whereBetween('used_at', [$startDate, $endDate])
                        ->sum('original_price');
                }

                return $coupon;
            });

        return [
            'partners' => $partners,
            'filteredPartners' => $filteredPartners,
            'partnerPerformance' => $partnerPerformance,
            'overallStats' => $overallStats,
            'topCoupons' => $topCoupons,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'selectedPartnerId' => $partnerId,
        ];
    }

    /**
     * Process payout for affiliate partner
     */
    public function processPayout(AffiliatePartner $partner, array $data): AffiliatePartner
    {
        try {
            DB::beginTransaction();

            // Get approved commissions
            $approvedCommissions = $partner->commissions()
                ->where('status', 'approved')
                ->get();

            $totalApproved = $approvedCommissions->sum('commission_amount');

            if ($data['amount'] > $totalApproved) {
                throw new \InvalidArgumentException(
                    "Payout amount cannot exceed approved commission total of \${$totalApproved}"
                );
            }

            // Mark commissions as paid
            $remaining = $data['amount'];
            foreach ($approvedCommissions as $commission) {
                if ($remaining <= 0) break;

                $payAmount = min($commission->commission_amount, $remaining);

                $commission->update([
                    'status' => 'paid',
                    'paid_amount' => $payAmount,
                    'paid_at' => Carbon::now(),
                    'paid_by' => Auth::id(),
                    'payment_method' => $data['payment_method'],
                    'payment_reference' => $data['reference_number'] ?? null,
                    'payment_notes' => $data['notes'] ?? null
                ]);

                $remaining -= $payAmount;
            }

            // Update partner totals
            $partner->total_paid += $data['amount'];
            $partner->save();

            DB::commit();

            Log::info('Payout processed successfully', [
                'partner_id' => $partner->id,
                'partner_name' => $partner->name,
                'amount' => $data['amount'],
                'processed_by' => Auth::id()
            ]);

            return $partner;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to process payout', [
                'partner_id' => $partner->id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Generate export data for partner
     */
    public function generateExportData(AffiliatePartner $partner): array
    {
        $commissions = $partner->commissions()
            ->with(['usageLog', 'order'])
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'partner' => $partner,
            'commissions' => $commissions,
            'filename' => 'partner_' . $partner->referral_code . '_' . Carbon::now()->format('Y-m-d') . '.csv'
        ];
    }

    /**
     * Stream partner export as CSV response
     */
    public function streamPartnerExport(AffiliatePartner $partner)
    {
        $exportData = $this->generateExportData($partner);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$exportData['filename']}\"",
        ];

        $callback = function () use ($exportData) {
            $file = fopen('php://output', 'w');
            $partnerData = $exportData['partner'];
            $commissions = $exportData['commissions'];

            fputcsv($file, ['Partner Report']);
            fputcsv($file, ['Name', $partnerData->name]);
            fputcsv($file, ['Code', $partnerData->referral_code]);
            fputcsv($file, ['Total Commission Earned', '$' . number_format($partnerData->total_earnings ?? 0, 2)]);
            fputcsv($file, ['Total Commission Paid', '$' . number_format($partnerData->total_paid ?? 0, 2)]);
            fputcsv($file, []);

            fputcsv($file, [
                'Date', 'Order ID', 'Coupon Code', 'Order Amount',
                'Commission Rate', 'Commission Amount', 'Status', 'Paid Date'
            ]);

            foreach ($commissions as $commission) {
                fputcsv($file, [
                    $commission->created_at->format('Y-m-d H:i:s'),
                    $commission->order_id,
                    $commission->usageLog->coupon->code ?? 'N/A',
                    '$' . number_format($commission->order_total, 2),
                    $commission->commission_rate . '%',
                    '$' . number_format($commission->commission_amount, 2),
                    ucfirst($commission->status),
                    $commission->paid_at ? $commission->paid_at->format('Y-m-d') : '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Generate unique referral code
     */
    private function generateUniqueReferralCode(string $name): string
    {
        $referralCode = strtoupper(substr($name, 0, 3) . rand(1000, 9999));

        while (AffiliatePartner::where('referral_code', $referralCode)->exists()) {
            $referralCode = strtoupper(substr($name, 0, 3) . rand(1000, 9999));
        }

        return $referralCode;
    }
}
