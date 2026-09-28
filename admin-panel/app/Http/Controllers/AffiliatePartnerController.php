<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreAffiliatePartnerRequest;
use App\Http\Requests\UpdateAffiliatePartnerRequest;
use App\Http\Requests\ProcessPayoutRequest;
use App\Models\AffiliatePartner;
use App\Services\AffiliatePartnerService;

class AffiliatePartnerController extends Controller
{
    protected $affiliatePartnerService;

    public function __construct(AffiliatePartnerService $affiliatePartnerService)
    {
        $this->affiliatePartnerService = $affiliatePartnerService;
    }
    /**
     * Display listing of affiliate partners with filtering
     */
    public function index(Request $request)
    {
        $partners = $this->affiliatePartnerService->getFilteredPartners($request);

        $totalCommissions = $this->affiliatePartnerService->getTotalCommissions();

        return view('admin.affiliate_partners.index', compact('partners', 'totalCommissions'));
    }

    /**
     * Show the form for creating a new affiliate partner
     */
    public function create()
    {
        return view('admin.affiliate_partners.create');
    }

    /**
     * Store a newly created affiliate partner
     */
    public function store(StoreAffiliatePartnerRequest $request)
    {
        try {
            $partner = $this->affiliatePartnerService->createPartner($request->validated());

            return redirect()->route('affiliate-partners.show', $partner)
                ->with('success', "Affiliate partner '{$partner->name}' created successfully! Referral code: {$partner->referral_code}");

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create partner: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified affiliate partner with statistics
     */
    public function show(AffiliatePartner $affiliatePartner)
    {
        $data = $this->affiliatePartnerService->getPartnerWithStats($affiliatePartner);

        return view('admin.affiliate_partners.show', $data);
    }

    /**
     * Show the form for editing the specified affiliate partner
     */
    public function edit(AffiliatePartner $affiliatePartner)
    {
        return view('admin.affiliate_partners.edit', compact('affiliatePartner'))->with('partner', $affiliatePartner);
    }

    /**
     * Update the specified affiliate partner
     */
    public function update(UpdateAffiliatePartnerRequest $request, AffiliatePartner $affiliatePartner)
    {
        try {
            $updatedPartner = $this->affiliatePartnerService->updatePartner($affiliatePartner, $request->validated());

            return redirect()->route('affiliate-partners.show', $updatedPartner)
                ->with('success', "Affiliate partner '{$updatedPartner->name}' updated successfully!");

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update partner: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified affiliate partner
     */
    public function destroy(AffiliatePartner $affiliatePartner)
    {
        try {
            $name = $affiliatePartner->name;
            $this->affiliatePartnerService->deletePartner($affiliatePartner);

            return redirect()->route('affiliate-partners.index')
                ->with('success', "Affiliate partner '{$name}' deleted successfully!");

        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete partner: ' . $e->getMessage()]);
        }
    }

    /**
     * Approve a pending affiliate partner
     */
    public function approve(AffiliatePartner $affiliatePartner)
    {
        try {
            $this->affiliatePartnerService->approvePartner($affiliatePartner);

            return redirect()->back()
                ->with('success', "Partner '{$affiliatePartner->name}' approved successfully!");
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Suspend an affiliate partner
     */
    public function suspend(AffiliatePartner $affiliatePartner)
    {
        try {
            $this->affiliatePartnerService->suspendPartner($affiliatePartner);

            return redirect()->back()
                ->with('success', "Partner '{$affiliatePartner->name}' suspended and all coupons deactivated!");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to suspend partner: ' . $e->getMessage()]);
        }
    }

    /**
     * Reactivate a suspended affiliate partner
     */
    public function reactivate(AffiliatePartner $affiliatePartner)
    {
        try {
            $this->affiliatePartnerService->reactivatePartner($affiliatePartner);

            return redirect()->back()
                ->with('success', "Partner '{$affiliatePartner->name}' reactivated successfully!");
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * View affiliate partners performance report
     */
    public function report(Request $request)
    {
        $data = $this->affiliatePartnerService->getGeneralReport($request);

        return view('admin.affiliate_partners.report', $data);
    }

    /**
     * Process payout for affiliate partner
     */
    public function processPayout(AffiliatePartner $affiliatePartner, ProcessPayoutRequest $request)
    {
        try {
            $this->affiliatePartnerService->processPayout($affiliatePartner, $request->validated());

            return redirect()->route('affiliate-partners.show', $affiliatePartner)
                ->with('success', "Payout of \${$request->validated()['amount']} processed successfully!");

        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['amount' => $e->getMessage()])->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to process payout: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Export partner data
     */
    public function export(AffiliatePartner $affiliatePartner)
    {
        return $this->affiliatePartnerService->streamPartnerExport($affiliatePartner);
    }
}
